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
            $list[$key]['starttime'] = date('Y-m-d H:i:s',$value['starttime']);
            $list[$key]['endtime'] = date('Y-m-d H:i:s',$value['endtime']);
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

                $save_arr = array();
                $tmp_item = array();
                $tmp_item['field'] = 'id';
                $tmp_item['show'] = 'ID';
                $save_arr[]=$tmp_item;
                $tmp_item = array();
                $tmp_item['field'] = 'name,phone,qq,extra_filed1';
                $tmp_item['show'] = '姓名';
                $save_arr[]=$tmp_item;

                $data2 = array(
                    'item_list' => 'id,name,phone,qq,email',
                    'show_dict' => json_encode($save_arr)
                );
                $this->tournament_item->insert($data2);
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

    public function update_tour_show_state($id=0){
        if($id>0){
            $state = $this->input->post_get('state');
            $tournament = $this->tournament->get_info($id);
            $show_state = $tournament->show_state;
            $show_state_arr = explode(',', $show_state);
            if(in_array($state, $show_state_arr)){
                foreach ($show_state_arr as $key=>$value)
                {
                    if ($value === $state)
                        unset($show_state_arr[$key]);
                }
            }else{
                $show_state_arr[]=$state;
            }
            $data['show_state'] = implode(',', $show_state_arr);

            if($this->tournament->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            } 
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function update_tour_item_list($id=0){
        if($id>0){
            $item_name = $this->input->post_get('item_name');
            $tournament_item = $this->tournament_item->get_info($id);
            $item_list = $tournament_item->item_list;
            $item_list_arr = explode(',', $item_list);
            if(in_array($item_name, $item_list_arr)){
                foreach ($item_list_arr as $key=>$value)
                {
                    if ($value === $item_name)
                        unset($item_list_arr[$key]);
                }
            }else{
                $item_list_arr[]=$item_name;
            }
            $data['item_list'] = implode(',', $item_list_arr);

            if($this->tournament_item->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            } 
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function update_tour_show_dict($id=0){
        if($id>0){
            $item = $this->input->post_get('item');
            $position = $this->input->post_get('position');
            $value = $this->input->post_get('value');

            $tournament_item = $this->tournament_item->get_info($id);
            $show_dict = $tournament_item->show_dict;
            $show_dict_arr = json_decode($show_dict,true);

            $tmp_arr = $show_dict_arr;
            foreach ($show_dict_arr as $key => $value) {
                $tmp_arr[$value['field']] = $value;
            }

            if(!isset($tmp_arr[$item])){
                $tmp_arr[$item]['field'] = $item;
            }
            if($position == 1){
                $tmp_arr[$item]['show'] = $value;
            }else if($position == 2){
                $tmp_arr[$item]['width'] = $value;
            }else if($position == 3){
                if($value == true){
                    $tmp_arr[$item]['type'] = 'image';   
                }else{
                    $tmp_arr[$item]['type'] = 'text';   
                }                
            }

            if($this->tournament_item->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            } 
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }





}
