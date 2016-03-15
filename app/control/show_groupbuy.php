<?php
/**
 * APP团购显示
 *
 * @author QT
 */
defined('InShopNC') or exit('Access Invalid!');
define('APP_SITE_URL', 'http://shopnc/app');
define('TPL_SHOP_NAME','default');
define('SHOP_TEMPLATES_URL',APP_SITE_URL.'/templates/'.TPL_SHOP_NAME);
class show_groupbuyControl extends mobileHomeControl{
    public function __construct() {
        parent::__construct();
        Tpl::setDir('wap');
        //检查团购功能是否开启
        if (intval($GLOBALS['setting_config']['groupbuy_allow']) !== 1){
            Tpl::output('errorInfo','团购功能未开启');
        }
    }
    
    /**
     * 团购详细信息
     */
    public function groupbuy_detailOp() {
        $group_id = intval($_GET['group_id']);
        $model_groupbuy = Model('groupbuy');

        //获取团购详细信息
        $groupbuy_info = $model_groupbuy->getGroupbuyInfoByID($group_id);
        if(empty($groupbuy_info['groupbuy_id'])) {
            Tpl::output('errorInfo','团购商品不存在');
        }
        Tpl::output('groupbuy_info',$groupbuy_info);

        // 浏览数加1
        $update_array = array();
        $update_array['views'] = array('exp', 'views+1');
        $model_groupbuy->editGroupbuy($update_array, array('groupbuy_id'=>$group_id));
        Tpl::showpage('groupbuy_detail');
    }

}
