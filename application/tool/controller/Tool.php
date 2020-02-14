<?php


namespace app\tool\controller;


class Tool
{

    protected static $_instance=null;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private $url = '';
    private $param = [];
    public function getUrl(){
       return $this->url;
    }
    public function getParam(){
        return $this->param;
    }

    public function setUrl($url){
        $this->url = $url;
        return $this;
    }
    public function setParam($param){
        $this->param = $param;
        return $this;
    }

    function execCurl() {
        $url = $this->getUrl();
        $param = $this->getParam();
        if (empty($url) || empty($param)) {
            return array('result' => false, 'err_msg' => 'url or param is null');
        }
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($curl);//运行curl
        $curl_err_msg = curl_error($curl);
        curl_close($curl);
        return json_decode($data,true);
    }


    function checkPictureToCheckFace($data) {
        if($data['error_code'] != 0) return array('result' => false, 'err_msg' => '上传的人脸不符合要求，请重新上传五官清晰的真实头像');
        $err_result = array('result' => false, 'err_msg' => '上传的人脸不符合要求，请重新上传五官清晰的真实头像!');
        $quality = $data['result']['face_list'][0]['quality'];
        if($quality['occlusion']['left_eye'] > 0.6) return $err_result; // 左眼被遮挡的阈值
        if($quality['occlusion']['right_eye'] > 0.6) return $err_result; // 右眼被遮挡的阈值
        if($quality['occlusion']['nose'] > 0.7) return $err_result; // 鼻子被遮挡的阈值
        if($quality['occlusion']['mouth'] > 0.7) return $err_result; // 嘴巴被遮挡的阈值
        if($quality['occlusion']['left_cheek'] > 0.8) return $err_result; // 左脸颊被遮挡的阈值
        if($quality['occlusion']['right_cheek'] > 0.8) return $err_result; // 右脸颊被遮挡的阈值
        if($quality['occlusion']['chin_contour'] > 0.6) return $err_result; // 下巴被遮挡阈值
        if($quality['blur'] != 0 && $quality['blur'] > 0.7) return $err_result; // 模糊度范围 0是最清晰，1是最模糊
        if($quality['illumination'] < 40) return $err_result; // 光照范围
        if($quality['completeness'] == 0) return $err_result; // 人脸完整度 0为人脸溢出图像边界，1为人脸都在图像边界内
        return array('result' => true);
    }


    function base64EncodeImage($image_path) {
        $base64_image = '';
        $image_info = getimagesize($image_path);
        $image_data = fread(fopen($image_path, 'r'), filesize($image_path));
        $base64_image = chunk_split(base64_encode($image_data));
        return $base64_image;
    }


}