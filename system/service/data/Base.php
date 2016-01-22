<?php

/**
 * $Id: Base.php Jul 2, 2014 wangguoxing (wangguoxing@system.com) $
 * class Service_Data_Base
 */
abstract class Service_Data_Base {

    public $enable_cache_empty = true;
    /**
     * cache 功能是否开启
     *
     * @var boolean
     */
    public $enableCache = true;
    private $daoList = null;

    public function __construct() {
    }

    public function enableCache($boolean) {
        $this->enableCache = $boolean;
    }

    /**
     * 优先使用APP_PATH下面的dao，其次使用core下面的
     * core为公共的，app下面app当前的
     *
     * @param $daoName - string
     *
     * @return Db_Dao
     */
    public function getDao($daoName) {
        if (!isset($this->daoList[$daoName])) {
            $this->daoList[$daoName] = new $daoName();
        }
        return $this->daoList[$daoName];
    }

    /**
     * 将ID数组格式化成例如 1,2,3
     */
    public function join_id_array($id_array) {
        $id_array = $this->format_id_array($id_array);
        if (empty($id_array)) {
            return '';
        } else {
            return join(',', $id_array);
        }
    }

    /**
     * 格式化ID数组，去掉空的项目
     * @param array $id_array
     * @return array
     */
    public function format_id_array($id_array) {
        if (empty($id_array) || !is_array($id_array)) {
            return array();
        }
        foreach ($id_array as $k => $v) {
            if (empty($v)) {
                unset($id_array[$k]);
            } else {
                $id_array[$k] = intval($v);
            }
        }
        return $id_array;
    }

    /**
     * 将 array('id1','id2') 这样的数组变成 'id1','id2' 这样的字符串，用户sql语句
     */
    public function join_string_array($ids, $glue = "'") {
        if (empty($ids) || !is_array($ids)) {
            return '';
        } else {
            $tmp = array();
            foreach ($ids as $val) {
                $tmp[] = addslashes($val);
            }
            return "$glue" . join("$glue,$glue", $tmp) . "$glue";
        }
    }
}
