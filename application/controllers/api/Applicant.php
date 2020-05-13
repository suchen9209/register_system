<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Applicant extends Api_Controller {

    public function __construct(){
        parent::__construct('none_rest');
        $this->load->model('applicant_model','applicant');
        $this->load->model('tournament_model','tournament');
        $this->load->model('function/email_div_model','email_div');
    }

    
    public function insert(){
        header('Access-Control-Allow-Origin:*');
/*        $_POST[] = $this->security->xss_clean($_POST);*/
        $data_json = $this->input->post_get('data');
/*      $data_json = '{"name":"宿晨","nickname":"suchot","idcard":"1123123123123123","qq":"123123123123","phone":"12312312312","email":"12312312312","extra_filed1":"http://www.baoming11.com/uploads/1902/155133245595.png","game_id":"12312312312"}';*/
        $data = json_decode($data_json,true);

        
        $data['tid'] = 1;
        $data['createtime'] = time();

        $tournament_info = $this->tournament->get_info(1);
        if($data['name'] && $data['qq'] && $data['phone'] && $data['extra_filed1']){
            if($tournament_info->need_num > $tournament_info->now_num){
                if($this->applicant->insert($data)){
                    $this->tournament->add_one_applicant(1);
                    $this->response($this->getResponseData(parent::HTTP_OK, '报名成功'), parent::HTTP_OK);
                }else{
                    $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '报名失败'), parent::HTTP_OK);
                }    
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '报名人数已满'), parent::HTTP_OK);
            }
             
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function insert_by_tid($tid){
        header('Access-Control-Allow-Origin:*');
/*        $_POST[] = $this->security->xss_clean($_POST);*/
        $data_json = $this->input->post_get('data');
/*      $data_json = '{"name":"宿晨","nickname":"suchot","idcard":"1123123123123123","qq":"123123123123","phone":"12312312312","email":"12312312312","extra_filed1":"http://www.baoming11.com/uploads/1902/155133245595.png","game_id":"12312312312"}';*/
        $data = json_decode($data_json,true);
        
        $data['tid'] = $tid;
        $data['createtime'] = time();

        $tournament_info = $this->tournament->get_info($tid);
        if($data['name'] && $data['phone']){
            if($tournament_info->need_num > $tournament_info->now_num){
                $find_parm = array('phone'=>$data['phone'],'tid'=>$tid);
                $applicant_info = $this->applicant->find($find_parm);
                if($applicant_info){
                    $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '该手机号码已报名'), parent::HTTP_OK);
                }else{
                    if($this->applicant->insert($data)){

                        $this->tournament->add_one_applicant($tid);
                        if($tid == 11){
                            $this->load->helper('send_sms');
                            //$this->load->library('mailer');
                            sendTemplateSMS($data['phone'], [$data['name']], "593869");
                        }


                        $this->response($this->getResponseData(parent::HTTP_OK, '报名成功'), parent::HTTP_OK);
                    }else{
                        $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '报名失败'), parent::HTTP_OK);
                    }  
                }  
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '报名人数已满'), parent::HTTP_OK);
            }
             
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }
}
