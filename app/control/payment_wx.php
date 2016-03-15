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

class payment_wxControl extends mobileHomeControl{

	public function __construct() {
		parent::__construct();
	}



public function wxpayOp(){
    
   
     
     
    ini_set('date.timezone','Asia/Shanghai');
    //error_reporting(E_ERROR);
    require_once(BASE_PATH.DS."api/payment/wxpayv3/lib/WxPay.Api.php");
    require_once(BASE_PATH.DS."api/payment/wxpayv3/unit/WxPay.JsApiPay.php");

     $model_order = Model();
     $pay_sn=$_GET['pay_sn'];
     $order_list = $model_order->table('order')->where(array('pay_sn'=>$pay_sn,'order_state'=>ORDER_STATE_NEW))->select();  
     $pay_amount_online = 0;
     //订单总支付金额 
     foreach ($order_list as $key => $order_info) {				
                //计算相关支付金额
                $pay_amount_online += ncPriceFormat(floatval($order_info['order_amount'])-floatval($order_info['pd_amount']));
      }
    

    //获取用户openid
    $tools = new JsApiPay();
    $openId = $tools->GetOpenid();

    //统一下单
    $input = new WxPayUnifiedOrder();
    $input->SetBody($pay_sn);
    $input->SetAttach($pay_sn);
    $input->SetOut_trade_no(date("mdHis").'2B'.$pay_sn);
    $input->SetTotal_fee($pay_amount_online*100);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("test");
    $input->SetNotify_url(MOBILE_SITE_URL."/api/payment/wxpay/notify_url.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openId);


    $order = WxPayApi::unifiedOrder($input);
      
   // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
    //$this->printf_info($order);

    $jsApiParameters = $tools->GetJsApiParameters($order);
    
    echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/>  <title>订单微信支付</title></head><body>';
    echo ' <script type="text/javascript">';
    echo 'function jsApiCall(){';
	echo 'WeixinJSBridge.invoke(';
	echo "'getBrandWCPayRequest',";
	echo $jsApiParameters.',';
	echo '		function(res){';
	echo '		WeixinJSBridge.log(res.err_msg);';
//	echo '		alert("A="+res.err_code+"B="+res.err_desc+"C="+res.err_msg);';
    echo 'if(res.err_msg == "get_brand_wcpay_request:ok" ){ }else{alert("支付失败")}';
    echo 'location.href="'.WAP_SITE_URL.'/tmpl/member/order_list.html";';
	echo '			}	); 	}';

	echo '	function callpay(){	';
	echo	'	if (typeof WeixinJSBridge == "undefined"){';
	echo '		    if( document.addEventListener ){';
	echo "		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);";
	echo '		    }else if (document.attachEvent){';
	echo "		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); ";
	echo "		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);";
	echo '		    }';
	echo '		}else{ ';
	echo '		    jsApiCall();';
	echo '		}}</script>';
    echo ' <table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr> <td align="center"> <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:30px">'.$pay_amount_online.'元</span>钱</b></font><br/><br/></td> </tr> <tr><td><div align="center"><button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button></div></td></tr></tbody></table>';
    
}


public function vr_wxpayOp(){
    ini_set('date.timezone','Asia/Shanghai');
    //error_reporting(E_ERROR);
    require_once(BASE_PATH.DS."api/payment/wxpayv3/lib/WxPay.Api.php");
    require_once(BASE_PATH.DS."api/payment/wxpayv3/unit/WxPay.JsApiPay.php");

    $model_payment = Model('payment');
    $order_sn = $_GET['pay_sn'];
    
    //重新计算所需支付金额
    $result = $model_payment->getVrOrderInfo($order_sn, $_SESSION['member_id']);
    
    if ($result['error']) {
        output_error($result['error']);
    }
    if ($result['order_state'] != ORDER_STATE_NEW || empty($result['pay_amount'])) {
        output_error('该订单不需要支付');
    }
    //订单总支付金额 
    $pay_amount_online = $result['pay_amount'];

    //获取用户openid
    $tools = new JsApiPay();
    $openId = $tools->GetOpenid();

    //统一下单
    $input = new WxPayUnifiedOrder();
    $input->SetBody($order_sn);
    $input->SetAttach($order_sn);
    $input->SetOut_trade_no(date("mdHis") . '2B' . $order_sn);
    $input->SetTotal_fee($pay_amount_online * 100);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("test");
    $input->SetNotify_url(MOBILE_SITE_URL . "/api/payment/wxpay/vr_notify_url.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openId);


    $order = WxPayApi::unifiedOrder($input);

    // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
    //$this->printf_info($order);

    $jsApiParameters = $tools->GetJsApiParameters($order);

    echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/>  <title>订单微信支付</title></head><body>';
    echo ' <script type="text/javascript">';
    echo 'function jsApiCall(){';
    echo 'WeixinJSBridge.invoke(';
    echo "'getBrandWCPayRequest',";
    echo $jsApiParameters . ',';
    echo '		function(res){';
    echo '		WeixinJSBridge.log(res.err_msg);';
//	echo '		alert("A="+res.err_code+"B="+res.err_desc+"C="+res.err_msg);';
    echo 'if(res.err_msg == "get_brand_wcpay_request:ok" ){ }else{alert("支付失败")}';
    echo 'location.href="' . WAP_SITE_URL . '/tmpl/member/vr_order_list.html";';
    echo '			}	); 	}';

    echo '	function callpay(){	';
    echo '	if (typeof WeixinJSBridge == "undefined"){';
    echo '		    if( document.addEventListener ){';
    echo "		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);";
    echo '		    }else if (document.attachEvent){';
    echo "		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); ";
    echo "		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);";
    echo '		    }';
    echo '		}else{ ';
    echo '		    jsApiCall();';
    echo '		}}</script>';
    echo ' <table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr> <td align="center"> <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:30px">' . $pay_amount_online . '元</span>钱</b></font><br/><br/></td> </tr> <tr><td><div align="center"><button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button></div></td></tr></tbody></table>';
}
 function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }

    public function wxpaysmOP(){
       ini_set('date.timezone','Asia/Shanghai');
   
       require_once(BASE_PATH.DS."api/payment/wxpayv3/lib/WxPay.Api.php");
       require_once(BASE_PATH.DS."api/payment/wxpayv3/unit/WxPay.NativePay.php");
       $notify = new NativePay();
         $model_order = Model();
         $pay_sn=$_GET['pay_sn'];
         $order_list = $model_order->table('order')->where(array('pay_sn'=>$pay_sn,'order_state'=>ORDER_STATE_NEW))->select();  
         $pay_amount_online = 0;
         //订单总支付金额 
         foreach ($order_list as $key => $order_info) {				
                    //计算相关支付金额
                    $pay_amount_online += ncPriceFormat(floatval($order_info['order_amount'])-floatval($order_info['pd_amount']));
          }   
          
        $input = new WxPayUnifiedOrder();
        $input->SetBody($pay_sn);
        $input->SetAttach($pay_sn);
        $input->SetOut_trade_no(date("mdHis").'2B'.$pay_sn);
        $input->SetTotal_fee($pay_amount_online*100);
        
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(MOBILE_SITE_URL."/api/payment/wxpay/notify_url.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id('123456');
    
        $result = $notify->GetPayUrl($input);
        $url = $result["code_url"];
      
        require_once BASE_PATH.DS.'api/payment/wxpayv3/unit/phpqrcode/phpqrcode.php';
        QRcode::png($url);
        
    }


}
?>