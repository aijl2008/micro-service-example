let userSqlMap = {
    deleteById: 'delete from goods where id = ?',
    list: 'select * from goods',
    getById: 'select * from goods where id = ?'
};

module.exports = userSqlMap;