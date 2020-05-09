<?php
/*
 *  Copyright (c) 2014 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.yuntongxun.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */

require_once __MYDIR__.'/application/libraries/CCPRestSmsSDK.php';

//主帐号,对应开官网发者主账号下的 ACCOUNT SID
$accountSid= 'aaf98f894f16fdb7014f1baabe7a0697';

//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
$accountToken= 'd43e9a2c1ab3447ab8c38550e9340910';

//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
$appId='aaf98f894f16fdb7014f1bc01efe06bf';

//请求地址
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
$serverIP='https://app.cloopen.com';
// $serverIP='https://sandboxapp.cloopen.com';


//请求端口，生产环境和沙盒环境一致
$serverPort='8883';

//REST版本号，在官网文档REST介绍中获得。
$softVersion='2013-12-26';


/**
  * 发送模板短信
  * @param to 手机号码集合,用英文逗号分开
  * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
  * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
  */       
function sendTemplateSMS($to,$datas,$tempId)
{
     // 初始化REST SDK
     // $accountSid= 'aaf98f894f16fdb7014f1baabe7a0697';测试
  $accountSid = 'aaf98f894f73de3f014f823c2492083d';

//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
//$accountToken= 'd43e9a2c1ab3447ab8c38550e9340910';测试
  $accountToken= 'f251e6c0e97b4094ace423defc64a920';


//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
$appId='8a48b5514f73ea32014f824130d3175f';

//请求地址
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
// $serverIP='sandboxapp.cloopen.com';
  $serverIP='app.cloopen.com';

//请求端口，生产环境和沙盒环境一致
$serverPort='8883';

//REST版本号，在官网文档REST介绍中获得。
$softVersion='2013-12-26';
     $rest = new REST($serverIP,$serverPort,$softVersion);
     $rest->setAccount($accountSid,$accountToken);
     $rest->setAppId($appId);
    
     // 发送模板短信
     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
     if($result == NULL ) {
         return false;
     }
     if($result->statusCode!=0) {
        return false;
     }else{
         return true;
     }
}

?>
