<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if($output['errorInfo']){?>
<div id="product_detail_wp"><?php echo $output['errorInfo'];?></div>
<?php }else{?>
<div id="product_detail_wp">
    <div id="product_detail_wp"><div class="content">
            <div class="pddetail-cnt">
                <div class="pddc-topwp">
                    <a class="pddct-imgwp" href="javascript:void(0);">
                        <div class="swiper-container" id="mySwipe" style="visibility: visible;">
                            <div class="swipe-wrap" style="width: 1280px;">

                                <div class="swipe-item" style="width: 320px; left: 0px; transition-duration: 0ms; transform: translateX(0px);" data-index="0"><img src="<?php echo $output['prodinfo']['pgoods_image'];?>"></div>

                            </div>
                        </div>
                        <div class="pddct-shadow"></div>
                        <div class="pddct-name-wp">
                            <div class="pddctnw-name">
                                <?php echo $output['prodinfo']['pgoods_name'];?>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="pddc-gray-warp">
                    <a class="pddetail-go-title clearfix">
                        <span class="pgt-title fleft">
                            礼品介绍：
                        </span>
                    </a>
                    <a class="pddetail-go-title clearfix">
                        <span class="pgt-title fleft">
                            <?php echo $output['prodinfo']['pgoods_body'];?>
                        </span>
                    </a>
                </div>
                <div class="pddc-property-one pddc-gray-warp">
                    <div class="pddcp-one-wp ppdc-white-wrap">
                        <div class="pddcp-one-top">
                            <ul>
                                <li class="clearfix">
                                    <span class="key">兑换所需：</span>
                                    <div class="price value"><?php echo $output['prodinfo']['pgoods_points'];?>分</div>
                                </li>
                                <li class="clearfix">
                                    <span class="key">市场价格：</span>
                                    <div class="value">￥<?php echo $output['prodinfo']['pgoods_price'];?></div>
                                </li>
                                <li class="clearfix">
                                    <span class="key">礼品编号：</span>
                                    <div class="value"><?php echo $output['prodinfo']['pgoods_serial'];?></div>
                                </li>
                            </ul>
                        </div>
<!--                        <div class="pddcp-one-hide">
                            <div class="clearfix">
                                <span class="key">商品描述：</span>
                            </div>
                            <p>

                                新款特惠

                            </p>
                        </div>-->
                    </div>
                </div>
                <div class="pddc-gray-warp">
                    <ul class="pddc-stock ppdc-white-wrap">
                        <li class="pddc-stock-title clearfix">
                            <span class="key">剩余数量：</span>
                            <div class="price value">
                                <span class="stock-num"><?php echo $output['prodinfo']['pgoods_storage'];?></span>
                                件
                            </div>
                        </li>
                        <?php if($output['prodinfo']['pgoods_islimit']){?>
                        <li class="pddc-stock-title clearfix">
                            <span class="key">每人限购：</span>
                            <div class="price value">
                                <span class="stock-num"><?php echo $output['prodinfo']['pgoods_limitnum'];?></span>
                                件
                            </div>
                        </li>
                        <?php }?>

                        <li class="bd-tdashed-dd">
                            <span class="key-no">
                                数量：
                            </span>
                            <div class="value-no mt10 clearfix">
                                <span class="minus-wp fleft">
                                    <span class="i-minus"></span>
                                </span>
                                <input type="text" value="1" id="buynum" class="buy-num fleft">
                                <span class="add-wp fleft">
                                    <span class="i-add"></span>
                                </span>
                            </div>
                        </li>
                        <li class="bd-tdashed-dd">
                            <div class="opera-product-wp">
                                <?php if($output['ex_state'] == 'end'){?>
                                <div class="opera-pd-item buy-off">兑换结束</div>
                                <?php }else{?>
                                <div class="opera-pd-item buy-now">我要兑换</div>
                                <?php }?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/zepto.min.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/simple-plugin.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript">
    $(function (){
        //是否限购
        var islimit = <?php echo $output['prodinfo']['pgoods_islimit'];?>;
        //限购数量
        var limitnum = <?php echo $output['prodinfo']['pgoods_limitnum'];?>;
        //购买数量，减
        $(".minus-wp").click(function (){
           var buynum = $(".buy-num").val();
           if(buynum >1 && !islimit && limitnum == 0){
              $(".buy-num").val(parseInt(buynum-1));
           }
        });
        //购买数量加
        $(".add-wp").click(function (){
           var buynum = parseInt($(".buy-num").val());
           if(buynum < <?php echo $output['prodinfo']['pgoods_storage'];?> && !islimit && limitnum == 0){
              $(".buy-num").val(parseInt(buynum+1));
           }
        });
        //验证购买数量是不是数字
        $("#buynum").blur(buyNumer);
        
        //检测商品数目是否为正整数
        function buyNumer(){
            $.sValid();
        }
        $.sValid.init({
            rules:{
                buynum:"digits"
            },
            messages:{
                buynum:"请输入正确的数字"
            },
            callback:function (eId,eMsg,eRules){
                var buynum = $(".buy-num").val();
                if(eId.length >0){
                    var errorHtml = "";
                    $.map(eMsg,function (idx,item){
                        errorHtml += "<p>"+idx+"</p>";
                    });
                    $.sDialog({
                        skin:"red",
                        content:errorHtml,
                        okBtn:false,
                        cancelBtn:false
                    });
                }
                if(islimit && buynum > limitnum){
                    $.sDialog({
                        skin:"red",
                        content:"<p>每人限购"+limitnum+"件</p>",
                        okBtn:false,
                        cancelBtn:false
                    });
                }
            }  
        });
        //立即兑换
        $(".buy-now").click(function (){
            var buynum = $(".buy-num").val();
            if(islimit && buynum > limitnum){
                $.sDialog({
                    skin:"red",
                    content:"<p>每人限购一件</p>",
                    okBtn:false,
                    cancelBtn:false
                });
                return false;
            }
            var json = {};
            var pgoods_id = <?php echo $output['prodinfo']['pgoods_id'];?>;
            var buynum = $('.buy-num').val();
            location.href = '<?php echo APP_SITE_URL.'/index.php?act=pointcart&op=step1'?>&pgid='+pgoods_id+'&buynum='+buynum+'&key=<?php echo $_SESSION['key'];?>';
        });
    });
</script>
<?php }?>