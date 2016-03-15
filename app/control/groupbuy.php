<?php

defined('InShopNC') or exit('Access Invalid!');

class groupbuyControl extends mobileHomeControl {

    public function __construct() {
        parent::__construct();
        if (intval($GLOBALS['setting_config']['groupbuy_allow']) !== 1) {
            output_error('团购功能尚未开启');
        }
    }
    
    /**
     * 进行中的团购团购
     **/
    public function indexOp() {
        $this->_show_groupbuy_list('getGroupbuyOnlineList');
    }
    
    /**
     * 即将开始的团购
     **/
    public function groupbuy_soonOp() {
        $this->_show_groupbuy_list('getGroupbuySoonList');
    }

    /**
     * 获取团购列表
     */
    private function _show_groupbuy_list($function_name) {

        $model_groupbuy = Model('groupbuy');
        $condition = array();

        //获取团购详细信息
        $groupbuy_info = $model_groupbuy->$function_name($condition, $this->page);
        if (empty($groupbuy_info)) {
            output_error('没有数据');
        }
        foreach ($groupbuy_info as $k => $v) {
            $goodsinfo = Model('goods');
            $groupbuy = $goodsinfo->getGoodsInfo(array('goods_id' => $v['goods_id']));
            $groupbuy_info[$k]['goods_image'] = $groupbuy['goods_image'];
            $groupbuy_info[$k]['store_id'] = $groupbuy['store_id'];
        }
        $page_count = $model_groupbuy->gettotalpage();

        $groupbuy_info = $this->_goods_list_extend($groupbuy_info);
        echo "<pre>";
        print_r($groupbuy_info);
        exit();
        output_data(array('groupbuy_infolist' => $groupbuy_info), mobile_page($page_count));
    }

    /**
     * 处理商品列表(团购、限时折扣、商品图片)
     */
    private function _goods_list_extend($goods_list) {
        //获取商品列表编号数组
        $commonid_array = array();
        $goodsid_array = array();
        foreach ($goods_list as $key => $value) {
            $commonid_array[] = $value['goods_commonid'];
            $goodsid_array[] = $value['goods_id'];
        }

        //促销
        $groupbuy_list = Model('groupbuy')->getGroupbuyListByGoodsCommonIDString(implode(',', $commonid_array));

        $xianshi_list = Model('p_xianshi_goods')->getXianshiGoodsListByGoodsString(implode(',', $goodsid_array));
        foreach ($goods_list as $key => $value) {
            //团购
            if (isset($groupbuy_list[$value['goods_commonid']])) {
                $goods_list[$key]['goods_price'] = $groupbuy_list[$value['goods_commonid']]['groupbuy_price'];
                $goods_list[$key]['group_flag'] = true;
            } else {
                $goods_list[$key]['group_flag'] = false;
            }

            //限时折扣
            if (isset($xianshi_list[$value['goods_id']]) && !$goods_list[$key]['group_flag']) {
                $goods_list[$key]['goods_price'] = $xianshi_list[$value['goods_id']]['xianshi_price'];
                $goods_list[$key]['xianshi_flag'] = true;
            } else {
                $goods_list[$key]['xianshi_flag'] = false;
            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']);

            unset($goods_list[$key]['store_id']);
            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['nc_distinct']);
        }

        return $goods_list;
    }

}

?>