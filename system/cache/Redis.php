<?php
class Cache_Redis extends Redis {
	
// 	public function set($key, $value, $time=null) {
// 		if (is_array($value) || is_object($value)) {
// 			$value = json_encode($value, JSON_UNESCAPED_UNICODE);
// 		}
// 		if ($time){
// 			parent::setex($key, $time, $value);
// 		}
// 		else {
// 			parent::set($key, $value);
// 		}
// 	}
	
}