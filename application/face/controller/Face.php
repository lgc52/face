<?php


namespace app\face\controller;


use app\tool\controller\Result;
use app\tool\controller\Tool;
use think\facade\Cache;


class Face
{
    private  $url = 'https://aip.baidubce.com/oauth/2.0/token';
    private $param = array(
                    'grant_type' => 'client_credentials',
                    'client_id' => 'tGbWSBsfOEEMv6CObLKoMsv2',
                    'client_secret' => '913oOafU9HAbMvn59PCXvLavgGsY7OTf'
                    );
    public function index(){
        $access_token = Cache::get('access_token');
        if(empty($access_token)){
            $result = Tool::getInstance()->setUrl($this->url)->setParam($this->param)->execCurl();
            if(empty($result) || empty($result['access_token'])){
                return Result::getInstance()->setMsg('获取access_token失败')->echoJson();
            }
            $access_token = $result['access_token'];
            Cache::set('access_token',$access_token,2590000);//设置缓存
        }

        $image_path = 'C:\Users\HUAWEI\Desktop\4.jpg';
        $url = 'https://aip.baidubce.com/rest/2.0/face/v3/detect?access_token=' . $access_token;
        $param = array(
            'image' => Tool::getInstance()->base64EncodeImage($image_path),
            'image_type' => 'BASE64',
            'face_field' => 'quality'
        );
        $param = json_encode($param);
        $data = Tool::getInstance()->setUrl($url)->setParam($param)->execCurl();
        $result = Tool::getInstance()->checkPictureToCheckFace($data);

        if(empty($result) || $result['result'] === false){
            return Result::getInstance()->setMsg('人脸识别失败')->setDebug($result)->echoJson();
        }
        return Result::getInstance()->setCode(200)->setStatus(true)->setMsg('人脸识别成功')->setDatas($result)->echoJson();

    }

}