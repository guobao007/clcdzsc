<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $output['title'];?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/reset.css">
	<link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/child.css">
</head>
<script>
var ApiUrl = "http://ahxbgw.com/app";
var key = '<?php echo $_SESSION['key'];?>';
</script>
<body>
<?php if(!$output['hideTop']){?>
<header id="header">
    <div class="header-wrap">
        <a class="header-back" href="javascript:history.back();"><span>返回</span></a>
        <h2><?php echo $output['title'];?></h2>
    </div>
</header>
<?php }?>
<?php require_once($tpl_file);?>
<footer id="footer">
    <div class="footer">
        <div class="footer-top">
            <a class="gotop" href="javascript:void(0);"><span class="gotop-icon"></span><p>回顶部</p></a>
        </div>
    </div>
</footer>
<script>
//回到顶部
$(".gotop").click(function (){
    $(window).scrollTop(0);
});
</script>
</body>
</html>