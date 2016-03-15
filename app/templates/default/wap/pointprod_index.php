<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="content">
    <div class="categroy-cnt" id="categroy-cnt">
        <ul class="categroy-list">
            <li class="category-item">
                <a href="index.php?act=pointvoucher&op=vlist&key=<?php echo $_SESSION['key'];?>" class="category-item-a">
                    <div class="ci-fcategory-name">最新代金券</div>
                    <span class="grayrightarrow"></span>
                </a>
            </li>
        </ul>
        <div class="content" style="border-left: ">
            <div class="product-cnt">
                <div class="product_list">
                    <?php if (is_array($output['voucherlist']) && count($output['voucherlist'])) { ?>
                        <ul class="product-list">
                            <?php foreach ($output['voucherlist'] as $v) { ?>
                                <li class="pdlist-item" goods_id="<?php echo $v['voucher_t_id']; ?>">
                                    <a href="index.php?act=pointvoucher&op=vinfo&vid=<?php echo $v['voucher_t_id']; ?>&key=<?php echo $_SESSION['key'];?>">
                                    <span class="pdlist-iw-imgwp">
                                        <img src="<?php echo $v['voucher_t_customimg'] ?>" alt=""/>
                                    </span>
                                        <div class="pdlist-iw-cnt">
                                            <p class="pdlist-iwc-pdname">满¥<?php echo $v['voucher_t_limit']; ?>
                                                减¥<?php echo $v['voucher_t_price']; ?></p>

                                            <p class="pdlist-iwc-pdprice">
                                                代金券面值：<?php echo $v['voucher_t_price'] . "元"; ?></p>

                                            <p class=" pdlist-iwc-pdcomment  clearfix">
                                                <span class="fleft">积分：<?php echo $v['voucher_t_points']; ?>分</span>
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <div class="no-record"> 暂无记录</div>
                    <?php } ?>
                </div>
            </div>
            <a href="index.php?act=pointvoucher&op=vlist&key=<?php echo $_SESSION['key'];?>" class="category-item-a">
                <div class="ci-fcategory-text">更多代金券...</div>
            </a>
        </div>
        <ul class="category-list">
            <li class="category-item categroy-seciond-list">
                <a href="index.php?act=pointprod&op=plist&key=<?php echo $_SESSION['key'];?>" class="category-item-a">
                    <div class="ci-fcategory-name">最新兑换礼品</div>
                    <span class="grayrightarrow"></span>
                </a>
            </li>
        </ul>
        <div class="content">
            <div class="product-cnt">
                <div class="product_list">
                    <?php if (is_array($output['pointprod_list']) && count($output['pointprod_list'])) { ?>
                        <ul class="product-list">
                            <?php foreach ($output['pointprod_list'] as $v) { ?>
                                <li class="pdlist-item" goods_id="<?php echo $v['pgoods_id']; ?>">
                                    <a href="index.php?act=pointprod&op=pinfo&id=<?php echo $v['pgoods_id']; ?>&key=<?php echo $_SESSION['key'];?>">
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
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <div class="no-record"> 暂无记录</div>
                    <?php } ?>
                    <a href="index.php?act=pointprod&op=plist&key=<?php echo $_SESSION['key'];?>" class="category-item-a">
                        <div class="ci-fcategory-text">更多兑换礼品...</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/zepto.min.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo SHOP_TEMPLATES_URL;?>/js/simple-plugin.js" id="dialog_js" charset="utf-8"></script>