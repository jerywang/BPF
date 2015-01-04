<?php
/**
 * Database Access Object
 * $Id Base.php 2014-12-31 13:13 王国行 wangguoxing@baidu.com $
 */
abstract class Dao_Base {

    /**
     * @brief  返回 当前dao对应的数据库表的名称
     * @return string - table name
     */
    abstract public function getTableName();

    /**
     * @brief  返回当前cluster的名称
     * @return string - pdo name
     */
    abstract public function getClusterName();

    /**
     * @brief  返回当前table主键
     * @return string primary key name
     */
    abstract public function getTablePK();

    /**
     * @var DB_Factory
     */
    private $dbfactory = null;

    /**
     * @brief  获取一个 pdo 实例
     * @param boolean $isMaster - true :master db connection false: slave db connection
     * @return Db_Pdo
     */
    protected function getPdo ($isMaster = false) {
        if (null == $this->dbfactory) {
            $this->dbfactory = DB_Factory::getInstance();
        }
        return $this->dbfactory->getPdo($this->getClusterName(),$isMaster);
    }

    /**
     * @brief 从数据库表中获取$limit行记录
     * @param $where - array eg: array('id'=>$id) = where id = $id;
     * @param $order - string or array eg: id desc or array("id desc","updated desc")
     * @param $limit - 返回的记录数
     * @param $offset - 开始位置
     * @return array - table row
     */
    public function select ($where = array(), $order = null, $limit = 100, $offset = 0) {
        $rt = $this->fetchAll($where,$order,$limit,$offset);
        return $rt;
    }

    /**
     * 查找指定条件的行数(包含去重复计数)
     * @param $where - @see find
     * @param $field: 去重字段
     * @return int - table rowsd
     */
    public function selectCount ($where = array(), $field = '') {
        $rt = $this->fetchCount($where, $field);
        return $rt;
    }

    protected function fetchCount ($where = array(), $field = '') {
        $pdo = $this->getPdo ();
        $_where = $this->build_where($where);
        if($field) {
            $sql = "SELECT COUNT(DISTINCT {$field}) AS total_rows FROM " . $this->getTableName();
        }else {
            $sql = "SELECT COUNT(*) AS total_rows FROM " . $this->getTableName();
        }
        $sql .= @$_where['where'];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($_where['params']);
        $rs = $stmt->fetch();

        return $rs['total_rows'];
    }

    /**
     * 更新数据库表
     * @param  $data - array eg: array('id'=>$id)
     * @param  $where - array @see
     * @return effect rows
     */
    public function update ($data = array(),$where = array(), $status = false) {
        $dataCount = count($data);
        if (empty($data) || empty($where)) {
            return false;
        }
        //防止误更新
        if (is_numeric($where)) {
            return false;
        }
        $sql = "UPDATE " . $this->getTableName() . " SET ";
        $i = 1;
        foreach ($data as $key => $value) {
            $v = self::escape_value($value);
            $sql .= $status ? "`{$key}`= $v ":"`{$key}` = '{$v}'";
            $sql .= ($i < $dataCount)?',':'';
            $i ++;
        }
        $_where = $this->build_where($where);
        $sql .= @$_where['where'];
        $pdo = $this->getPdo(true);
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($_where['params']);
        $count = $stmt->rowCount();

        return ($count == 0) ? $result : $count;
    }

    /**
     * 向数据库表中插入一行数据
     * @param  array $data - array eg:array('column'=>$colvalue)
     * @param  array $filter 需要插入DB的列名，有可能传递过来的数据会多于列名
     * @return insert id , if primary key not exists  return effect rows
     */
    public function insert($data, $filter = array()) {
        $_data = array();
        if (!empty($filter) && is_array($filter)) {
            foreach ($filter as $column) {
                $_data[$column] = $data[$column];
            }
        } else {
            $_data = $data;
        }
        if (empty($_data)) {
            return false;
        }
        $rs = $this->_insert($_data);

        return $rs;
    }

    /**
     * 批量插入多行数据
     * @param $data array eg:array(array('column'=>$colvalue1),array('column'=>$colvalue2))
     * @param bool $isIgnore
     * @return bool|string
     */
    public function batchInsert($data, $isIgnore = false){
        if (empty($data)){
            return false;
        }
        $_data = $data;
        $pdo = $this->getPdo(true);
        $param = array();
        $values = array();

        //过滤掉重复提交的报错
        $ignore = "";
        if ($isIgnore) {
            $ignore = "ignore";
        }
        $sql = "INSERT {$ignore} INTO ". $this->getTableName()." (`";
        $sql .= implode('`,`',array_keys(array_pop($data)))."`) VALUES ";
        foreach($_data as $arr){
            unset($values);
            foreach ($arr as $v) {
                $values[] = "?";
                $param[] = $v;
            }
            $sql .= "(";
            $sql .= implode(",",$values) . "),";
        }
        $sql = rtrim($sql,',');
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($param);
        $id = $pdo->lastInsertId();
        if ($id) {
            return $id;
        }
        $count = $stmt->rowCount();

        return ($count == 0) ? $result : $count;
    }

    /**
     * 对数据库表中数据进行删除
     * @param $where - array eg:array('column'=>$colvalue)
     * @return int $result -  effect rows
     */
    public function delete ($where) {
        $pdo = $this->getPdo(true);
        $_where = $this->build_where($where);
        $sql = "delete from " . $this->getTableName();
        $sql .= @$_where['where'];
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($_where['params']);
        $rs = $stmt->rowCount();

        return ($rs == 0) ? $result : $rs;
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql SQL语句
     * @param array $params SQL绑定参数
     * @param boolean $write 数据库主机选择
     * @return array|null|string
     */
    public function execute($sql, $params = array(), $write = false) {
        $pdo = $this->getPdo($write);
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute($params)) {
            return false;
        }
        $type = strtoupper(substr($sql, 0, 6));
        $result = null;
        switch ($type) {
            case 'INSERT':
                $result = $pdo->lastInsertId();
                break;

            case 'UPDATE':
                $result = $stmt->rowCount();
                break;

            case 'DELETE':
                $result = $stmt->rowCount();
                break;

            case 'SELECT':
                $result = $stmt->fetchAll();
                break;

            default:
                break;
        }

        return $result;
    }

    protected function build_where ($where) {
        $_where = array('where' => '', 'params' => array());
        if (empty($where)) {
            return $_where;
        }
        if (is_array($where) && count($where)) {
            foreach ($where as $key => $value) {
                if (preg_match("/\?/", $key)) {
                    $_where['field'][] = '(' . $key . ')';
                } else {
                    $_where['field'][] = '(`' . $key . '` = ?)';
                }
                $_where['params'][] = self::escape_value($value);
            }
            $_where['where']=' WHERE ' . implode(' AND ',$_where['field']);
        }else{
            $_where['where']=' WHERE ' . $where;
        }

        return $_where;
    }

    protected function fetchAll ($where = array(), $order = null, $limit = 100, $offset = 0) {
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM " . $this->getTableName();
        $_where = $this->build_where($where);
        $sql .= @$_where['where'];
        if (is_array($order) && count($order)) {
            $sql .= ' ORDER BY ' . implode(',', $order);
        } else if(is_string($order) && $order) {
            $sql .= ' ORDER BY ' . $order;
        }
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . $offset;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($_where['params']);

        return $stmt->fetchAll();
    }

    protected function _insert ($arr) {
        $pdo = $this->getPdo(true);
        $param = array();
        $values = array();
        foreach ($arr as $v) {
            $values[] = "?";
            $param[] = $v;
        }
        $sql = "INSERT INTO " . $this->getTableName() . " (`";
        $sql .= implode('`,`',array_keys($arr));
        $sql .= "`) VALUES (";
        $sql .= implode(",",$values) . ")";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($param);
        $id = $pdo->lastInsertId();
        if ($id) {
            return $id;
        }
        $count = $stmt->rowCount();

        return ($count == 0) ? $result : $count;
    }

    public static function format_to_string ($data) {
        if (is_array($data)) {
            $str = "";
            foreach ($data as $key => $value) {
                $str .= $key . "," . $value;
            }
            return $str;
        }
        return $data;
    }

    public static function escape_like ($value) {
        if (is_numeric($value) || is_string($value)) {
            $value = self::escape_value($value);
            return str_replace(array('_','%'),array('\_','\%'),$value);
        } else {
            return $value;
        }
    }

    public static function escape_value ($value) {
        if (is_numeric($value) || is_string($value)) {
            return str_replace(array("'",'\\'),array("''",'\\\\'),$value);
        } else {
            return $value;
        }
    }

}
