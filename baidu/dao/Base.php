<?php
/**
 * Database Access Object
 * @author jerry
 *
 */
abstract class Dao_Base {

    /**
     * 是否要从master 数据库上读
     * @var unknown_type
     */
    public $read_from_master = false;

    /**
     * memcache key 的前缀
     * @var string
     */
    const MEMCACHE_KEY_PREFIX = "mem-dao-";

    /**
     * 如果 table 没有 primary key 则使用 缺省的
     * @var unknown_type
     */
    const NONE_TABLE_PK = "nonepk";

    public $cache_expire_time = 300;

    public $cache_expire_time_pk = 900;

    /**
     * 缓存时是否缓存空
     * @var boolean
     * @example if (isset ($rt) && $rt !== false && ($this->enable_cache_empty || !empty($rt))) {return $rt;}
     */
    public $enable_cache_empty = true;

    public function get_cache_expire_time_pk () {
        return $this->cache_expire_time_pk;
    }

    public function get_cache_expire_time () {
        return $this->cache_expire_time;
    }

    public function __construct() {
    }

    /**
     * 返回 当前dao对应的数据库表的名称
     * @return string - table name
     */
    abstract public function getTableName();

    /**
     * 返回当前pdo的名称
     * @return string - pdo name
     */
    abstract public function getReadPdoName();
    abstract public function getWritePdoName();

    /**
     * return the table primary key name
     * @return string - primary key name
     */
    abstract public function getTablePK ();

    /**
     * 从数据库表中获取$limit行记录
     * @param $where - array eg: array('id'=>$id) = where id = $id;
     * @param $order - string or array eg: id desc or array("id desc","updated desc")
     * @param $limit - 返回的记录数
     * @param $offset - 开始位置
     * @return array - table row
     */
    public function find ($where = array(), $order = null, $limit = 100, $offset = 0) {
        $id = md5(self::format_to_string($where)  . "-" . self::format_to_string($order) . "-" . $limit . "-" . $offset);
        $key = $this->getKey ($id);
        if ($this->getCache()->enableCache) {
            $rt = $this->getCache()->get($key);
            if (isset ($rt) && $rt !== false && !empty($rt)) {
                return $rt;
            }
        }
        $rt = $this->_fetch_all($where,$order,$limit,$offset);
        $this->getCache()->set($key,$rt,null,$this->get_cache_expire_time());
        return $rt;
    }

    public function findBySql ($sql='',$params=array()) {
        $sql = trim($sql);
        if (empty($sql)) {
            return false;
        }

        $key = self::build_key(md5($sql.json_encode($params))) ;

        if ($this->getCache()->enableCache) {
            $rt = $this->getCache()->get($key);
            if (isset ($rt) && $rt !== false && !empty($rt)) {
                return $rt;
            }
        }
        $rt = $this->execute($sql,$params);
        $this->getCache()->set($key,$rt,null,$this->get_cache_expire_time());
        return $rt;
    }

    /**
     * 从数据库表中获取$limit行记录 $select 得到指定的字段
     * @param $where - array eg: array('id'=>$id) = where id = $id;
     * @param $order - string or array eg: id desc or array("id desc","updated desc")
     * @param $limit - 返回的记录数
     * @param $offset - 开始位置
     * @return array - table row
     */
    public function findShort ($where = array(), $order = null, $limit = 100, $offset = 0, $feild=array()) {
        $id = md5(self::format_to_string($where)  . "-" . self::format_to_string($order) . "-" . $limit . "-" . $offset.self::format_to_string($feild));
        $key = $this->getKey ($id);

        if ($this->getCache()->enableCache) {
            $rt = $this->getCache()->get($key);
            if (isset ($rt) && $rt !== false) {
                return $rt;
            }
        }
        $rt = $this->_fetch_all_short($where,$order,$limit,$offset,$feild);
        $this->getCache()->set($key,$rt,null,$this->get_cache_expire_time());
        return $rt;
    }

    /**
     * 从数据库表中获取一行记录
     * @see find()
     * @param $where
     * @param $order
     * @return array - table row data
     */
    public function findRow ($where = array(), $order = null) {
        $id = md5 (self::format_to_string($where) . "-" . self::format_to_string($order));
        $key = $this->get_key($id);

        if ($this->getCache()->enableCache) {
            $rt = $this->getCache()->get($key);
            if (isset ($rt) && $rt !== false && !empty($rt)) {
                return $rt;
            }
        }

        $rt = $this->_fetch_row($where,$order);
        $this->getCache()->set($key,$rt,null,$this->get_cache_expire_time());
        return $rt;
    }

    /**
     * 查找指定条件的行数(包含去重复计数)
     * @param $where - @see find
     * @param $field: 去重字段
     * @return int - table rowsd
     */
    public function findCount ($where = array(), $field = '') {
        $id = md5(self::format_to_string($where)."-".$field);
        $key = $this->get_key ($id);

        if ($this->getCache()->enableCache) {
            $rt = $this->getCache()->get($key);
            if (isset ($rt) && $rt !== false && !empty($rt)) {
                return $rt;
            }
        }

        $rt = $this->_fetch_count($where, $field);
        $this->getCache()->set($key,$rt,null,$this->get_cache_expire_time());
        return $rt;
    }

    /**
     * 得到指定字段的总合 SUM
     * @param: $where -@see find
     * @return int -feild SUM
     * */
    public function findSum($feild,$where = array()){
        $id = 'find_sum'.$feild.md5(self::format_to_string($where));
        $key = $this->get_key ($id);
        if ($this->getCache()->enableCache) {
            $data = $this->getCache()->get($key);
            if ($data !== false) {
                return $data;
            }
        }
        $data = $this->_fetch_sum($feild,$where);
        $this->getCache()->set($key,$data,null,$this->get_cache_expire_time());
        return $data;
    }

    /**
     * find table data by primary key
     * @param $id - primary key value
     * @return array
     */
    public function findById ($id) {
        //如果可以缓存，才可以从缓存中取
        $key = $this->build_key_pk($id);
        if ($this->getCache()->enableCache) {
            $data = $this->getCache()->get($key);
            if ($data !== false) {
                return $data;
            }
        }
        $data = $this->_fetch_by_id($id);
        $this->getCache()->set($key,$data,null,$this->get_cache_expire_time_pk());
        return $data;
    }


    /**
     * find data by multi primary key value
     * @param $id_array - multi primary key value eg: array($id,$id)
     * @return array
     */
    public function findByIds ($id_array) {
        if (empty($id_array) || !is_array($id_array)) {
            return array();
        }

        //确保传进来的全部是数字
        $id_array = $this->format_id_array($id_array);
        $key_array = array();
        $list = array();
        foreach ($id_array as $id) {
            $key = $this->build_key_pk($id);
            $key_array[] = $key;
            $list[$id]['key'] = $key;
        }
        $data = $this->getCache()->get($key_array);
        $res = array();
        //需要在数据库中查询的IDS
        $_ids = array();
        foreach ($list as $id => $item) {
            if (isset($data[$item['key']])) {
                $res[$id] = $data[$item['key']];
            } else {
                $_ids[] = intval($id);
            }
        }
        if (!empty($_ids)) {
            $_res = $this->_find_by_ids ($_ids);
            if (!empty($_res)) {
                //设置缓存
                foreach ($_res as $id =>$val) {
                    $key = $this->build_key_pk($id);
                    $this->getCache()->set($key,$val,null,$this->get_cache_expire_time_pk());
                }
                $res += $_res;
            }
        }

        $result = array();
        //对数据按照传入ID顺序排序
        if(count($res)>0){
            foreach ($id_array as $id){
                if(isset($res[$id])){
                    $result[$id]= $res[$id];
                }
            }
        }
        return $result;
    }

    protected  function _find_by_ids ($id_array) {
        if (empty($id_array) || !is_array($id_array)) {
            return array();
        }
        $id_array = $this->format_id_array($id_array);
        $pk = $this->getTablePK();
        $ids_str = join (',',$id_array);
        $where = "$pk in ($ids_str)";
        $query = $this->_fetch_all($where,null,count($id_array));
        $rs = array();
        foreach ($query as $val) {
            $rs[$val[$pk]] = $val;
        }
        return $rs;
    }

    /**
     * 将pdo返回结果集中的索引改为主键
     * @param array $array
     * @return array
     */
    public function findAssoc($where = array(), $order = null, $limit = 100, $offset = 0) {
        $query = $this->find($where,$order,$limit,$offset);
        if (empty($query)) {
            return array();
        }
        $pk = $this->getTablePK();
        $rs = array();
        foreach ($query as $val) {
            $rs[$val[$pk]] = $val;
        }
        return $rs;
    }

    /**
     * 将pdo返回结果集中的索引改为主键
     * @param array $array
     * @return array
     */
    public function findShortAssoc($where = array(), $order = null, $limit = 100, $offset = 0,$fields=array()) {
        $query = $this->findShort($where,$order,$limit,$offset,$fields);
        if (empty($query)) {
            return array();
        }
        $pk = $this->getTablePK();
        $rs = array();
        foreach ($query as $val) {
            $rs[$val[$pk]] = $val;
        }
        return $rs;
    }

    /**
     * 更新数据库表
     * @param $data - array eg: array('id'=>$id)
     * @param $where - array @see
     * @return effect rows
     */
    public function update ($data = array(),$where = array(),$status=FALSE) {
        $rs = $this->_update($data,$where,$status);
        return $rs;
    }


    /**
     * update row by primary key
     * @param $id - primary key value
     * @param $data - update data eg: array('updated'=>$value)
     * @param $status - update status  'field_name = field_name+1'
     * @return effect rows
     */
    public function updateById ($id,$data,$status = FALSE ) {
        //$rs = $this->_update_by_ids($data,array($id));
        $rs = $this->_update($data,array($this->getTablePK()=>$id),$status);
        $key = $this->build_key_pk($id);
        if ($rs) {
            $this->getCache()->delete($key);
        }

        return $rs;
    }

    /**
     * update rows by multi primary key
     * @param $id_array
     * @param $data
     * @return unknown_type
     */
    public function updateByIds ($id_array,$data) {
        $rs = $this->_update_by_ids($data,$id_array);
        if ($id_array&$rs) {
            foreach ($id_array as $id) {
                $key = $this->build_key_pk($id);
                $this->getCache()->delete($key);
            }
        }

        return $rs;
    }

    /**
     * 向数据库表中插入一行数据
     *
     * @param array $data - array eg:array('column'=>$colvalue)
     * @param array $filter 需要插入DB的列名，有可能传递过来的数据会多于列名
     * @return insert id , if primary key not exists  return effect rows
     */
    public function insert ($data,$filter=array()) {
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
     * 向数据库批量插入多行数据
     * @param $data - array eg:array(array('column'=>$colvalue1),array('column'=>$colvalue2))
     * warning：每行的列结构应该相同
     */
    public function batchInsert($data, $is_ignore = false){
        if (empty($data)) return false;

        $_data = $data;
        $pdo = $this->getPdo(true);
        $param = array();
        $values = array();

        //过滤掉重复提交的报错
        $ignore = "";
        if ($is_ignore) {
            $ignore = "ignore";
        }
        $sql = "INSERT {$ignore} INTO ". $this->getTableName()." (`";
        $sql .= implode('`,`',array_keys(array_pop($data)))."`) VALUES ";
        $param = array();
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
    public function remove ($where) {
        if (!is_array($where)) {
            return false;
        }
        $pdo = $this->getPdo(true);
        $_where = $this->build_where($where);
        $sql = "delete from " . $this->getTableName();
        $sql .= @$_where['where'];
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($_where['params']);
        $rs = $stmt->rowCount();
        return ($rs == 0) ? $result : $rs;
    }


    public function removeByIds ($ids) {
        if ($this->getTablePK() == self::NONE_TABLE_PK) {
            return false;
        }
        if (empty($ids)) {
            return false;
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $ids = $this->format_id_array($ids);
        if(empty($ids)) {
            return false;
        }
        $rs = $this->remove(array($this->getTablePK()." in (".join(',',$ids).") and ?"=>1));
        $this->delete_pk_cache($ids);
        return $rs;
    }

    /**
     * 设置当前的 dao memcache是否开启
     * @param $boolean - true : enable memcache , false : disable memcache
     * @return null;
     */
    public function setCacheEnable ($boolean) {
        $this->getCache()->setEnable($boolean);
    }

    public function getKey ($str) {
        return $this->build_key($str);
    }

    final protected function build_key_pk ($id) {
        $table_name = $this->getTableName();
        $key = self::MEMCACHE_KEY_PREFIX . $table_name . "-".$this->getTablePK()."_".$id;
        return $key;
    }

    final protected function build_key ($str) {
        $table_name = $this->getTableName();
        $key = self::MEMCACHE_KEY_PREFIX .$table_name."-".$str;
        return $key;
    }

    protected function _fetch_by_id ($id) {
        return $this->_fetch_row(array($this->getTablePK() => $id));
    }

    protected function _fetch_row ($where = array(), $order = null) {
        $rows = (array)$this->_fetch_all($where, $order, 1, 0);
        $rt = array_pop($rows);
        if ($rt === null) {
            $rt = array();
        }
        return $rt;
    }

    protected function _fetch_count ($where = array(), $field = '') {
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

    protected function _fetch_sum ($feild,$where = array()) {
        $pdo = $this->getPdo ();
        $_where = $this->build_where($where);
        $sql = "SELECT SUM($feild) AS sum FROM " . $this->getTableName();
        $sql .= @$_where['where'];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($_where['params']);
        $rs = $stmt->fetch();
        return $rs['sum'];
    }

    protected function _fetch_all ($where = array(), $order = null, $limit = 100, $offset = 0) {
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

    protected function _fetch_all_short ($where = array(), $order = null, $limit = 100, $offset = 0, $feild=array()) {
        $pdo = $this->getPdo();
        $str_select = '*';
        if($feild) {
            $str_select = @implode(',',$feild);
        }
        $sql = "SELECT $str_select FROM " . $this->getTableName();

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

    protected function _update ($data = array(),$where = array(),$status = FALSE) {
        $data_count  = count($data);
        if ($data_count < 1) {
            return false;
        }
        if (empty($where)) {
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
            $sql .= ($i < $data_count)?',':'';
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

    protected function _update_by_ids ($arr,$id_array) {
        if (empty($id_array) || !is_array($id_array)) {
            return 0;
        }
        $id_array = $this->format_id_array($id_array);
        $pdo = $this->getPdo(true);
        $param = array();
        $sql = "UPDATE " . $this->getTableName() . " SET ";
        $pk = $this->getTablePK();
        foreach ($arr as $k => $item) {
            $sql .= "`{$k}`= ?,";
            $param[] = $item;
        }
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE " . $pk . " in (" . implode(",",$id_array) . ")";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($param);
        $rs = $stmt->rowCount();
        return ($rs == 0) ? $result : $rs;
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

    /**
     * @var Cache_Memcached
     */
    private $_cache = null;

    /**
     * @return object Memcached
     * 'object'这个key将在5分钟后过期  $this->_cache->set('object', new stdclass, time() + 300);
     */
    public function getCache () {
        if ($this->_cache === null) {
            $this->_cache = new Cache_Memcached();
            $this->_cache->_addServers();
        }
        return $this->_cache;
    }

    /**
     * @var DB_Factory
     */
    private $_dbfactory = null;

    /**
     * 获取一个 pdo 实例
     * @param $write boolean - true :master db connection false: slave db connection
     * @return Db_Pdo
     */
    protected function getPdo ($write = false) {
        if (null === $this->_dbfactory) {
            $this->_dbfactory = DB_Factory::getInstance();
        }
        if ($write) {
            return $this->_dbfactory->getPdo($this->getWritePdoName());
        } else {
            if ($this->read_from_master) {
                return $this->_dbfactory->getPdo($this->getWritePdoName());
            }
        }
        return $this->_dbfactory->getPdo($this->getReadPdoName());
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


    /**
     * 执行SQL语句
     *
     * @param string $sql SQL语句
     * @param array $params SQL绑定参数
     * @param boolean $write 数据库主机选择
     * @return array
     */
    public function execute($sql, $params = array(), $write = false) {

        $pdo = $this->getPdo($write);

        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute((array)$params)) {
            return false;
        }

        $type = strtoupper(substr($sql, 0, 6));
        $result = null;
        switch ($type) {
            case 'INSERT':
                $result = $pdo->lastInsertId();
                if (!$result) {
                    $result = $stmt->rowCount();
                }
                break;

            case 'UPDATE':
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

    /**
     * 格式化ID数组，确保数组中全部是数字
     * @param $id_array
     */
    public function format_id_array($id_array) {
        foreach ($id_array as $key=>$val) {
            $val = intval($val);
            if ($val<1) {
                unset($id_array[$key]);
            }else {
                $id_array[$key] = $val;
            }
        }
        return $id_array;
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

    public function delete_pk_cache ($ids) {
        if(is_array($ids)) {
            foreach ($ids as $id) {
                if (!$id) {
                    continue;
                }
                $this->getCache()->delete($this->build_key_pk($id));
            }
        } else {
            $id = intval($ids);
            if ($id > 0) {
                $this->getCache()->delete($this->build_key_pk($id));
            }
        }
    }

}
