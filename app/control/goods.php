<?php
/**
 * 商品
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class goodsControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }

    /**
     * 商品列表
     */
    public function goods_listOp() {
        $model_goods = Model('goods');

        //查询条件
        $condition = array();
        if(!empty($_GET['gc_id']) && intval($_GET['gc_id']) > 0) {
            $condition['gc_id'] = $_GET['gc_id'];
        } elseif (!empty($_GET['keyword'])) { 
            $condition['goods_name|goods_jingle'] = array('like', '%' . $_GET['keyword'] . '%');
        }elseif(!empty($_GET['appkey'])){		
		   $condition['goods_appkey'] = array('like', '%|' . $_GET['appkey'] . '|%');
		}

        //所需字段
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_price,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count,is_virtual,virtual_indate";

        //排序方式
        $order = $this->_goods_list_order($_GET['key'], $_GET['order']);

        $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order, $this->page);
        $page_count = $model_goods->gettotalpage();

        //处理商品列表(团购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);
  
        output_data(array('goods_list' => $goods_list), mobile_page($page_count));
    }
    
 public function goods_groupbuylistOp() {
        $model_goods = Model('goods');
        $model_groupbuy = Model('groupbuy');
        $condition = array();
        $groupbuy_info = $model_groupbuy->getGroupbuyOnlineList($condition);
        $goods_list_id=array();
        foreach($groupbuy_info as $k=>$val){
            $goods_list_id[$k]=$val['goods_id'];
            
        }
        
      
        //查询条件
        $condition = array();
        $condition['goods_id']=array('in',$goods_list_id);

        //所需字段
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_price,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count";

        //排序方式
        $order = $this->_goods_list_order($_GET['key'], $_GET['order']);
        
        $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order, $this->page);
        $page_count = $model_goods->gettotalpage();

        //处理商品列表(团购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);
    //echo "<pre>";
  //  print_r($goods_list);
        output_data(array('goods_list' => $goods_list), mobile_page($page_count));
    }


    /**
     * 商品列表排序方式
     */
    private function _goods_list_order($key, $order) {
        $result = 'goods_id desc';
        if (!empty($key)) {

            $sequence = 'desc';
            if($order == 1) {
                $sequence = 'asc';
            }

            switch ($key) {
                //销量
                case '1' :
                    $result = 'goods_salenum' . ' ' . $sequence;
                    break;
                //浏览量
                case '2' : 
                    $result = 'goods_click' . ' ' . $sequence;
                    break;
                //价格
                case '3' :
                    $result = 'goods_price' . ' ' . $sequence;
                    break;
				//APP首页推荐
				case '4':
					$result = 'goods_num' . ' ' . $sequence.',goods_id desc';
                    break;
            }
        }
        return $result;
    }

    /**
     * 处理商品列表(团购、限时折扣、商品图片)
     */
    private function _goods_list_extend($goods_list) {
        //获取商品列表编号数组
        $commonid_array = array();
        $goodsid_array = array();
        foreach($goods_list as $key => $value) {
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
                $goods_list[$key]['group_flag'] = 1;

            } else {
                $goods_list[$key]['group_flag'] = 0;
            }

            //限时折扣
            if (isset($xianshi_list[$value['goods_id']]) && !$goods_list[$key]['group_flag']) {
                $goods_list[$key]['goods_price'] = $xianshi_list[$value['goods_id']]['xianshi_price'];
                $goods_list[$key]['xianshi_flag'] = 1;
            } else {
                $goods_list[$key]['xianshi_flag'] = 0;
            }
            
            //虚拟兑换
            if($value['is_virtual'] && $value['virtual_indate'] > time()){
                $goods_list[$key]['is_virtual'] = 1;
            }else{
                $goods_list[$key]['is_virtual'] = 0;
            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']); 

            unset($goods_list[$key]['store_id']);
            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['nc_distinct']);
            unset($goods_list[$key]['virtual_indate']);
        }

        return $goods_list;
    }

    /**
     * 商品详细页
     */
    public function goods_detailOp() {
        $goods_id = intval($_GET ['goods_id']);
        
        // 商品详细信息
        $model_goods = Model('goods');
        $goods_detail = $model_goods->getGoodsDetail($goods_id, '*');
        if (empty($goods_detail)) {
            output_error('商品不存在');
        }

       //团购
            if (intval(@$goods_detail['goods_info']['promotion_price'])>0) {
                $goods_detail['goods_info']['goods_price'] = $goods_detail['goods_info']['promotion_price'];
               
            }
        $condition = array();
        $condition['geval_goodsid'] = $goods_id;
   
        //查询商品评分信息
        $model_evaluate_goods = Model("evaluate_goods");
        $page_count = $model_evaluate_goods->gettotalpage();
        $goodsevallist = $model_evaluate_goods->getEvaluateGoodsList($condition, 20);
        foreach ($goodsevallist as $key => $val){
            $image = explode(',', $val['geval_image']);
//            print_R($image);exit;
            foreach($image as $k=>$v){
                $imgArr[] = UPLOAD_SITE_URL.DS.ATTACH_MALBUM.DS.$val['geval_frommemberid'].DS.$v;
            }
          
            $goodsevallist[$key]['geval_image'] = $imgArr;
        }
        //echo BASE_UPLOAD_PATH.DS.ATTACH_MALBUM.DS.$member_id.DS.$img_path;
        foreach ($goodsevallist as $key => $v) {
        	$goods_detail['goods_infos'][$key]['geval_frommembername'] = str_cut($v['geval_frommembername'],7).'****';
        	$goods_detail['goods_infos'][$key]['geval_content'] = $v['geval_content'];
        	$goods_detail['goods_infos'][$key]['geval_scores'] = $v['geval_scores'];
        }
         //echo '<pre>'; print_r($goodsevallist);exit;
//echo '<pre>';print_r($goods_detail);exit;
        //推荐商品
        $model_store = Model('store');
        $hot_sales = $model_store->getHotSalesList($goods_detail['goods_info']['store_id'], 6);
        $goods_commend_list = array();
        foreach($hot_sales as $value) {
            $goods_commend = array();
            $goods_commend['goods_id'] = $value['goods_id'];
            $goods_commend['goods_name'] = $value['goods_name'];
            $goods_commend['goods_price'] = $value['goods_price'];
            $goods_commend['goods_image_url'] = cthumb($value['goods_image'], 240);
            $goods_commend_list[] = $goods_commend;
        }
        $goods_detail['goods_commend_list'] = $goods_commend_list;

        //商品详细信息处理
        $goods_detail = $this->_goods_detail_extend($goods_detail);
       
        output_data($goods_detail);
    }

    /**
     * 商品详细信息处理
     */
    private function _goods_detail_extend($goods_detail) {
        //整理商品规格
        unset($goods_detail['spec_list']);
        $goods_detail['spec_list'] = $goods_detail['spec_list_mobile'];
        unset($goods_detail['spec_list_mobile']);

        //整理商品图片
        unset($goods_detail['goods_image']);
        $goods_detail['goods_image'] = implode(',', $goods_detail['goods_image_mobile']);
        unset($goods_detail['goods_image_mobile']);

        //整理数据
        unset($goods_detail['goods_info']['goods_commonid']);
        unset($goods_detail['goods_info']['gc_id']);
        unset($goods_detail['goods_info']['gc_name']);
        unset($goods_detail['goods_info']['store_id']);
        unset($goods_detail['goods_info']['store_name']);
        unset($goods_detail['goods_info']['brand_id']);
        unset($goods_detail['goods_info']['brand_name']);
        unset($goods_detail['goods_info']['type_id']);
        unset($goods_detail['goods_info']['goods_image']);
        unset($goods_detail['goods_info']['goods_body']);
        unset($goods_detail['goods_info']['goods_state']);
        unset($goods_detail['goods_info']['goods_stateremark']);
        unset($goods_detail['goods_info']['goods_verify']);
        unset($goods_detail['goods_info']['goods_verifyremark']);
        unset($goods_detail['goods_info']['goods_lock']);
        unset($goods_detail['goods_info']['goods_addtime']);
        unset($goods_detail['goods_info']['goods_edittime']);
        unset($goods_detail['goods_info']['goods_selltime']);
        unset($goods_detail['goods_info']['goods_show']);
        unset($goods_detail['goods_info']['goods_commend']);
        //整理团购的开始和结束时间
        if($goods_detail['goods_info']['promotion_type'] == 'groupbuy'){
            $goods_detail['goods_info']['groupbuy_start_time'] = date('Y-m-d H:i',$goods_detail['groupbuy_info']['start_time']);
            $goods_detail['goods_info']['groupbuy_end_time'] = date('Y-m-d H:i',$goods_detail['groupbuy_info']['end_time']);
        }
        unset($goods_detail['groupbuy_info']);
        unset($goods_detail['xianshi_info']);

        return $goods_detail;
    }

    /**
     * 商品详细页
     */
    public function goods_bodyOp() {
        $goods_id = intval($_GET ['goods_id']);

        $model_goods = Model('goods');

        $goods_info = $model_goods->getGoodsInfo(array('goods_id' => $goods_id));
        $goods_common_info = $model_goods->getGoodeCommonInfo(array('goods_commonid' => $goods_info['goods_commonid']));

        Tpl::output('goods_common_info', $goods_common_info);
        Tpl::showpage('goods_body');
    }
}
