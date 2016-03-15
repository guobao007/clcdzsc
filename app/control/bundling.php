<?php
/**
 * 优惠套装
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
class bundlingControl extends mobileHomeControl {

    public function __construct() {
        parent::__construct();
        /**
         * 读取语言包
         */
        Language::read('member_layout,member_store_promotion_bundling');
        //检查是否开启
        if (intval(C('promotion_allow')) !== 1) {
           output_error('优惠套餐功能尚未开启');
        }

    }

    public function indexOp() {
        $this->bundling_listOp();
    }

    /**
     * 发布的活动列表
     */
    public function bundling_listOp() {
        $model_bundling = Model('p_bundling');
        
    
        
        // 检查是否已购买套餐
        $where = array();
        
        $bundling_quota = $model_bundling->getBundlingQuotaInfo($where);
   //echo '<pre>';print_R($bundling_quota);
        if (!empty($bundling_quota)) {
          
             $page_count = $model_bundling->gettotalpage();
            // 查询活动
            $where = array();    
            $bundling_list = $model_bundling->getBundlingList($where, '*', 'bl_id desc', $this->page, 0, $bundling_published);
            $bundling_list = array_under_reset($bundling_list, 'bl_id');
            
           
            
            if (!empty($bundling_list)) {
                $blid_array = array_keys($bundling_list);
                $bgoods_array = $model_bundling->getBundlingGoodsList(array('bl_id' => array('in', $blid_array), 'bl_appoint' => 1), 'bl_id,goods_id,count(*) as count', 'bl_appoint desc', 'bl_id');
                $bgoods_array = array_under_reset($bgoods_array, 'goods_id');
                if (!empty($bgoods_array)) {
                    $goodsid_array = array_keys($bgoods_array);
                    $goods_array = Model('goods')->getGoodsList(array('goods_id' => array('in', $goodsid_array)), 'goods_id,goods_image');
                    $goods_array = array_under_reset($goods_array, 'goods_id');
                }
                $bgoods_array = array_under_reset($bgoods_array, 'bl_id');
                foreach ($bundling_list as $key => $val) {
                    $bundling_list[$key]['goods_id'] = $bgoods_array[$val['bl_id']]['goods_id'];
                    $bundling_list[$key]['count'] = $bgoods_array[$val['bl_id']]['count'];
                    $bundling_list[$key]['img'] = thumb($goods_array[$bgoods_array[$val['bl_id']]['goods_id']], 60);
                }
            }
            //echo '<pre>';print_R($bundling_list);
            output_data(array('bundling_list'=>$bundling_list),mobile_page($page_count));
           
            
        }
      
    }
    
    
    /**
     * 显示优惠套装
     */
    public function get_bundlingOp() {
        $goods_id = intval($_GET['goods_id']);
       // $goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0) {
          output_error('参数错误');
        }
        $model_bundling = Model('p_bundling');
 
         // 根据商品id查询bl_id
        $b_g_list = $model_bundling->getBundlingGoodsList(array('goods_id' => $goods_id, 'bl_appoint' => 1), 'bl_id');
            if (!empty($b_g_list) && is_array($b_g_list)) {
                $b_id_array = array();
                foreach ($b_g_list as $val) {
                    $b_id_array[] = $val['bl_id'];
                }
                
                // 查询套餐列表
                $bundling_list = $model_bundling->getBundlingOpenList(array('bl_id' => array('in', $b_id_array)));
                
                // 整理
                if (!empty($bundling_list) && is_array($bundling_list)) {
                    $bundling_array = array();
                    foreach ($bundling_list as $val) {
                        $bundling_array[$val['bl_id']]['id'] = $val['bl_id'];
                        $bundling_array[$val['bl_id']]['name'] = $val['bl_name'];
                        $bundling_array[$val['bl_id']]['cost_price'] = 0;
                        $bundling_array[$val['bl_id']]['price'] = $val['bl_discount_price'];
                        $bundling_array[$val['bl_id']]['freight'] = $val['bl_freight'];
                    }
                    $blid_array = array_keys($bundling_array);
                    
                    $b_goods_list = $model_bundling->getBundlingGoodsList(array('bl_id' => array('in', $blid_array)));
                    if (!empty($b_goods_list)) {
                        $goodsid_array = array();
                        foreach ($b_goods_list as $val) {
                            $goodsid_array[] = $val['goods_id'];
                        }
                        $goods_list = Model('goods')->getGoodsAsGoodsShowList(array('goods_id' => array('in', $goodsid_array)), 'goods_id,goods_name,goods_price,goods_image');
                        $goods_list = array_under_reset($goods_list, 'goods_id');
                    }
                    // 整理
                    if (! empty ( $b_goods_list ) && is_array ( $b_goods_list )) {
                        $b_goods_array = array ();
                        foreach ( $b_goods_list as $val ) {
                            if (isset($goods_list[$val['goods_id']])) {
                                $k = (intval($val['goods_id']) == $goods_id) ? 0 : $val['goods_id'];    // 排序当前商品放到最前面
                                $b_goods_array[$val['bl_id']][$k]['id'] = $val['goods_id'];
                                $b_goods_array[$val['bl_id']][$k]['image'] = thumb($goods_list[$val['goods_id']], 240);
                                $b_goods_array[$val['bl_id']][$k]['name'] = $goods_list[$val['goods_id']]['goods_name'];
                                $b_goods_array[$val['bl_id']][$k]['shop_price'] = ncPriceFormat($goods_list[$val['goods_id']]['goods_price']);
                                $b_goods_array[$val['bl_id']][$k]['price'] = ncPriceFormat($val['bl_goods_price']);
                                $bundling_array[$val['bl_id']]['cost_price'] += ncPriceFormat($goods_list[$val['goods_id']]['goods_price']);
                            }
                        }
                    }
                    
                
                    //echo '<pre>';print_R($b_goods_array);
        output_data(array('b_goods_array'=>$b_goods_array));
                    Tpl::output('bundling_array', $bundling_array);
                    Tpl::output('b_goods_array', $b_goods_array);
                }
            }else{
                 output_error('该商品没有优惠套餐');
                
            }
       
    }
    
   
    
 
    
   
}
