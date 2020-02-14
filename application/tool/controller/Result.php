<?php


namespace app\tool\controller;


use think\Controller;

class Result extends Controller
{
    protected static $_instance=null;
    private $status = false;
    private $code = 400;
    private $msg = '失败原因：';
    private $datas = [];
    private $extras = [];
    private $debug=0;

    public $echos=[];


    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function setNewInstance(){
        self::$_instance = new self;
        return self::$_instance;
    }

    /*返回json格式化*/
    public function echoJson($arr = null)
    {

        if (empty($arr)) {
            $arr = [
                'status' => $this->status,
                'code' => $this->code,
                'msg' => $this->msg,
                'datas' => $this->datas,
                'extras' => $this->extras,
                'debug' => $this->debug,
            ];
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        //$response = Response::create($arr, 'json');
        //throw new HttpResponseException($response);
    }


    public function setStatus($status){
        $this->status=$status;
        return $this;
    }


    public function setCode($code){
        $this->code=$code;
        return $this;
    }

    public function setMsg($msg){
        $this->msg=$msg;
        return $this;
    }

    public function setDatas($datas){
        $this->datas=$datas;
        return $this;
    }

    /*
extras：拓展字段，某些情况，目前的数据格式满足不了时，可将部分字段放在该属性中
    paging：分页属性，分页属性待定
    total：汇总属性，如：个人报表记录的汇总字段。
    target： 配合code使用，使用场景见 code枚举表
    timespan：服务器当前Linux时间戳
    gameversion：游戏版本号，用于清除前端缓存
     */
    public function setExtras($timespan=true,$target='',$paging=[],$gameversion=[],$total=[]){
        $extras=[];
        if($timespan){
            $extras['timespan']=time();
        }
        if($target){
            $extras['target']=$target;
        }
        if($paging){
            $extras['paging']=$paging;
        }
        if($gameversion){
            $extras['gameversion']=$gameversion;
        }
        $this->extras=$extras;
        return $this;
    }

    public function setDebug($debug){
        $this->debug=$debug;
        return $this;
    }

}