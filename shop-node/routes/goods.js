var express = require('express');
var router = express.Router();
var goodsDao = require('../dao/goodsDao');
var result = require('../model/result');

/* list goods */
router.get('/', function(req, res) {
    console.log('list goods called');
    goodsDao.list(function (goods) {
        res.json(result.createResult(true, goods));
    });
});

/* get goods */
router.get('/:id', function(req, res) {
    var id = req.params.id;
    console.log('get goods called, id: ' + id);
    goodsDao.getById(id, function (goods) {
        res.json(result.createResult(true, goods));
    });
});

/* delete goods */
router.delete('/:id', function (req, res) {
    var id = req.params.id;
    console.log('delete goods called, id=' + id);
    goodsDao.deleteById(id, function (success) {
        res.json(result.createResult(success, null));
    });
});

/* add goods */
router.post('/', function (req, res) {
    console.log('post goods called');
    var goods = req.body;
    console.log(goods);
    goodsDao.add(goods, function (success) {
        var r =  result.createResult(success, null);
        res.json(r);
    });
});

/* update goods */
router.put('/:id', function (req, res) {
    console.log('update goods called');
    var goods = req.body;
    goods.id = req.params.id;
    console.log(goods);
    goodsDao.update(goods, function (success) {
        var r =  result.createResult(success, null);
        res.json(r);
    });
});

/* patch goods */
router.patch('/:id', function (req, res) {
    console.log('patch goods called');
    goodsDao.getById(req.params.id, function (goods) {
        var username = req.body.username;
        if(username) {
            goods.username = username;
        }
        var password = req.body.password;
        if(password) {
            goods.password = password;
        }
        console.log(goods);
        goodsDao.update(goods, function (success) {
            var r =  result.createResult(success, null);
            res.json(r);
        });
    });
});

module.exports = router;
