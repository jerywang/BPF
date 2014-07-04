<?php
/**
 * $Id: WEBInterceptor.php Jul 2, 2014 wangguoxing (wangguoxing@baidu.com) $
 */
class WEBInterceptor extends Interceptor{
    public function before() {
        if($this->is_enable_debug()){
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }
    }
    public function after() {
        if($this->is_enable_debug()){
            $data = xhprof_disable();
            // xhprof_lib在下载的包里存在这个目录,记得将目录包含到运行的php代码中
            include_once APP_PATH."xhprof/xhprof_lib/utils/xhprof_lib.php";
            include_once APP_PATH."xhprof/xhprof_lib/utils/xhprof_runs.php";
            $objXhprofRun = new XHProfRuns_Default();
            // 第一个参数j是xhprof_disable()函数返回的运行信息
            // 第二个参数是自定义的命名空间字符串(任意字符串),
            // 返回运行ID,用这个ID查看相关的运行结果
            $run_id = $objXhprofRun->save_run($data, "xhprof");
            echo $run_id;
            echo "---------------\n".
                    "Assuming you have set up the http based UI for \n".
                    "XHProf at some address, you can view run at \n".
                    "<a target='_blank' href='http://dev.jerry.com/xhprof/xhprof_html/?run=$run_id&source=xhprof' style='color:#ff0000;font-weight:bolder'>查看性能</a>\n".
                    "---------------\n";
        }
    }

    public function is_enable_debug(){
        if($_GET['debug']){
            $pattern = BPF::getInstance()->getConfig("Config_Common");
            $ip = BPF::getInstance()->getRequest()->getClientIp();
            foreach ($pattern['allow_debug_ip'] as $pat){
                if(preg_match($pat, $ip)){
                    return true;
                    break;
                }
            }
        }
        return false;
    }
}