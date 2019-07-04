<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Init extends Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('users_model','users');
        $this->load->model('applicant_model','applicant');
        $this->load->model('tournament_model','tournament');

    }

    public function index(){
        $this->load->library('session');
        $user_id = $this->session->userdata(ADMIN_SESSION_NAME);
        $user_info = $this->users->get_info($user_id);
        unset($user_info->password);
        if($user_info->tid !=0){
            $tournament_info = $this->tournament->get_info($user_info->tid);    
        }else{
            $tournament_info = NULL;
        }
        

        $return_arr['user_info'] = $user_info;
        $return_arr['tournament_info'] = $tournament_info;

        $this->response($return_arr);     
    }

    public function menu_json(){
        $this->load->library('session');
        $user_id = $this->session->userdata(ADMIN_SESSION_NAME);
        $user_info = $this->users->get_info($user_id);
        unset($user_info->password);
        if($user_info->tid !=0){
            $tournament_info = $this->tournament->get_info($user_info->tid);    
        }else{
            $tournament_info = NULL;
        }


        $return_arr = array();
        $return_arr['contentManagement'] = array();

        $menu = array();
        $menu['title'] = $tournament_info->name;
        $menu['icon'] = "&#xe61c;";
        $menu['href'] = '';
        $menu['children'] = array();

        $state_show_arr = explode(',', $tournament_info->show_state);
        if(in_array('-1', $state_show_arr)){
            $menu['children'][]=array('title'=>'全部报名列表','icon'=>"&#xe61c;",'href'=>'page/main/all.html','spread'=>true);   
        }
        if(in_array('5', $state_show_arr)){
            $menu['children'][]=array('title'=>'备选列表','icon'=>"&#xe61c;",'href'=>'page/main/alternative.html','spread'=>true);   
        }
        if(in_array('10', $state_show_arr)){
            $menu['children'][]=array('title'=>'通过列表','icon'=>"&#xe61c;",'href'=>'page/main/pass.html','spread'=>true);   
        }
        if(in_array('0', $state_show_arr)){
            $menu['children'][]=array('title'=>'未审核列表','icon'=>"&#xe61c;",'href'=>'page/main/init.html','spread'=>true);   
        }

        $return_arr['contentManagement'][]=$menu;
        
        $this->response($return_arr);  
    }


}
