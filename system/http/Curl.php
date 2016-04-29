<?php

class Http_Curl {

    public $timeout = 5;
    private $curl;

    /**
     * @param bool $isMulti
     * @return Http_Curl
     */
    public function __construct($isMulti = false) {
        if (!$isMulti) {
            $this->curl = curl_init();
            $this->init();
        }
    }

    public function init() {
        $this->setAttribute(CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
        $this->setAttribute(CURLOPT_RETURNTRANSFER, true);
        $this->setAttribute(CURLOPT_CONNECTTIMEOUT, $this->getTimeout());
        $this->setAttribute(CURLOPT_TIMEOUT, $this->getTimeout());
    }

    public function setAttribute($name, $value) {
        curl_setopt($this->curl, $name, $value);
    }

    public function getTimeout() {
        return $this->timeout;
    }

    public function setTimeout($sec) {
        $this->timeout = $sec;
    }

    /**
     * @param string $url
     * @param string $params
     * @param string $method
     *
     * @return array
     */
    public function execute($url, $params = '', $method = 'GET') {
        $pos = strpos($url, '?');
        switch ($method) {
            case 'POST':
                $this->setAttribute(CURLOPT_POST, true);
                if (!empty($params)) {
                    $this->setAttribute(CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'PUT':
                $this->setAttribute(CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($params)) {
                    $this->setAttribute(CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'DELETE':
                $this->setAttribute(CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    if ($pos) {
                        $url = $url . '&' . $params;
                    } else {
                        $url = $url . '?' . $params;
                    }
                }
                break;
            default:
                if (!empty($params)) {
                    if ($pos) {
                        $url = $url . '&' . $params;
                    } else {
                        $url = $url . '?' . $params;
                    }
                }
        }

        $this->setAttribute(CURLOPT_URL, $url);

        $document = curl_exec($this->curl);
        $curl_info = curl_getinfo($this->curl);

        return array("info" => $curl_info, "content" => $document);
    }

    /**
     * 非阻塞模式批量http请求
     * $urls = array (
     *        "url1" => array ("url" => "http://www.system.com/"),
     *        "url2" => array ("url" => "http://www.nuomi.com/"),
     * );
     * @param array $urls
     * @param string $options
     * @return array
     */
    public function multiCurl($urls, $options = "") {
        if (count($urls) <= 0) {
            return false;
        }
        $handles = array();
        if (!$options) {
            $options = array(
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_TIMEOUT => $this->getTimeout()
            );
        }
        // add curl options to each handle
        foreach ($urls as $k => $row) {
            $handles [$k] = curl_init();
            $options [CURLOPT_URL] = $row ['url'];
            curl_setopt_array($handles [$k], $options);
        }

        $mh = curl_multi_init();

        foreach ($handles as $k => $handle) {
            curl_multi_add_handle($mh, $handle);
            // echo "<br>adding handle {$k}";
        }

        $running_handles = null;
        // execute the handles
        do {
            $mrc = curl_multi_exec($mh, $running_handles);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($running_handles && $mrc == CURLM_OK) { //check for results and execute until everything is done
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $running_handles);
                    // echo "<br>''threads'' running = {$running_handles}";
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            } else {
                //if it returns -1, wait a bit, but go forward anyways!
                usleep(100);
            }
        }

        foreach ($urls as $k => $row) {
            $urls [$k] ['info'] = curl_getinfo($handles [$k]);
            $urls [$k] ['error'] = curl_error($handles [$k]);
            //if (! empty ( $urls [$k] ['error'] )) {
            if ($urls [$k] ['info']['http_code'] != 200) {
                $urls [$k] ['data'] = '';
            } else {
                $urls [$k] ['data'] = curl_multi_getcontent($handles [$k]); // get results
            }
            curl_multi_remove_handle($mh, $handles [$k]);
        }
        curl_multi_close($mh);
        return $urls;
    }

    public function __destruct() {
        if ($this->curl) {
            curl_close($this->curl);
        }
    }
}

?>
