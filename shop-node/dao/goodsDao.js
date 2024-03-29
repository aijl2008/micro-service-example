let mysql = require('mysql');
let mysqlConf = require('../conf/mysqlConf');
let userSqlMap = require('./userSqlMap');
let pool = mysql.createPool(mysqlConf.mysql);

module.exports = {
    add: function (goods, callback) {
        pool.query(userSqlMap.add, [user.username, user.password], function (error, result) {
            if (error) throw error;
            callback(result.affectedRows > 0);
        });
    },
    list: function (callback) {
        pool.query(userSqlMap.list, function (error, result) {
            if (error) throw error;
            callback(result);
        });
    },
    getById: function (id, callback) {
        pool.query(userSqlMap.getById, id, function (error, result) {
            if (error) throw error;
            console.log(result[0]);
            callback(result[0]);
        });
    },
    deleteById: function (id, callback) {
        pool.query(userSqlMap.deleteById, id, function (error, result) {
            if (error) throw error;
            callback(result.affectedRows > 0);
        });
    },
    update: function (goods, callback) {
        pool.query(userSqlMap.update, [user.username, user.password, user.id], function (error, result) {
            if (error) throw error;
            callback(result.affectedRows > 0);
        });
    }
};
