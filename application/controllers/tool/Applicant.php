<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Applicant extends Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('applicant_model','applicant');
        $this->load->model('tournament_mail_model','tournament_mail');
        $this->load->model('fail_mail_model','fail_mail');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $tid = $this->input->get_post('tid')? $this->input->get_post('tid') : 1;
        $state = $this->input->get_post('state')? $this->input->get_post('state') : 0;
        $name = $this->input->get_post('name')? $this->input->get_post('name') : '';

        $offset = ($page - 1)*$num;        

        if($state == -1){
            $option = array('tid'=>$tid);
        }else{
            $option = array('tid'=>$tid,'state'=>$state);
        }
        if($name != ''){
            $option['name'] = $name;
        }
        $list = $this->applicant->get_list($offset,$num,$option);
        $count = $this->applicant->get_num($option);
        $return_arr = $this->getLayuiList(0,'报名列表',$count,$list);
        $this->response($return_arr);     
    }

    public function find(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $tid = $this->input->get_post('tid')? $this->input->get_post('tid') : 1;
        $name = $this->input->get_post('name')? $this->input->get_post('name') : 0;

        $offset = ($page - 1)*$num;        

        $option = array('tid'=>$tid,'name'=>$name);

        $list = $this->applicant->get_list($offset,$num,$option);
        $count = $this->applicant->get_num($option);
        $return_arr = $this->getLayuiList(0,'报名列表',$count,$list);
        $this->response($return_arr);     
    }

    
    public function update_batch(){
        $group = $this->input->get_post('group') ? $this->input->get_post('group') : '';
        $state = $this->input->get_post('state') ? $this->input->get_post('state') : 0;
        $aids = $this->input->get_post('aids') ? $this->input->get_post('aids') : '';
        $mail_id = $this->input->get_post('mail_id') ? $this->input->get_post('mail_id') : 0;

        if($mail_id != 0){
            $mail_info = $this->tournament_mail->get_info($mail_id);
        }

    	$this->load->library('mailer');
    	if($state > 0 && $aids != ''){
    		$return_data = array();
            $update_err_arr = array();
            $mail_err_arr = array();

            $update_data = array(
                'state' => $state,
                'dealtime' => time(),
                'group' => $group
            );

            $applicant_id_list = explode(',', $aids);
            foreach ($applicant_id_list as $key => $value) {                
                if($this->applicant->update($value,$update_data)){
                    $applicant_info = $this->applicant->get_info($value);
                    if($mail_id !=0){
                        $tmp_content = $mail_info->content;
                        $subject = $mail_info->title;
                        $to = $applicant_info->email;
                        $to_name = $applicant_info->name;
                        $applicant_arr = json_decode(json_encode($applicant_info),true);
                        preg_match_all('/\{.*?\}/', $tmp_content, $matches, PREG_OFFSET_CAPTURE);
                        foreach ($matches[0] as $key => $value) {
                            $vname = substr($value[0],1,-1);
                            $tmp_content = str_replace($value, $applicant_arr[$vname], $tmp_content);
                        }

                        $msg = $this->mailer->sendmail($to, $to_name, $subject, $tmp_content);
                        if($msg != "success"){
                            $mail_err_arr []= $applicant_info->name;    
                            $this->fail_mail->insert(array('applicant_id'=>$applicant_info->id,'tid'=>$applicant_info->tid,'tournament_mail_id'=>$mail_id,'state'=>$state,'detail'=>$msg));                        
                        }
                    }
                    if($applicant_info->tid == 11){
                        $this->load->helper('send_sms');
                        //$this->load->library('mailer');
                        sendTemplateSMS($applicant_info->phone, ["abc","60分钟"], "112552");
                    }
                }else{
                    $applicant_info = $this->applicant->get_info($value);
                    $update_err_arr []= $applicant_info->name;
                }
            }
            $return_data['更新失败'] = implode(',', $update_err_arr);
            $return_data['发送邮件失败'] = implode(',', $mail_err_arr);

            if(empty($return_data['更新失败']) && empty($return_data['发送邮件失败'])){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功',''), parent::HTTP_OK);    
            }else{
                if(!empty($return_data['更新失败'])){
                    $return_str = '更新失败：'.$return_data['更新失败'].';';   
                }
                if(!empty($return_data['发送邮件失败'])){
                    $return_str .= '发送邮件失败:'.$return_data['发送邮件失败'].'!';    
                }                
                
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, $return_str), parent::HTTP_OK);
            } 
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }



    public function update($id=0){
        die;
        $state = $this->input->get_post('state') ? $this->input->get_post('state') : 0;
        if($id > 0 && $state > 0){
            $update_data = array(
                'state' => $state,
                'dealtime' => time()
            );

            $applicant_info = $this->applicant->get_info($id);
            if($this->applicant_info->state == $state){
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '请勿重复提交'), parent::HTTP_OK);
            }else{
                if($this->applicant->update($id,$update_data)){
                    $msg = '';
                    if($applicant_info->tid == 3 && $state == 10){
                        $order_id = $this->applicant->get_num(array('tid'=>$applicant_info->tid,'state'=>10));
                        $order_id = $order_id - 1;
                        $abc_array = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P'];
                        $group_name = $abc_array[intval(floor($order_id/8))].'组'.(intval($order_id % 8)+1).'号';
                        $update_data2 = array('extra_filed2'=>$group_name);
                        $this->applicant->update($id,$update_data2);
                        $this->load->library('mailer');

                        $to = $applicant_info->email;
                        $to_name = $applicant_info->name;
                        $subject = 'Imba自走棋线下公开赛报名结果';
                        $body = '<!DOCTYPE html>
                            <html>
                            <head>
                             <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

                            <title>报名通过</title>
                            </head>
                            <body>
                            <P>亲爱的自走棋玩家'.$applicant_info->name.'，您好！</P>
                            <P>恭喜你成功报名《身临其境》Imba自走棋线下公开赛，您本次线下分组为：'.$group_name.'。</P>
                            <P>A-H组签到时间为6月8日中午12点半，I-P组签到时间为6月8日下午3点半，届时请携带本人身份证至比赛现场签到处核实信息。</P>
                            <P>请在6月5日18:00之前加入赛事选手QQ群，群号：574196968（验证信息为：Dota2数字ID），逾期将视作放弃报名。</P>
                            <P>预祝您在本次比赛中获得理想的成绩。</P>
                            </body>
                            </html>';
                        $msg = $this->mailer->sendmail($to, $to_name, $subject, $body);   
                    }else if($applicant_info->tid == 3 && $state == 5){
                        $order_id = $this->applicant->get_num(array('tid'=>$applicant_info->tid,'state'=>5));
                        $update_data2 = array('extra_filed2'=>$order_id);
                        $this->applicant->update($id,$update_data2);
                        if($order_id < 50){
                            $data_char = '6月8日下午1点';
                        }else{
                            $data_char = '6月8日下午4点';
                        }
                        $this->load->library('mailer');

                        $to = $applicant_info->email;
                        $to_name = $applicant_info->name;
                        $subject = 'Imba自走棋线下公开赛报名结果';
                        $body = '<!DOCTYPE html>
                            <html>
                            <head>
                             <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

                            <title>替补通过</title>
                            </head>
                            <body>
                            <P>亲爱的刀塔自走棋玩家'.$applicant_info->name.'，您好！</P>
                            <P>很遗憾您的报名顺序在128人之外，但您可在比赛当日（6月8日）凭报名信息前往比赛现场签到替补。</P>
                            <P>补签到时间为6月8日下午1点及6月8日下午4点。替补名额将在比赛当天由现场工作人员通知按实际现场情况按签到先后顺序分配，凡成功签到者即可获得IMBA电竞体验中心一个月上网卡。</P>
                            <P>有任何赛事相关疑问可以加入赛事替补选手QQ群，群号：691236737（验证信息为：Dota2数字ID）。</P>
                            <P>感谢您对本次赛事的支持。</P>
                            </body>
                            </html>';
                        $msg = $this->mailer->sendmail($to, $to_name, $subject, $body);   
                    }

                    $this->response($this->getResponseData(parent::HTTP_OK, '更改成功',$msg), parent::HTTP_OK);
                }else{
                    $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '更改失败'), parent::HTTP_OK);
                }  
            }

        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }



}
