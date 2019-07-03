<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        header('Access-Control-Allow-Origin:*');
        header('content-type:text/html charset:utf-8');
        $date = date('ymd');

        $dir_base = __MYDIR__.'/uploads/'.$date."/";
        is_dir($dir_base) OR mkdir($dir_base, 0777, true);
        $show_dir_base = "/uploads/".$date."/"; 

        $upload_file = $_FILES['file'];

        $extend = pathinfo($upload_file['name']);//获取文件后缀名
        
        $gb_filename = time().rand(1,100).'.'.$extend['extension'];    

        $isMoved = false;  //默认上传失败
        $abc = $upload_file['size'];
        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;     //文件大小限制    1M = 1 * 1024 * 1024 B;
        $allow_type = array('image/png','image/gif','image/jpg','image/jpeg','image/bmp');
        if ($upload_file['size'] <= $MAXIMUM_FILESIZE && in_array($upload_file['type'], $allow_type)) {
            $abc = 1;
            $isMoved = @move_uploaded_file($upload_file['tmp_name'], $dir_base.$gb_filename);        //上传文件
        }

        header('Content-type:text/json');
        if($isMoved){
            $return_data['code'] = 0;
            $return_data['msg'] = '上传成功';
            $pic_url = 'http://'.HTTP_HOST.$show_dir_base.$gb_filename;
            $return_data['data'] = array('src'=>$pic_url);
            //$output = 'http://'.HTTP_HOST.$show_dir_base.$gb_filename;
        }else {
            $return_data['code'] = 400;
            $return_data['msg'] = '上传失败';
            $return_data['data'] = array('src'=>$abc);
            //$output = "error";
        }
        echo json_encode($return_data);
    }

}
