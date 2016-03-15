<?php
/**
 * 前台登录 退出操作
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class loginControl extends mobileHomeControl {

	public function __construct(){
		parent::__construct();
	}

	/**
	 * 登录
	 */
	public function indexOp(){
        if(empty($_POST['username']) || empty($_POST['password']) || !in_array($_POST['client'], $this->client_type_array)) {
            output_error('登陆失败');
        }

		$model_member = Model('member');

        $array = array();
        $array['member_name']	= $_POST['username'];
        $array['member_passwd']	= md5($_POST['password']);
        $member_info = $model_member->getMemberInfo($array);

        if(!empty($member_info)) {
            $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
            if($token) {
                output_data(array('username' => $member_info['member_name'], 'key' => $token));
            } else {
                output_error('登陆失败');
            }
        } else {
            output_error('用户名密码错误');
        }
    }

    /**
     * 登陆生成token
     */
    private function _get_token($member_id, $member_name, $client) {
        $model_mb_user_token = Model('mb_user_token');

        //重新登陆后以前的令牌失效
        //暂时停用
        //$condition = array();
        //$condition['member_id'] = $member_id;
        //$condition['client_type'] = $_POST['client'];
        //$model_mb_user_token->delMbUserToken($condition);

        //生成新的token
        $mb_user_token_info = array();
        $token = md5($member_name . strval(TIMESTAMP) . strval(rand(0,999999)));
        $mb_user_token_info['member_id'] = $member_id;
        $mb_user_token_info['member_name'] = $member_name;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = $_POST['client'];
        $mb_user_token_info['device_tokens'] = $_POST['device_tokens'];   
        

       
			//查询手机标识码    
			$model=model();
			$data=array();
			$data['token']=$token;
			$data['login_time'] = TIMESTAMP;
			
			$condition=array();
			$condition['member_id']=$member_id;
			$condition['client_type']=$_POST['client'];
			if($_POST['client']=='android' or $_POST['client']=='ios'){
			   $condition['device_tokens']=$_POST['device_tokens'];
			}
			$result=$model_mb_user_token->getMbUserTokenInfo($condition);
			if(!empty($result)){
				$model->table('mb_user_token')->where($condition)->update($data); 
			}else{
				 $result = $model_mb_user_token->addMbUserToken($mb_user_token_info);
			}      
        
        if($result) {
            return $token;
        } else {
            return null;
        }

    }

	/**
     * 手机验证
	*/
public function phonecodeOp(){
		$num = 6;
		$str = "0123456789";
		$code = '';
		for ($i = 0; $i < $num; $i++) {
			$code .= $str[mt_rand(0, strlen($str)-1)];
		} 
		$datetime=time()+PHONE_CODE_TIME;	
		$phonenum = $_POST['username'];  
		if(empty($phonenum)){			
			output_error('请输入手机号');
		}
		
		if(!empty($phonenum) && preg_match("/^[1][358]\d{9}$/",$phonenum)== false){			
			output_error('手机格式不正确，请重新输入');
		}
        $phone = Model('member');
		$phone_info = $phone->where("member_phone='$phonenum'")->select();
		if($phone_info){
			output_error('手机号已注册，请重新填写新的号码');			
		} 
		$msg="您好，您收到的验证码为:".$code.'。http://www.ahhoke.com。';
		$msgs= iconv("UTF-8","gb2312",$msg);
		if(preg_match("/^[1][4358]\d{9}$/",$phonenum)== true){			          
		     $this->sendmsgs($phonenum,$msgs);
			 output_data(array('username' => $phonenum, 'code' => $code));
		}
		
		
		
}
/**
*找回密码 短信认证
**/
public function app_passcodeOp(){
        $member_model	= Model('member');
		$num = 6;
		$str = "0123456789";
		$code = '';
		for ($i = 0; $i < $num; $i++) {
			$code .= $str[mt_rand(0, strlen($str)-1)];
		} 
		$datetime=time()+PHONE_CODE_TIME;	
		$phonenum = $_POST['username'];  
		if(empty($phonenum)){			
			output_error('请输入手机号');
		}
		
		if(!empty($phonenum) && preg_match("/^[1][358]\d{9}$/",$phonenum)== false){			
			output_error('手机格式不正确，请重新输入');
		}
        $phone = Model('member');
		$phone_info = $phone->where("member_phone='$phonenum' or member_name='$phonenum'")->find();
		if($phone_info){
				$msg="您好，您收到的验证码为:".$code.'。http://www.ahhoke.com。';
        		$msgs= iconv("UTF-8","gb2312",$msg);
        		if(preg_match("/^[1][4358]\d{9}$/",$phonenum)== true){			          
        		     $this->sendmsgs($phonenum,$msgs);
                     $member_model->updateMember(array('app_code'=>$code),$phone_info['member_id']);
        			 output_data(array('username' => $phonenum, 'code' => $code));
        		}			
		}else{		  
          output_error('手机号未注册，请重新填写新的号码');
		} 	
		
		
}

public function app_pwdOp() {	
    
        $member_model	= Model('member');
		$member	= $member_model->infoMember(array('member_name'=>$_POST['username'],'member_phone'=>$_POST['username']));
		if(empty($member) or !is_array($member)){		   
		   output_error('用户名(手机)不存在');
		}else{		  
          $app_code=$member["app_code"];
		}    
		$phonecaptcha = $_POST['code'];
		if($phonecaptcha !== $app_code){		
		  output_error('用户名(手机)验证码填写不正确,请重新发送.');
		}      
        
		if($member_model->updateMember(array('member_passwd'=>md5($_POST[' password']),'app_code'=>""),$member['member_id'])){
		  output_data('1');
		}
		 
        
		

}



/**
*短信接口
**/
	function rstr($str){
  
		if($str==0){
		  $error='验证码已发送到你的手机 请查收';
		}else{
		   switch($str){
		   case -1:$error='帐号未注册';break;
		   case -2:$error='其他错误';break;
		   case -3:$error='密码错误';break;
		   case -4:$error='手机号格式不对';break;
		   case -5:$error='余额不足';break;
		   case -6:$error='定时发送时间不是有效的时间格式';break;
		   case -8:$error='非法短信内容';break;
		   case -9:$error='未知系统故障';break;
		   case -10:$error='网络性错误';break;
		   default:$error='未知的错误';
		  } 
		}
		return $error;
		
	}
	function sendmsgs($phonenum,$msg){
		Language::read("home_login_register");
		$lang	= Language::getLangContent();
		$url = "http://www.106551.com/ws/Send.aspx?CorpID=YXS02282&Pwd=785623&Mobile=".$phonenum."&Content=".$msg;
		$string = $this->curl_file_get_contents($url);
		return  $this->rstr($string);
	}
	function curl_file_get_contents($durl){  
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $durl);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回    
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回    
		$r = curl_exec($ch);  
		curl_close($ch);  

	}


	/**
	 * 注册
	 */
	public function registerOp(){
		$model_member	= Model('member');

        $register_info = array();
        $register_info['username'] = $_POST['username'];
        $register_info['phone']    = $_POST['username'];
        $register_info['password'] = $_POST['password'];
        $register_info['password_confirm'] = $_POST['password_confirm'];
        $register_info['email'] = $_POST['email'];

        $member_info = $model_member->register($register_info);
        if(!isset($member_info['error'])) {
            $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
            if($token) {
                output_data(array('username' => $member_info['member_name'], 'key' => $token));
            } else {
                output_error('注册失败');
            }
        } else {
			output_error($member_info['error']);
        }

    }
  
   /**
     * QQ注册
    */
    public function qqregisterOp(){
	        //实例化模型
	        $model_member	= Model('member');
	        
  
       	 $qqopnid=$_POST['openid'];	
	  	 $info = $_POST['qquser_info'];    
         $info=str_replace("&quot;", "\"",$info);
         $qquser_info = json_decode($info, true);           
           // $qquser_info = getGBK($qquser_info,CHARSET);
	  	 
		 $user_passwd = rand(100000, 999999);
	
         
			/**
			 * 会员添加
			 */
			$user_array	= array();
            if($_POST['client']=='android'){
                $qquser_info['nickname'] = trim($qquser_info['nickname']);
                $user_array['member_name']		= $qquser_info['nickname'];                
            }elseif($_POST['client']=='ios'){
                $qquser_info['screen_name'] = trim($qquser_info['screen_name']);
                $user_array['member_name']		= $qquser_info['screen_name'];                
            }else{
                 output_error('0');
            }
		    $user_array['member_qqopenid']	= $qqopnid;
			$user_array['member_passwd']	= $user_passwd;
			$user_array['member_email']		= 'qq@qq.com';
			 //qq openid            
            
			$user_array['member_qqinfo']	= serialize($qquser_info);//qq 信息
           
			$rand = rand(100, 899);
			if(strlen($user_array['member_name']) < 3) $user_array['member_name']		= $qquser_info['nickname'].$rand;
			$check_member_name	= $model_member->infoMember(array('member_name'=>trim($user_array['member_name'])));
			$result	= 0;
			if(empty($check_member_name)) {
			 
             
			  $result	= $model_member->addMember($user_array);
              
			}else {
			   
            
              
				$user_array['member_name'] = trim($qquser_info['nickname']).$rand;
				$check_member_name	= $model_member->infoMember(array('member_name'=>trim($user_array['member_name'])));
				if(empty($check_member_name)) {
					$result	= $model_member->addMember($user_array);
				}else {
					for ($i	= 1;$i < 999999;$i++) {
						$rand = $rand+$i;
						$user_array['member_name'] = trim($qquser_info['nickname']).$rand;
						$check_member_name	= $model_member->infoMember(array('member_name'=>trim($user_array['member_name'])));
						if(empty($check_member_name)) {
							$result	= $model_member->addMember($user_array);
							break;
						}
					}
				}
			}
            
			if($result) {				
				$avatar	= @copy($qquser_info['figureurl_qq_2'],BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR."/avatar_$result.jpg");
				$update_info	= array();
				if($avatar) {
				    $update_info['member_avatar'] 	= "avatar_$result.jpg";
    				$model_member->updateMember($update_info,$result);
    				$user_array['member_avatar'] 	= "avatar_$result.jpg";
				}
				$user_array['member_id']		= $result;
                $token = $this->_get_token($user_array['member_id'], $user_array['member_name'], $_POST['client']);
                if($token) {
                   output_data(array('username' => $user_array['member_name'], 'key' => $token));
                } else {
                  output_error('0');
                }

			} else {
				 output_error('0');
			}
	
	}


	public function qqloginOp(){
	   
		//查询是否已经绑定该qq,已经绑定则直接跳转
		$model_member	= Model('member');
		$array	= array(); 
		$array['member_qqopenid']	= $_POST['openid'];
		$member_info = $model_member->infoMember($array);       
		if (is_array($member_info) && count($member_info)>0){
			if(!$member_info['member_state']){//1为启用 0 为禁用
				output_error('此账号已禁用');
			}
			$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
                if($token) {
                   output_data(array('username' => $member_info['member_name'], 'key' => $token));
                } else {
                  output_error('登录失败');
            }
		}else{		  
            output_error('0'); //返回0 跳转注册页面
		}
	}
    

    
    public function wxloginOp(){
	   
		//查询是否已经绑定该weixin,已经绑定则直接跳转
		$model_member	= Model('member');
		$array	= array(); 
		$array['member_wxopenid']	= $_POST['openid'];
        if(empty($array['member_wxopenid'])){ output_error('1获取微信失败');}
         
       	$member_info = $model_member->infoMember($array);       
		if (is_array($member_info)){
			if(!$member_info['member_state']){//1为启用 0 为禁用
				output_error('2此账号已禁用');
			}
           
			$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
                if($token) {
                   output_data(array('username' => $member_info['member_name'], 'key' => $token));
                } else {
                  output_error('3登录失败');
            }
           
		}else{		  
          
              
             $wxopenid=$_POST['openid'];	
    	  	 $info = $_POST['wxuser_info'];    
             $info=str_replace("&quot;", "\"",$info);
             $wxuser_info = json_decode($info, true);           
             //$wxuser_info = getGBK($wxuser_info,CHARSET);	  	 
    		 $user_passwd = rand(100000, 999999);
             
		  	 /**
			 * 会员添加
			 */
			$user_array	= array();
            $wxuser_info['nickname'] = trim($wxuser_info['nickname']);
            $user_array['member_name']		= $wxuser_info['nickname'];
            
		    $user_array['member_wxopenid']	= $wxopenid;
			$user_array['member_passwd']	= $user_passwd;
			$user_array['member_email']		= '@qq.com';
			$user_array['member_wxinfo']	= serialize($wxuser_info);//qq 信息
        
			$rand = rand(100, 899);
			if(strlen($user_array['member_name']) < 3) $user_array['member_name']	= $wxuser_info['nickname'].$rand;
			$check_member_name	= $model_member->infoMember(array('member_name'=>trim($user_array['member_name'])));
			$result	= 0;
			if(empty($check_member_name)) {		              
			  $result	= $model_member->addMember($user_array);              
			}else {
			   
            
              
				$user_array['member_name'] = trim($wxuser_info['nickname']).$rand;
				$check_member_name	= $model_member->infoMember(array('member_name'=>trim($user_array['member_name'])));
				if(empty($check_member_name)) {
					$result	= $model_member->addMember($user_array);
				}else {
					for ($i	= 1;$i < 999999;$i++) {
						$rand = $rand+$i;
						$user_array['member_name'] = trim($wxuser_info['nickname']).$rand;
						$check_member_name	= $model_member->infoMember(array('member_name'=>trim($user_array['member_name'])));
						if(empty($check_member_name)) {
							$result	= $model_member->addMember($user_array);
							break;
						}
					}
				}
			}
            
			if($result) {				
				$avatar	= @copy($wxuser_info['headimgurl'],BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR."/avatar_$result.jpg");
				$update_info	= array();
				if($avatar) {
				    $update_info['member_avatar'] 	= "avatar_$result.jpg";
    				$model_member->updateMember($update_info,$result);
    				$user_array['member_avatar'] 	= "avatar_$result.jpg";
				}
				$user_array['member_id']		= $result;
                $token = $this->_get_token($user_array['member_id'], $user_array['member_name'], $_POST['client']);
                if($token) {
                   output_data(array('username' => $user_array['member_name'], 'key' => $token));
                } else {
                  output_error('1');
                }

			 } else {
				 output_error('2');
			}
            
            
            
		}
	}

}
