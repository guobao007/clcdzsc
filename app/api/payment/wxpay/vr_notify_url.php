<?php
/* *
 * 功能：支付宝服务器异步通知页面
 */


$_GET['act'] = 'payment_app';
$_GET['op']	= 'vr_wxnotify';
require_once(dirname(__FILE__).'/../../../index.php');
?>
