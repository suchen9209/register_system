<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Applicant extends Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('applicant_model','applicant');

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

    
    public function update_all($tid=0){
        die;
    	$state = $this->input->get_post('state') ? $this->input->get_post('state') : 5;
    	$this->load->library('mailer');
    	if($tid > 0 && $state > 0){
    		$return_arr = array();
            $update_data = array(
                'state' => $state,
                'dealtime' => time()
            );

            $option = array('tid'=>$tid,'state'=>0);
            $list = $this->applicant->get_list(0,25,$option);
            foreach ($list as $key => $value) {
            	if($this->applicant->update($value['id'],$update_data)){
            		$return_arr[] = $value;
            		$to = $value['email'];
                    $to_name = $value['name'];
                    $subject = 'IMBA自走棋公开赛报名结果';
                    $body = '<!DOCTYPE html>
                        <html>
                        <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

                        <title>替补通过</title>
                        </head>
                        <body>
                        <P>亲爱的刀塔自走棋玩家'.$value['name'].'，您好！</P>
                        <P>　　很遗憾您的报名顺序在1024人之外，但您可加入赛事替补选手群，替补名额将在比赛当天由裁判通知，先到先得。</p>
                        <p>    请在3月23日18:00之前加入赛事替补选手QQ群，群号：<span>724081523</span>（验证信息为：Dota2数字ID）。</P>
                        <P>　　感谢您对本次赛事的支持并预祝您能收获理想的成绩。</P>
                        </body>
                        </html>';
                    $msg = $this->mailer->sendmail($to, $to_name, $subject, $body);
            	}
            }
            $this->response($this->getResponseData(parent::HTTP_OK, '更改成功',$return_arr), parent::HTTP_OK);

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

    public function set_group(){
        $aids = $this->input->get_post('aids') ? $this->input->get_post('aids') : 0;
        $group = $this->input->get_post('group') ? $this->input->get_post('group') : 0;
        //$group_order = $this->input->get_post('group_order') ? $this->input->get_post('group_order') : '';
        $aid_arr = explode(',', $aids);
        $update_data = array(
            'group_num' => $group
        );
        foreach ($aid_arr as $key => $value) {
            if($this->applicant->update($value,$update_data)){
                $success_arr []=$value;
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '更改'.$value.'失败'), parent::HTTP_OK);
            }
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '更改成功',$success_arr), parent::HTTP_OK);
    }

    public function update_and_mail(){
        $state = $this->input->get_post('state') ? $this->input->get_post('state') : 0;
        $aids = $this->input->get_post('aids') ? $this->input->get_post('aids') : 0;
        $mail_id = $this->input->get_post('mail_id') ? $this->input->get_post('mail_id') : 0;

        $aid_arr = explode(',', $aids);
        $update_data = array(
            'state' => $state,
            'dealtime' => time()
        );
        foreach ($aid_arr as $key => $value) {
            if($this->applicant->update($value,$update_data)){

            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '更改'.$value.'失败'), parent::HTTP_OK);
            }
        }
    }


}
