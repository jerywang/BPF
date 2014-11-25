<?php
/**
 * $Id: Response.php 2014-6-30 wangguoxing (wangguoxing@system.com) $
 */
class Response {
    
    const COOKIE_DOMAIN = "cookie_domain";
    const COOKIE_PATH   = "cookie_path";

    public function redirect($url, $permanent=false) {
        header("Location: $url", true, $permanent ? 301 : 302);
    }

    public function setHeader($name, $value, $http_reponse_code=null){
		header("$name: $value", $http_reponse_code);
	}
	
	/**
	 * 设置浏览器local_cache
	 */
	public function setCacheHeader($maxage=60,$smaxage=0){
		header("Cache-Control: public,max-age=$maxage,s-maxage=$smaxage,must-revalidate");
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		header('Expires: ' . gmdate ("D, d M Y H:i:s", time() + $maxage). " GMT");
	}
	
	/**
	 * 渲染页面
	 * @param unknown $page
	 * @throws Exception
	 */
	public function render($page) {
	    $pathInfo = str_replace('_', '/', $page);
	    $cfg = BPF::getInstance()->getConfig('Config_Common');
	    $pagePath = 'view/'.strtolower($pathInfo).$cfg['tpl_suffix'];
        if(file_exists($pagePath)){
            extract($this->data);
            include($pagePath);
        }
        else {
            echo $pagePath.' not found!';
            throw new Exception($pagePath.' not found!', 1001);
        }
	}
	
	public function setAttr($name, $value){
	    $this->data[$name] = $value;
	}
	
	public function getAttr($name = null){
	    if ($name) {
	        return $this->data[$name];
	    }
	    return $this->data;
	}
	
	public $data = array();
	
	public function isAjax($bool = true){
	    $this->isAjax = $bool;
	}
	
	public $isAjax = false;
	
}
