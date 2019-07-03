<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class Mailer {
 
    var $mail;
 
    public function __construct()
    {
    	
    	//require_once('PHPMailer-5.2.4/class.phpmailer.php');
        //$this->mail = new PHPMailer(true);
        require_once('PHPMailer-6.0.6/src/PHPMailer.php');
        require_once('PHPMailer-6.0.6/src/SMTP.php');
		
 
        // the true param means it will throw exceptions on errors, which we need to catch
        $this->mail = new PHPMailer\PHPMailer\PHPMailer(true);
 
        $this->mail->IsSMTP(); // telling the class to use SMTP
 
        $this->mail->CharSet = "utf-8";                  // 一定要設定 CharSet 才能正確處理中文
        $this->mail->SMTPDebug  = 0;                     // enables SMTP debug information
        $this->mail->SMTPAuth   = true;                  // enable SMTP authentication
        $this->mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $this->mail->Host       = "smtp.exmail.qq.com";      // sets GMAIL as the SMTP server
        $this->mail->Port       = 465;                   // set the SMTP port for the GMAIL server
        $this->mail->Username   = "suchen@imbatv.cn";// GMAIL username
        $this->mail->Password   = "Su_4309209";       // GMAIL password
        $this->mail->AddReplyTo('suchen@imbatv.cn', 'suchot');
        $this->mail->SetFrom('suchen@imbatv.cn', 'suchot');
    }
 
    public function sendmail($to, $to_name, $subject, $body){
        try{
            $this->mail->AddAddress($to, $to_name);
            $this->mail->IsHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
 
            $this->mail->Send();
            return "Message Sent OK</p>\n";
 
        } catch (phpmailerException $e) {
            return $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            return $e->getMessage(); //Boring error messages from anything else!
        }
    }
}
 
/* End of file mailer.php */
