<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Api_Controller {
    private $user_id;
    private $user_info;

    public function __construct(){
        parent::__construct();

        $this->load->model('users_model','users');
        $this->load->library('session');
        $this->user_id = $this->session->userdata(ADMIN_SESSION_NAME);
        $this->user_info = $this->users->get_info($this->user_id);

    }

    public function index(){

        if(intval($this->user_info->weight) != 100){
            $this->response([
                    $this->config->item('rest_status_field_name') => FALSE,
                    $this->config->item('rest_message_field_name') => 'Not Authorized'
                ], self::HTTP_UNAUTHORIZED);
        }else{
            $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
            $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;

            $offset = ($page - 1)*$num;      
            $option =  array();

            $list = $this->users->get_list($offset,$num,$option);
            $count = $this->users->get_num($option);

            $return_arr = $this->getLayuiList(0,'后台分配成员',$count,$list);
            $this->response($return_arr); 
        }
        
           
    }

    public function insert(){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"百事可乐","username":"asdafsd","password":"123456","weight":"50","tid":1}';*/
        $data = json_decode($data_json,true);
        $data['password'] = password_md5($data['password']);
        
        if($data){
            if($this->users->insert($data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function update($id=0){
        $data_json = $this->input->post_get('data');
        $data = json_decode($data_json,true);
        if(isset($data['password'])){
            $data['password'] = password_md5($data['password']);
        }
        if($data && $id > 0){
            if($this->goods->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }


}
