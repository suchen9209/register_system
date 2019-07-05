<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Init extends Api_Controller {

    private $tid;
    private $user_id;
    private $usr_info;
    private $tournament_info = NULL;

    public function __construct(){
        parent::__construct();

        $this->load->model('users_model','users');
        $this->load->model('applicant_model','applicant');
        $this->load->model('tournament_model','tournament');
        $this->load->model('tournament_item_model','tournament_item');
        $this->load->model('tournament_mail_model','tournament_mail');

        $this->load->library('session');
        $this->user_id = $this->session->userdata(ADMIN_SESSION_NAME);
        $this->user_info = $this->users->get_info($this->user_id);
        if($this->user_info->tid !=0){
            $this->tournament_info = $this->tournament->get_info($this->user_info->tid);    
        }

    }

    public function index(){   

        $return_arr['user_info'] = $this->user_info;
        $return_arr['tournament_info'] = $this->tournament_info;

        $this->response($return_arr);     
    }

    public function menu_json(){

        $return_arr = array();
        $return_arr['contentManagement'] = array();

        $menu = array();
        $menu['title'] = $this->tournament_info->name;
        $menu['icon'] = "&#xe61c;";
        $menu['href'] = '';
        $menu['children'] = array();

        $state_show_arr = explode(',', $this->tournament_info->show_state);
        if(in_array('-1', $state_show_arr)){
            $menu['children'][]=array('title'=>'全部报名列表','icon'=>"&#xe61c;",'href'=>'page/main/all.html','spread'=>true);   
        }
        if(in_array('5', $state_show_arr)){
            $menu['children'][]=array('title'=>'备选列表','icon'=>"&#xe61c;",'href'=>'page/main/all.html?state=5','spread'=>true);   
        }
        if(in_array('10', $state_show_arr)){
            $menu['children'][]=array('title'=>'通过列表','icon'=>"&#xe61c;",'href'=>'page/main/all.html?state=10','spread'=>true);   
        }
        if(in_array('0', $state_show_arr)){
            $menu['children'][]=array('title'=>'未审核列表','icon'=>"&#xe61c;",'href'=>'page/main/all.html?state=0','spread'=>true);   
        }

        $return_arr['contentManagement'][]=$menu;
        
        $this->response($return_arr);  
    }

    public function list_header_json(){
        //没有dict时默认使用这个
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
            'group_num' =>  '组名',
            'group_order'   =>  '组内编号',
            'createtime'    =>  '创建时间',
            'extra_filed1'    =>  '备用字段1',
            'extra_filed2'    =>  '备用字段2',
            'extra_filed3'    =>  '备用字段3',
            'extra_filed4'    =>  '备用字段4'
        );
/*
        width不写则默认宽度
        type不写则为默认text
        $save_arr = array();
        $tmp_item = array();
        $tmp_item['field'] = 'id';
        $tmp_item['show'] = 'ID';
        $save_arr[]=$tmp_item;
        $tmp_item = array();
        $tmp_item['field'] = 'name,phone,qq,extra_filed1';
        $tmp_item['show'] = '姓名';
        $save_arr[]=$tmp_item;
        $tmp_item = array();
        $tmp_item['field'] = 'nickname';
        $tmp_item['show'] = '昵称';
        $save_arr[]=$tmp_item;
        $tmp_item = array();
        $tmp_item['field'] = 'phone';
        $tmp_item['show'] = '手机号码';
        $tmp_item['width'] = 180;
        $save_arr[]=$tmp_item;
        $tmp_item = array();
        $tmp_item['field'] = 'extra_filed1';
        $tmp_item['show'] = '段位截图';
        $save_arr[]=$tmp_item;*/

        $extra_width_dict = array();
        $extra_type_dict = array();
        $tournament_item_info = $this->tournament_item->get_info($this->user_info->tid);
        $tournament_item_info->show_dict = json_decode($tournament_item_info->show_dict);
        foreach ($tournament_item_info->show_dict as $key => $value) {
            $local_dict_show_name[$value->field] = $value->show;
            if(isset($value->width)){
                $extra_width_dict[$value->field] = $value->width;
            }
            if(isset($value->type)){
                $extra_type_dict[$value->field] = $value->type;
            }
        }

        $show_field_arr = explode(',', $tournament_item_info->item_list);
        $return_arr = array();
        foreach ($show_field_arr as $key => $value) {
            $tmp_item = array();
            $tmp_item['field'] = $value;
            $tmp_item['title'] = $local_dict_show_name[$value];
            if(isset($extra_width_dict[$value])){
                $tmp_item['width'] = $extra_width_dict[$value];  
            }
            if(isset($extra_type_dict[$value])){
                $tmp_item['type'] = $extra_type_dict[$value];                   
            }
            $return_arr []= $tmp_item;

        }
        $this->response($return_arr);  
    }

    public function mail_json($tid,$state){
        $mails = $this->tournament_mail->get_mail(array('tid'=>$tid,'applicant_state'=>$state));
        $this->response($mails);  
    }


}
