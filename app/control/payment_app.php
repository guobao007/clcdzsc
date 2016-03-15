<?php
/**
 * 支付回调
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

class payment_appControl extends mobileHomeControl{

	public function __construct() {
		parent::__construct();
	}

public function wxnotifyOp(){
    
     require_once(BASE_PATH.DS.'api/payment/wxpay/WxPay.Config.php');
     
     $model_order = Model('order');
     $model_payment = Model('payment');    
     $notify_POST = $GLOBALS['HTTP_RAW_POST_DATA']; 
     
     
     $xml = simplexml_load_string($notify_POST);//转换post数据为simplexml对象
     $notify_info=array();
     foreach($xml->children() as $child){
     $notify_info[$child->getName()]=$child   ;   
     }

    // 这里缺少个微信订单号transaction_id进行比对，判断微信端是否成功。后期添加
    
   if(!empty($notify_info) and is_array($notify_info)){ 
    
      if($notify_info['appid']==$WXAPPID and $notify_info['return_code']=="SUCCESS" ){      
   
        
          $pay_all=array();
          $pay_all=explode('2B',$notify_info['out_trade_no']);                         
          $order_list = $model_order->getOrderList(array('pay_sn'=>$pay_all[1],'order_state'=>ORDER_STATE_NEW));  
          $pay_amount_online = 0;
          //订单总支付金额 
           foreach ($order_list as $key => $order_info) {				
                //计算相关支付金额
                $pay_amount_online += ncPriceFormat(floatval($order_info['order_amount'])-floatval($order_info['pd_amount']));
           }
           $pay_amount_online=$pay_amount_online*100;
           //此代码直接回调，以微信实际支付的费用与订单实现在线支付费用做对比检验。（没有采用官方检验机制）
           //if($pay_amount_online==floatval($notify_info['total_fee'])){
            
               $result = $model_payment->updateProductBuy($pay_all[1], 'weixin', $order_list, $notify_info['transaction_id']);   
               if(empty($result['error'])) {
    			  echo "success";	              
                }else{
                   echo "fail";   
                }
          // }else{
               
            /*	$file = 'error.txt';
            	file_put_contents($file,date('y-s-d').'[]'.$pay_amount_online.'=='.floatval($notify_info['total_fee']));
            	if (! file_exists ( $dir )) {
            	mkdir ( $dir );
            	}
            	if(!file_exists($file)){	    //如果文件不存在（默认为当前目录下）
            	$fs = fopen($file,'w+');
            	}else{
            	$fs = fopen($file,'a+');
            	} 
             */
            // echo "fail";  
         //  } 
          
          
         
        }else{
          echo "fail"; 	            
        }     
    
   }else{    
      echo "fail";  
   }
    
    
}

public function vr_wxnotifyOp(){
    
     require_once(BASE_PATH.DS.'api/payment/wxpay/WxPay.Config.php');
     
     $model_order = Model('order');
     $model_payment = Model('payment');    
     $notify_POST = $GLOBALS['HTTP_RAW_POST_DATA']; 
     
     $notify_info = json_decode(json_encode(simplexml_load_string($notify_POST, 'SimpleXMLElement', LIBXML_NOCDATA)), true); 
     
     //判断是否支付成功
    if (!empty($notify_info) && is_array($notify_info)) {
            if ($notify_info['appid'] == $WXAPPID && $notify_info['return_code'] == "SUCCESS") {
                $pay_all = array();
                $pay_all = explode('2B', $notify_info['out_trade_no']);
                $order_pay_info = $model_payment->getVrOrderInfo($notify_info['attach']);
                if($order_pay_info['error']) {
                    echo "fail";
                }
                if ($order_pay_info['order_state'] != ORDER_STATE_NEW) {
                    echo "fail";
                }
                $result = $model_payment->updateVrOrder($pay_all[1], 'weixin', $order_pay_info, $notify_info['transaction_id']);
                if (!empty($result['error'])) {
                    echo "fail";
                }
                if (empty($result['error'])) {
                    echo "success";
                } else {
                    echo "fail";
                }
            } else {
                echo "fail";
            }
        } else {
            echo "fail";
        }
    }
//支付宝回调
public function notifyOp() {
      
      
        require_once(BASE_PATH.DS.'api/payment/alipay/alipay.config.php');
        require_once(BASE_PATH.DS.'api/payment/alipay/lib/alipay_notify.class.php');
        
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();     
        
        if($verify_result) {//验证成功
        	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        	//请在这里加上商户的业务逻辑程序代
        
        	
        	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        	
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        	
        	//商户订单号
        
        	$out_trade_no = $_POST['out_trade_no'];
        
        	//支付宝交易号
        
        	$trade_no = $_POST['trade_no'];
        
        	//交易状态
        	$trade_status = $_POST['trade_status'];
             $model_order = Model('order');
             $model_payment = Model('payment');
             $payment_code = 'alipay';
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
        		//判断该笔订单是否在商户网站中已经做过处理
        			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        			//如果有做过处理，不执行商户的业务程序
        				
        		//注意：
        		//该种交易状态只在两种情况下出现
        		//1、开通了普通即时到账，买家付款成功后。
        		//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        		//判断该笔订单是否在商户网站中已经做过处理
        			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        			//如果有做过处理，不执行商户的业务程序
        				
        		//注意：
        		//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        	if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
        	    $order_list = $model_order->getOrderList(array('pay_sn'=>$out_trade_no,'order_state'=>ORDER_STATE_NEW));
        	    $result = $model_payment->updateProductBuy($out_trade_no, $payment_code, $order_list, $trade_no);              
               
        	}       
            
            if(empty($result['error'])) {
			  echo "success";	
              
            }else{
               echo "fail";  
               
                
            }
        	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
              
        //	echo "success";		//请不要修改或删除
        	
        	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            
            //验证失败
            echo "fail";        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
      

}

    
}
