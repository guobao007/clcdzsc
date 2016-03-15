<?php
/**
 * cms首页
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class wechatControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }

    //weixin消息
    public function indexOp(){
        $model=Model();       
        require_once(BASE_PATH.DS.'api/wechat/callback.php');
        $wx_config=$model->table('wx_config')->where(array('id'=>1))->find();
        define("TOKEN", $wx_config['token']);
        $wechatObj = new wechatCallbackapiTest();
        $wechatObj->valid();      
        $wechatObj->responseMsg();
  }
   //weixin网页授权获取用户基本信息
    public function webcodeOp(){   

        $model=Model();    
        $state='STATE';   
        $url=str_replace('/wap','',$_GET['url']);
        $wx_config=$model->table('wx_config')->where(array('id'=>1))->find();
      
        $redirect_uri = urlencode(MOBILE_SITE_URL."/index.php?act=wechat&op=webuserinfo&url=".$url);
        $wxurl="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$wx_config['appid']}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
	
    
      
        //判断是否用微信端打开
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) { 
           header("Location:".$wxurl);
        }else{
           header("Location:".WAP_SITE_URL.$url."?islogin=1");
        }
    }
    
  
    
    //weixin网页授权获取用户基本信息
    public function webuserinfoOp(){
       
    
        $model=Model();          
        $wx_config=$model->table('wx_config')->where(array('id'=>1))->find();
        $appid =$wx_config['appid'];
        $secret = $wx_config['appsecret'];
        $code = $_GET["code"];
       
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        
        $res=$this->curl_get_contents($get_token_url);
        $json_obj = json_decode($res,true);       
        
        //根据openid和access_token查询用户信息
        $access_token = $json_obj['access_token'];
        $openid = $json_obj['openid'];
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $res=$this->curl_get_contents($get_user_info_url);        
        //解析json
        $user_obj = json_decode($res,true);
        //读取App微信登陆接口
        $post_data=array('openid'=>$user_obj['openid'],'wxuser_info'=>$res,'client'=>'weixin');
        $tokey=$this->curl_post_contents(MOBILE_SITE_URL."/index.php?act=login&op=wxlogin",$post_data);
   
        $tokey=json_decode($tokey,true);
        	
       //  echo $tokey;
           
          header("Location:".WAP_SITE_URL."/tmpl/member/login_wx.html?islogin=1&key=".$tokey['datas']['key']."&url=".$_GET['url']);
       
                
    }
    
    
    function curl_get_contents($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
      // curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
       // curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
   function curl_post_contents($url,$post_data){
         //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据     
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
       return $data;
   } 
    
}