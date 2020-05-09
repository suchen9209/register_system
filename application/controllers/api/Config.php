<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends Api_Controller {

    public function __construct(){
        parent::__construct('none_rest');
         $this->load->model('tournament_model','tournament');
    }

    public function index()
    {
        $tid = $this->input->get_post('tid') ? $this->input->get_post('tid') : 1;
        $tournament_info = $this->tournament->get_info($tid);
        if($tournament_info->need_num <= $tournament_info->now_num){
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '报名人数已满',0), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '报名进行中',1), parent::HTTP_OK);
        }
    }

}
