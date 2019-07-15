<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tournament extends Api_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('tournament_model','tournament');
        $this->load->model('tournament_item_model','tournament_item');
        $this->load->model('tournament_mail_model','tournament_mail');
    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;

        $offset = ($page - 1)*$num;      
        $option =  array();

        $list = $this->tournament->get_list($offset,$num,$option);
        $count = $this->tournament->get_num($option);

        foreach ($list as $key => $value) {
            $list[$key]['starttime'] = date('Y-m-d',$value['starttime']);
            $list[$key]['endtime'] = date('Y-m-d',$value['endtime']);
        }

        $return_arr = $this->getLayuiList(0,'赛事列表',$count,$list);
        $this->response($return_arr);        
       
    }

    public function insert(){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"百事可乐","username":"asdafsd","password":"123456","weight":"50","tid":1}';*/
        $data = json_decode($data_json,true);
        $data['starttime'] = strtotime($data['starttime']);
        $data['endtime'] = strtotime($data['endtime']);
        
        if($data){
            if($this->tournament->insert($data)){
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
        $data['starttime'] = strtotime($data['starttime']);
        $data['endtime'] = strtotime($data['endtime']);
        if($data && $id > 0){
            if($this->tournament->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function setting($id=0){
        if($id>0){
            $return_data['tournament'] = $this->tournament->get_info($id);
            $return_data['tournament_item'] = $this->tournament_item->get_info($id);
            $return_data['tournament_item']->show_dict=json_decode($return_data['tournament_item']->show_dict);
            $this->response($this->getResponseData(parent::HTTP_OK, '赛事详细配置信息',$return_data), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function save_setting($id=0){
        if($id>0){
            $return_data['tournament'] = $this->tournament->get_info($id);
            $return_data['tournament_item'] = $this->tournament_item->get_info($id);
            $this->response($this->getResponseData(parent::HTTP_OK, '赛事详细配置信息',$return_data), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }


}
