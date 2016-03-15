<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if($output['errorInfo']){?>
<div id="product_detail_wp"><?php echo $output['errorInfo'];?></div>
<?php }else{?>
<div class="buy_step1">
    <div class="buys1-cnt buys1-address-cnt">
        <h3 class="clearfix">收货人信息 <span class="btn-s btn-prink-s fright buys1-edit-address buys1-edit-btn">修改</span></h3>
        <ul class="buys-ycnt buys1-hide-detail">
             <li class="clearfix">
                 <span class="key fleft">姓名：</span>
                 <div class="value fleft" id="true_name"><?php echo $output['address_info']['true_name'];?></div>
             </li>
             <li class="clearfix">
                 <span class="key fleft">详细地址：</span>
                 <div class="value fleft" id="address"><?php echo $output['address_info']['address'];?></div>
             </li>
             <li class="clearfix">
                 <span class="key fleft">联系电话：</span>
                 <div class="value fleft" id="mob_phone"><?php echo $output['address_info']['mob_phone'];?></div>
             </li>
        </ul>
        <ul class="buys1-hide-list buys-ycnt hide">
             <li id="addresslist">
                 <label class="new-address clr-d94">
                     <input type="radio" name="address" value="0" class="address-radio"/>
                     使用新的地址信息
                 </label>
                 <div class="invoice-addcnt">
                     <div class="iadd-title">
                         收货人信息：
                     </div>
                     <div>
                         <p class="iadd-ip">姓名：<span class="opera-tips">(*必填)</span></p>
                         <p class="iadd-ip">
                             <input type="text" class="n-input h22 wp100" name="true_name" id="vtrue_name"/>
                         </p>
                         <p class="iadd-ip"> 手机号码:<span class="opera-tips">(*必填)</span></p>
                         <p class="iadd-ip">
                             <input type="text" class="n-input h22 wp100" name="mob_phone" id="vmob_phone"/>
                         </p>
                         <p class="iadd-ip"> 电话号码:</p>
                         <p class="iadd-ip">
                             <input type="text" class="n-input h22 wp100" name="tel_phone" id="vtel_phone"/>
                         </p>
                     </div>
                     <div class="iadd-title"> 地址信息：</div>                        
                     <div>         
                             <p class="iadd-ip">省份：<span class="opera-tips">(*必填)</span></p>              	
                         <p class="iadd-ip">
                                                     <select class="select-30" name="prov" id="vprov">
                                                                     <option value="">请选择...</option>
                                                     </select>
                         </p>
                         <p class="iadd-ip">城市：<span class="opera-tips">(*必填)</span></p>
                         <p class="iadd-ip">
                                                             <select class="select-30" name="city" id="vcity">
                                                                     <option value="">请选择...</option>
                                                     </select>   
                         </p>
                         <p class="iadd-ip"> 区县：<span class="opera-tips">(*必填)</span></p>
                         <p class="iadd-ip">
                                                     <select class="select-30" name="region" id="vregion">
                                                                     <option value="">请选择...</option>
                                                     </select>
                         </p>
                         <p class="iadd-ip"> 街道：<span class="opera-tips">(*必填)</span></p>
                         <p class="iadd-ip">
                             <textarea class="wp100 h40 normal-textarea" name="address" id="vaddress"></textarea>
                         </p>
                     </div>
                 </div>
                 <div class="error-tips"></div>
             </li>
             <li class="invoice_opeara">
                 <a href="javascript:void(0);" class="btn-prink save-address">保存地址信息</a>
             </li>
         </ul>
     </div>
    <div class="buys1-cnt">
        <h3 class="clearfix">商品清单<!--  <span class="btn-s btn-prink-s fright" onclick="javascript:history.go(-1);">去购物车</span> --> </h3>
        <ul class="buys-ytable mt10" id="goodslist_before">
            <li>
                <p class="buys-yt-tlt">店铺名称：官方店铺</p>
                <div class="buys1-pdlist">
                    <div class="clearfix">
                        <a href="index.php?act=pointprod&op=pinfo&id=<?php echo $output['pointprod_arr']['pgoods_id'];?>" class="img-wp">
                            <img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_POINTPROD.DS.str_ireplace('.', '_small.', $output['pointprod_arr']['pgoods_image']);?>">
                        </a>
                        <div class="buys1-pdlcnt">
                            <p><a href="index.php?act=pointprod&op=pinfo&id=<?php echo $output['pointprod_arr']['pgoods_id'];?>" class="buys1-pdlc-name"><?php echo $output['pointprod_arr']['pgoods_name'];?></a></p>
                            <p>兑换所需积分：<?php echo $output['pointprod_arr']['totlepoints'];?>分</p>
                            <p>兑换数量：<?php echo $output['pointprod_arr']['quantity'];?></p>
                        </div>
                    </div>
                </div>
             </li>
            <li class="bd-t-cc">
                <div class="buys-order-total">
                     所需总积分：<span id="total_points"><?php echo $output['pointprod_arr']['totlepoints'];?>分</span>
                </div>
            </li>
            <li>
                <a href="javascript:void(0);" class="post-order" id="buy_step2">兑换完成</a>
            </li>
        </ul>
    </div>
 </div>
<?php }?>
<input type="hidden" id="address_id" name="address_id" value="<?php echo $output['address_info']['address_id'];?>">
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/zepto.min.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/simple-plugin.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/buy_step1.js" id="dialog_js" charset="utf-8"></script>