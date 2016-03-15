<?php defined('InShopNC') or exit('Access Invalid!');?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>团购详情</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/base.css">
    <link rel="stylesheet" type="text/css" href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_group.css">
    <link href="<?php echo SHOP_TEMPLATES_URL;?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
</head>
<script>
var ApiUrl = "http://shopnc/app";
var key = '<?php echo $_SESSION['key'];?>';
var tms = [];
var day = [];
var hour = [];
var minute = [];
var second = [];
function takeCount() {
    setTimeout("takeCount()", 1000);
    for (var i = 0, j = tms.length; i < j; i++) {
        tms[i] -= 1;
        //计算天、时、分、秒、
        var days = Math.floor(tms[i] / (1 * 60 * 60 * 24));
        var hours = Math.floor(tms[i] / (1 * 60 * 60)) % 24;
        var minutes = Math.floor(tms[i] / (1 * 60)) % 60;
        var seconds = Math.floor(tms[i] / 1) % 60;
        if (days < 0)
            days = 0;
        if (hours < 0)
            hours = 0;
        if (minutes < 0)
            minutes = 0;
        if (seconds < 0)
            seconds = 0;
        //将天、时、分、秒插入到html中
        document.getElementById(day[i]).innerHTML = days;
        document.getElementById(hour[i]).innerHTML = hours;
        document.getElementById(minute[i]).innerHTML = minutes;
        document.getElementById(second[i]).innerHTML = seconds;
    }
}
setTimeout("takeCount()", 1000);
</script>
    <body>
        <?php if(!$output['errorInfo']){?>
        <div class="ncg-container wrapper">
                <div class="ncg-layout-l">
                    <div class="ncg-main <?php echo $output['groupbuy_info']['state_flag']; ?>">
                        <div class="ncg-group">
                            <h2><?php echo $output['groupbuy_info']['groupbuy_name']; ?></h2>
                            <h3><?php echo $output['groupbuy_info']['remark']; ?></h3>
                            <div class="ncg-item">
                                <div class="pic"><img src="<?php echo gthumb($output['groupbuy_info']['groupbuy_image'], 'max'); ?>" alt=""></div>
                                <div class="clear"></div>
                            </div>
                            <div class="ncg-item">
                                <div class="button"><span>¥<em><?php echo $output['groupbuy_info']['groupbuy_price']; ?></em></span><a href="<?php echo WAP_SITE_URL.'/tmpl/product_dinfo.html?goods_id='.$output['groupbuy_info']['goods_id']; ?>" target="_blank"><?php echo $output['groupbuy_info']['button_text']; ?></a></div>
                                <div class="info" id="main-nav-holder">
                                    <div class="prices">
                                        <dl>
                                            <dt>原价</dt>
                                            <dd><del>¥<?php echo $output['groupbuy_info']['goods_price']; ?></del></dd>
                                        </dl>
                                        <dl>
                                            <dt>折扣</dt>
                                            <dd><em><?php echo $output['groupbuy_info']['groupbuy_rebate']; ?>折</em></dd>
                                        </dl>
                                        <dl>
                                            <dt>节省</dt>
                                            <dd><em>¥<?php echo sprintf("%01.2f", $output['groupbuy_info']['goods_price'] - $output['groupbuy_info']['groupbuy_price']); ?></em></dd>
                                        </dl>
                                    </div>
                                    <div class="trim"></div>
                                    <div class="require">
                                        <h4>本商品已被团购<em><?php echo $output['groupbuy_info']['virtual_quantity'] + $output['groupbuy_info']['buy_quantity']; ?></em>件</h4>
                                        <p>
                                            <?php if (!empty($output['groupbuy_info']['upper_limit'])) { ?>
                                                每人最多购买<em><?php echo $output['groupbuy_info']['upper_limit']; ?></em>件，
                                            <?php } ?>
                                            数量有限，欲购从速!</p>
                                    </div>
                                    <div class="time">
                                        <?php if (!empty($output['groupbuy_info']['count_down'])) { ?>
                                            <!-- 倒计时 距离本期结束 -->
                                            <i class="icon-time"></i>剩余时间：<span id="d1">0</span><strong>天</strong><span id="h1">0</span><strong>小时</strong><span id="m1">0</span><strong>分</strong><span id="s1">0</span><strong><?php echo $lang['text_second']; ?></strong>
                                            <script type="text/javascript">
                                                      tms[tms.length] = "<?php echo $output['groupbuy_info']['count_down']; ?>";
                                                      day[day.length] = "d1";
                                                      hour[hour.length] = "h1";
                                                      minute[minute.length] = "m1";
                                                      second[second.length] = "s1";
                                            </script>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>

                    <div class="ncg-title-bar">
                        <ul class="tabs-nav">
                            <li class="tabs-selected"><a href="javascript:void(0);">商品信息</a></li>
                            <li><a href="javascript:void(0);">购买记录</a></li>
                        </ul>
                    </div>
                    <div class="ncg-detail-content">
                        <div class="ncg-intro"><?php echo $output['groupbuy_info']['groupbuy_intro']; ?></div></div>
                    <div id="groupbuy_order" class="ncg-detail-content hide">
                    </div>
                </div>
            </div>
        <script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js" type="text/javascript"></script> 
        <script>
	//首页Tab标签卡滑门切换
        $(function(){
        $(".tabs-nav > li > a").live('mouseover', (function(e) {
                if (e.target == this) {
                        var tabs = $(this).parent().parent().children("li");
                        var panels = $(this).parent().parent().parent().parent().children(".ncg-detail-content");
                        var index = $.inArray(this, $(this).parent().parent().find("a"));
                        if (panels.eq(index)[0]) {
                                tabs.removeClass("tabs-selected").eq(index).addClass("tabs-selected");
                                panels.addClass("hide").eq(index).removeClass("hide");
                        }
                }
        }));

            $("#groupbuy_order").load('<?php echo urlShop('show_groupbuy', 'groupbuy_order', array('group_id' => $output['groupbuy_info']['groupbuy_id'],'is_vr'=>$output['groupbuy_info']['is_vr']));?>');
        });
        </script>
        <?php }else{?>
        <?php echo $output['errorInfo']?>
        <?php }?>
    </body>
</html>