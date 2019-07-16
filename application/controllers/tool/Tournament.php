<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tournament extends Api_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('applicant_model','applicant');
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
            $tid = $this->tournament->insert($data);
            if($tid > 0){
                $save_arr = array();
                $tmp_item = array();
                $tmp_item['field'] = 'id';
                $tmp_item['show'] = 'ID';
                $save_arr[]=$tmp_item;
                $tmp_item = array();
                $tmp_item['field'] = 'name';
                $tmp_item['show'] = '姓名';
                $save_arr[]=$tmp_item;

                $data2 = array(
                    'tid'   => $tid,
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

    public function state_setting($id=0){
       if($id>0){ 
            $tournament_info = $this->tournament->get_info($id);
            $this->response($tournament_info->show_state);  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function setting($id=0){
        $local_dict_show_name = array(
            'id' => 'ID',
            'name'    =>  '姓名',
            'nickname'    =>  '昵称',
            'age'    =>  '年龄',
            'qq'    =>  'QQ',
            'phone'    =>  '手机号码',
            'email'    =>  '邮箱',
            'avator'    =>  '头像',
            'idcard'    =>  '身份证',
            'game_id'   =>  '游戏ID',
            'group' =>  '组名',
            'group_order'   =>  '组内编号',
            'createtime'    =>  '创建时间',
            'extra_filed1'    =>  '备用字段1',
            'extra_filed2'    =>  '备用字段2',
            'extra_filed3'    =>  '备用字段3',
            'extra_filed4'    =>  '备用字段4',
            'dealtime'  =>  '审核时间'

        );
        if($id>0){
            $not_show_arr = array('id','tid','state');
            $count = 0;
            $columns = $this->applicant->get_col();
            $data = array();

            $tournament_item = $this->tournament_item->get_info($id);
            $item_list = explode(',', $tournament_item->item_list);
            $show_dict=json_decode($tournament_item->show_dict,true);
            $show_dict_arr = array();
            foreach ($show_dict as $key => $value) {
                $show_dict_arr[$value['field']] = $value;
            }

            foreach ($columns as $key => $value) {
                if(!in_array($value['Field'], $not_show_arr)){
                    $count ++;
                    $tmp = array();
                    $tmp['column_name'] = $value['Field'];
                    $tmp['default_show_name'] = $local_dict_show_name[$value['Field']];
                    if(isset($show_dict_arr[$value['Field']])){
                        if(isset($show_dict_arr[$value['Field']]['show'])){
                            $tmp['show_name'] = $show_dict_arr[$value['Field']]['show'];
                        }
                        if(isset($show_dict_arr[$value['Field']]['type']) && $show_dict_arr[$value['Field']]['type'] == 'image'){
                            $tmp['is_image'] = 1;
                        }
                    }
                    if(in_array($value['Field'], $item_list)){
                        $tmp['is_show'] = 1;
                    }
                    if(!isset($tmp['show_name'])) $tmp['show_name'] = '';
                    if(!isset($tmp['is_image'])) $tmp['is_image'] = 0;
                    if(!isset($tmp['is_show'])) $tmp['is_show'] = 0;
                    $data[]= $tmp;
                }
            }
            
            $return_arr = $this->getLayuiList(0,'配置',$count,$data);
            $this->response($return_arr);  
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
                if(isset($value)){
                    $tmp_arr[$item]['show'] = $value;   
                }else{
                    unset($tmp_arr[$item]['show']);
                }
            }else if($position == 3){
                if($value == true){
                    $tmp_arr[$item]['type'] = 'image';   
                }else{
                    $tmp_arr[$item]['type'] = 'text';   
                }                
            }
            $data['show_dict'] = json_encode($tmp_arr);


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
