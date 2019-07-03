<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct(){
        header('Access-Control-Allow-Origin:*');
        header('Content-Type:application/json');
        parent::__construct();

        $this->load->model('users_model','users');
        $this->load->library('session');
    }

    public function index()
    {
        $user_name = $this->input->get_post('username');
        $user_pass = $this->input->get_post('password');

        $return = [];
        if(isset($user_name) && isset($user_pass)){
            $res = $this->users->get_info_by_username($user_name);
            if ($res->password === password_md5($user_pass)) {
                $this->session->ADMIN_SESSION_NAME  =   $res->id;
                $return['status'] = 'success';
                $return['detail'] = '登录成功';
                $return['name'] = $res->name;
            }else{
                $return['status'] = 'fail';
                $return['detail'] = '账号或密码错误';
            }

        }else{
            $return['status'] = 'fail';
            $return['detail'] = '参数错误';
        }

        echo json_encode($return);
        exit();
        
    }

    public function get_info(){
        $return['username'] =  $_SESSION['username'];
        echo json_encode($return);
        exit();
    }

    public function register()
    {
        $action = $this->input->get('action');
        if($action == 'register'){
            $parm = $_POST;
            unset($parm['submit']);
            $parm['password'] = password_md5($parm['password']);
            $this->adminuser->insert($parm);
            redirect(ADMIN_PATH.'/admin');
            exit();
        }
        
    }



}
