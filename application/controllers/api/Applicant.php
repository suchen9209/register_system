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
                        if($tid == 2){
                            $this->load->library('mailer');

                            $to = $data['email'];
                            $to_name = $data['name'];
                            $subject = 'IMBA自走棋公开赛报名结果';
                            $body = '<!DOCTYPE html>
                                <html>
                                <head>
                                 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

                                <title>报名通过</title>
                                </head>
                                <body>
                                <P>亲爱的玩家，您好！</P>
                                <P>　　感谢您的报名，您已成功获得本次《一起来电竞》线下赛第一赛季的参赛资格，随机分组后会通知您前往指定的海选赛场进行海选小组赛，预祝您有好的表现！</P>
                                </body>
                                </html>';
                            $msg = $this->mailer->sendmail($to, $to_name, $subject, $body);   
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
