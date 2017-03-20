<?php

namespace App\Http\Controllers;
use DB;
use App\Http\Requests;
use Illuminate\Http\Request;
class weixinController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


     //验证消息
  /*   public function api()
     {
         $echoStr = $_GET["echostr"];
         if($this->checkSignature()){
             echo $echoStr;
             exit;
         }
     }
     */
     //检查签名
     private function checkSignature()
     {
         $signature = $_GET["signature"];
         $timestamp = $_GET["timestamp"];
         $nonce = $_GET["nonce"];
         $token = "weixin";
         $tmpArr = array($token, $timestamp, $nonce);
         sort($tmpArr, SORT_STRING);
         $tmpStr = implode($tmpArr);
         $tmpStr = sha1($tmpStr);
         if($tmpStr == $signature){
             return true;

         }else{
             return false;
         }
     }


     //响应消息
     public function api()
      {
        //get post data, May be due to the different environments
       $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//php:input
       //写入日志  在同级目录下建立php_log.txt
//chmod 777php_log.txt(赋权) chown wwwphp_log.txt(修改主)
error_log(var_export($postStr,1),3,'php_log.txt');
//日志图片

         //extract post data
       if (!empty($postStr)){
               /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                  the best way is to check the validity of xml by yourself */
               libxml_disable_entity_loader(true);
                 $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
               $fromUsername = $postObj->FromUserName;
               $toUsername = $postObj->ToUserName;
               $keyword = trim($postObj->Content);
               $time = time();
               $textTpl = "<xml>
                           <ToUserName><![CDATA[%s]]></ToUserName>
                           <FromUserName><![CDATA[%s]]></FromUserName>
                           <CreateTime>%s</CreateTime>
                           <MsgType><![CDATA[%s]]></MsgType>
                           <Content><![CDATA[%s]]></Content>
                           <FuncFlag>0</FuncFlag>
                           </xml>";
               //订阅事件
               if($postObj->Event=="subscribe")
               {
                   $msgType = "text";
                   $contentStr = "欢迎关注安子尘，微信babyanzichen";
                   $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                   echo $resultStr;
               }


               //语音识别
               if($postObj->MsgType=="voice"){
                   $msgType = "text";
                   $contentStr = trim($postObj->Recognition,"。");
                   $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                   echo  $resultStr;
               }

               //自动回复
               if(!empty( $keyword ))
               {
                     $msgType = "text";
                   $contentStr = "小朋友你好！";
                   $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                   echo $resultStr;
               }else{
                   echo "Input something...";
               }

       }else {
           echo "";
           exit;
       }

      }

}
