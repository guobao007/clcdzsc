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

                                <div class="swipe-item" style="width: 320px; left: 0px; transition-duration: 0ms; transform: translateX(0px);" data-index="0"><img src="<?php echo $output['voucher_info']['voucher_t_customimg'];?>"></div>

                            </div>
                        </div>
                        <div class="pddct-shadow"></div>
                        <div class="pddct-name-wp">
                            <div class="pddctnw-name">
                                <?php echo $output['voucher_info']['voucher_t_title'];?><br>
                                满<?php echo $output['voucher_info']['voucher_t_limit'];?>元
                                减<?php echo $output['voucher_info']['voucher_t_price'];?>元
                            </div>
                        </div>
                    </a>
                </div>
                <div class="pddc-gray-warp">
                    <a class="pddetail-go-title clearfix">
                        <span class="pgt-title fleft">
                            优惠店铺：<?php echo $output['voucher_info']['voucher_t_storename'];?>
                        </span>
                    </a>
                </div>
                <div class="pddc-property-one pddc-gray-warp">
                    <div class="pddcp-one-wp ppdc-white-wrap">
                        <div class="pddcp-one-top">
                            <ul>
                                <li class="clearfix">
                                    <span class="key">兑换所需：</span>
                                    <div class="price value"><?php echo $output['voucher_info']['voucher_t_points'];?>分</div>
                                </li>
                                <li class="clearfix">
                                    <span class="key">金额：</span>
                                    <div class="value">￥<?php echo $output['voucher_info']['voucher_t_price'];?></div>
                                </li>
                                <li class="clearfix">
                                    <span class="key">使用条件：</span>
                                    <div class="value">本店购物买<?php echo $output['voucher_info']['voucher_t_limit'];?>元</div>
                                </li>
                                <li class="clearfix">
                                    <span class="key">有效期：</span>
                                    <div class="value"><?php echo date('Y-m-d',$output['voucher_info']['voucher_t_end_date']);?></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="pddc-gray-warp">
                    <ul class="pddc-stock ppdc-white-wrap">
                        <li class="pddc-stock-title clearfix">
                            <span class="key">剩余数量：</span>
                            <div class="price value">
                                <span class="stock-num"><?php echo $output['voucher_info']['voucher_t_total']-$output['voucher_info']['voucher_t_giveout'];?></span>
                                张
                            </div>
                        </li>

                        <li class="bd-tdashed-dd">
                            <div class="opera-product-wp">
                                <?php if($output['voucher_info']['voucher_t_total']-$output['voucher_info']['voucher_t_giveout'] <= 0){?>
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
<?php }?>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/zepto.min.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/simple-plugin.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript">
    $(function (){
        //立即兑换
        $(".buy-now").click(function (){
            var vid = <?php echo $output['voucher_info']['voucher_t_id'];?>;
            $.ajax({
                type: 'post',
                url: ApiUrl + '/index.php?act=pointvoucher&op=voucherexchange_save&key=<?php echo $_SESSION['key'];?>',
                dataType: 'json',
                data: {vid:vid},
                success: function (result) {
                    var data = result.datas;
                    if (data.error) {
                        $.sDialog({
                            skin:"red",
                            content:"<p>"+data.msg+"</p>",
                            okBtn:false,
                            cancelBtn:false
                        });
                    }else{
                        $.sDialog({
                            autoTime: '5000',
                            skin:"red",
                            content:'<p>'+data.msg+'</p>',
                            okBtn:true,
                            cancelBtn:false,
                            "okFn": function() {
                                window.location.href = ApiUrl+'/index.php?act=pointprod&key=<?php echo $_SESSION['key'];?>';
                            }
                        });
                    }
                }
            });
        });
    });
</script>