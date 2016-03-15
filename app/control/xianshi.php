<?php
/**
 * 限时折扣 
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
class xianshiControl extends mobileHomeControl {

    const LINK_XIANSHI_LIST = 'index.php?act=store_promotion_xianshi&op=xianshi_list';
    const LINK_XIANSHI_MANAGE = 'index.php?act=store_promotion_xianshi&op=xianshi_manage&xianshi_id=';

    public function __construct() {
        parent::__construct() ;

        //读取语言包
        Language::read('member_layout,promotion_xianshi');
        //检查限时折扣是否开启
        if (intval(C('promotion_allow')) !== 1){
            output_error('限时折扣功能尚未开启');
        }

    }

    public function indexOp() {
        $this->xianshi_listOp();
    }
   
    /**
     * 限时折扣活动详细商品列表
     **/
    public function xianshi_listOp() {
       
        $model_xianshi = Model('p_xianshi_goods');
        $condition = array();
        $condition['state'] = 1;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        //$condition['xianshi_id'] = $xianshi_id;
        
        $listinfo = $model_xianshi->getXianshiGoodsList($condition, $this->page, 'state desc, end_time desc');
        foreach ($listinfo as $key => $v){
            $listinfo[$key]['start_time'] = date('Y-m-d',$v['start_time']);
            $listinfo[$key]['end_time'] = date('Y-m-d',$v['end_time']);
        }
        $page_count = $model_xianshi->gettotalpage();
        //echo '<pre>';print_R($listinfo);
         output_data(array('listinfo'=>$listinfo),mobile_page($page_count));
    }


     
  
}
