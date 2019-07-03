<?php 
class Email_div_model extends CI_Model {



    public function __construct()
    {
        parent::__construct('email_div','id');
    }

    public function sendMail()
    {
        $this->load->library('email');  
        $config = array();
        $config['protocol']     = 'SMTP';  //邮件协议

        $config['smtp_host']    = 'smtp.exmail.qq.com';
        $config['smtp_user']    = 'suchen@imbatv.cn';
        $config['smtp_pass']    = 'Su_4309209';  //注意这个不是邮箱登陆密码，是SMTP授权码
        $config['smtp_port']    = '465';

        $config['charset']      = 'utf-8';
        $config['mailtype']     = 'html';
        $config['smtp_timeout'] = '5';
        $config['newline'] = "\r\n";
        $config['wordwrap'] = TRUE;     // 自动换行

        $this->email->initialize($config);
        $this->email->from ('suchen@imbatv.cn', 'suchot');
        $this->email->to ('709807030@qq.com', 'zzz');
/*        $this->email->cc ('cc@email.com'); //抄送
        $this->email->bcc ('bcc@email.com'); //秘密抄送*/
        $this->email->subject ('Test subject');
        $this->email->message ('The content'); //内容
/*        $this->email->attach('application\controllers\1.jpeg');  //附件，相对于index.php的路径*/
        $abc = $this->email->send(false);
        $this->email->print_debugger();
        var_dump($abc->email);
    }
 
 

    
}
?>