let express = require('express');
let goods = require('./routes/goods');
let app = express();
let consul = require('consul')();

app.get("/health.json", function (req, res) {
    console.log((new Date()).toLocaleTimeString() + ':/health.json');
    res.header("Content-type", "application/json");
    res.send('{"status":"UP"}')
});
app.use('/goods', goods);

app.use(function (req, res, next) {
    var err = new Error('Not Found');
    err.status = 404;
    res.json(err);
});

const server = app.listen(3000, '192.168.1.100', function () {
    /**
     * 注册到 consul
     */
    const host = server.address().address
    const port = server.address().port
    consul.agent.service.register({
        "name": "shop-node",
        "id": "shop-node@" + host + ":" + port,
        "address": host,
        "port": port,
        "check": {
            "http": "http://" + host + ":" + port + "/health.json",
            "interval": "15s"
        }
    }, function (err) {
        if (err) throw err;
    });

    console.log("应用实例，访问地址为 http://%s:%s", host, port)
})

app.listen(3000);
