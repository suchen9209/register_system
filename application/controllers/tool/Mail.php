<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail extends Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('users_model','users');
        $this->load->model('tournament_model','tournament');
        $this->load->model('tournament_item_model','tournament_item');
        $this->load->model('tournament_mail_model','tournament_mail');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;

        $offset = ($page - 1)*$num;      

        $list = $this->tournament_mail->get_list($offset,$num,$option);
        $count = $this->tournament_mail->get_num($option);

        $return_arr = $this->getLayuiList(0,'邮件列表',$count,$list);
        $this->response($return_arr); 
        
           
    }

    public function insert(){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"百事可乐","username":"asdafsd","password":"123456","weight":"50","tid":1}';*/
        $data = json_decode($data_json,true);
        
        if($data){
            if($this->tournament_mail->insert($data)){
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
        if($data && $id > 0){
            if($this->tournament_mail->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }


}
