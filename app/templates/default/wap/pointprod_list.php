<?php defined('InShopNC') or exit('Access Invalid!');?>
<script>
var pagesize = 10;
var pUrl = "/index.php?act=pointprod&op=plist&curpage=";
</script>
<div class="content">
    <div class="product-cnt">
        <div class="product_list">
            <?php if (is_array($output['pointprod_list']) && count($output['pointprod_list'])){?>
            <ul class="product-list">
                <?php foreach ($output['pointprod_list'] as $v){?>
                <li class="pdlist-item" goods_id="<?php echo $v['pgoods_id'];?>">
                    <a href="index.php?act=pointprod&op=pinfo&id=<?php echo $v['pgoods_id'];?>&key=<?php echo $_SESSION['key'];?>">
                        <span class="pdlist-iw-imgwp">
                            <img src="<?php echo $v['pgoods_image'] ?>" alt=""/>
                        </span>
                        <div class="pdlist-iw-cnt">
                            <p class="pdlist-iwc-pdname"><?php echo $v['pgoods_name']; ?></p>
                            <p class="pdlist-iwc-pdprice">市场价格：<?php echo $v['pgoods_price']; ?></p>
                            <p class=" pdlist-iwc-pdcomment  clearfix">
                                <span class="fleft">积分：<?php echo $v['pgoods_points']; ?>分</span>
                            </p>
                        </div>
                    </a>
                </li>
                <?php }?>
            </ul>
            <?php } else {?>
            <div class="no-record"> 暂无记录 </div>
            <?php }?>
        </div>
        <div class="pagination mt10">
            <a href="javascript:void(0);" class="pre-page">上一页</a>
            <select name="page_list" style="padding: 7px 4px;  vertical-align: top;">
                
            </select>
            <a href="javascript:void(0);" class="next-page ">下一页</a>
        </div>
    </div>
</div>
<input type="hidden" name="page_total" value="<?php echo $output['pageInfo']['page_total'];?>">
<input type="hidden" name="hasmore" value="<?php echo $output['pageInfo']['hasmore'];?>">
<input type="hidden" name="curpage" value="<?php echo $output['curpage'];?>">
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/zepto.min.js"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/simple-plugin.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/product_list.js" id="dialog_js" charset="utf-8"></script>