<?php

/**
 * Database Access Object
 *
 * $Id Base.php 2014-12-31 13:13 王国行 wangguoxing@baidu.com $
 */
abstract class Dao_Base {

    /**
     * @brief  返回当前cluster的名称
     *
     * @return string - pdo name
     */
    abstract public function getClusterName();

    /**
     * @brief  返回 当前dao对应的数据库表的名称
     * @return string - table name
     */
    abstract public function getTableName();

    /**
     * @brief  返回当前table主键
     *
     * @return string primary key name
     */
    abstract public function getTablePK();

    /**
     * 数据表字段, 该方法可以被reload
     *
     * @return string
     */
    public function getFields() {
        return '*';
    }

    /**
     * @brief  获取一个 pdo 实例
     *
     * @param boolean $isMaster - true :master db connection false: slave db connection
     *
     * @return Db_Pdo
     */
    protected function getPdo($isMaster = false) {
        return DB_Factory::getInstance()->getPdo($this->getClusterName(), $isMaster);
    }

    /**
     * @brief 从数据库表中获取$limit行记录
     *
     * @param $where - array eg: array('id'=>$id) = where id = $id;
     * @param $order - string or array eg: id desc or array("id desc","updated desc")
     * @param $limit - 返回的记录数
     * @param $offset - 开始位置
     *
     * @return array - table row
     */
    public function select($where = array(), $order = null, $limit = 100, $offset = 0) {
        $sql = 'select ' . $this->getFields() . ' from ' . $this->getTableName();
        $_where = $this->buildWhere($where);
        $sql .= $_where['where'];
        if(!empty($order)) {
            if (is_array($order)) {
                $sql .= ' order by ' . implode(',', $order);
            } else if (is_string($order)) {
                $sql .= ' order by ' . $order;
            }
        }
        if ($limit > 0) {
            $sql .= ' limit ' . intval($limit) . ' offset ' . intval($offset);
        }
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($_where['params']);
        Log::notice('SQL: ' . $sql . '| '. implode(',', $_where['params']));
        return $stmt->fetchAll();
    }

    /**
     * 查找指定条件的行数(包含去重复计数)
     *
     * @param $where - @see find
     * @param $field : 去重字段
     *
     * @return int - table rows
     */
    public function selectCount($where = array(), $field = '') {
        $_where = $this->buildWhere($where);
        if ($field) {
            $sql = 'select count(distinct '.$field .') as total from ' . $this->getTableName();
        } else {
            $sql = 'select count(*) as total from ' . $this->getTableName();
        }
        $sql .= $_where['where'];
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($_where['params']);
        $res = $stmt->fetch();
        Log::notice('SQL: ' . $sql . '| '. implode(',', $_where['params']));
        return $res['total'];
    }

    /**
     * 更新数据库表
     *
     * @param $data - array eg: array('id'=>$id)
     * @param $where - array @see
     *
     * @return int effect rows
     */
    public function update($data = array(), $where = array()) {
        $count = 0;
        $dataCount = count($data);
        if (!empty($data)) {
            $sql = 'update ' . $this->getTableName() . ' set';
            $i = 1;
            foreach ($data as $key => $value) {
                $sql .= is_int($value) ? "{$key}= {$value} " : "{$key} = '{$value}'";
                $sql .= ($i < $dataCount) ? ',' : '';
                $i++;
            }
            $_where = $this->buildWhere($where);
            $sql .= $_where['where'];
            $stmt = $this->getPdo()->prepare($sql);
            $stmt->execute($_where['params']);
            $count = $stmt->rowCount();
            Log::notice('SQL: ' . $sql . '| '. implode(',', $_where['params']));
        }
        return $count;
    }

    /**
     * 向数据库表中插入一行数据
     *
     * @param  array $array - eg:array('column'=>$colvalue)
     *
     * @return int insert id , if primary key not exists  return effect rows
     */
    public function insert($array) {
        $param = array();
        $values = array();
        foreach ($array as $v) {
            $values[] = "?";
            $param[] = $v;
        }
        $sql = 'insert into ' . $this->getTableName() .' (' . implode(',', array_keys($array)) . ') values (';
        $sql .= implode(',', $values).')';
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($param);
        $id = $this->getPdo()->lastInsertId();
        Log::notice('SQL: ' . $sql . '| '. implode(',', $param));
        if ($id) {
            return $id;
        }
        $count = $stmt->rowCount();
        return $count;
    }

    /**
     * 对数据库表中数据进行删除
     *
     * @param $where - array eg:array('column'=>$value)
     *
     * @return int $result -  effect rows
     */
    public function delete($where) {
        $count = 0;
        if(!empty($where)) {
            $_where = $this->buildWhere($where);
            $sql = 'delete from ' . $this->getTableName();
            $sql .= @$_where['where'];
            $stmt = $this->getPdo()->prepare($sql);
            $stmt->execute($_where['params']);
            Log::notice('SQL: ' . $sql . '| '. implode(',', $_where['params']));
            $count = $stmt->rowCount();
        }
        return $count;
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql SQL语句
     * @param array $params SQL绑定参数
     *
     * @return array|null|string
     */
    public function execute($sql, $params = array()) {
        $stmt = $this->getPdo()->prepare($sql);
        if (!$stmt->execute($params)) {
            return false;
        }
        Log::notice('SQL: ' . $sql . '| '. implode(',', $params));
        $type = strtoupper(substr($sql, 0, 6));
        $result = null;
        switch ($type) {
            case 'INSERT':
                $result = $this->getPdo()->lastInsertId();
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

    private function buildWhere($where) {
        $_where = array('where' => '', 'params' => array());
        if (!empty($where)) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $_where['fields'][] = '(' . $key . ' = ?)';
                    $_where['params'][] = $value;
                }
                $_where['where'] = ' WHERE ' . implode(' AND ', $_where['fields']);
            } else {
                $_where['where'] = ' WHERE ' . $where;
            }
        }

        return $_where;
    }

}
