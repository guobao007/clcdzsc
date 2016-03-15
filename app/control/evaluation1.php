<?php
/**
 * 评价内容
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
class evaluationControl extends mobileMemberControl {

    public function __construct() {
        
        parent::__construct() ;
        Language::read('member_layout,member_evaluate');
    }

    public function indexOp() {
        $this->commentsOp();
    }
    
    /**
     * 商品评论
    */
    public function commentsOp() {
        $goods_id = intval($_GET['goods_id']);     
        $condition = array();
        $condition['geval_goodsid'] = $goods_id;
        switch ($type) {
            case '1':
                $condition['geval_scores'] = array('in', '5,4');
                Tpl::output('type', '1');
                break;
            case '2':
                $condition['geval_scores'] = array('in', '3,2');
                Tpl::output('type', '2');
                break;
            case '3':
                $condition['geval_scores'] = array('in', '1');
                Tpl::output('type', '3');
                break;
        }

        //查询商品评分信息
        $model_evaluate_goods = Model("evaluate_goods");
        $page_count = $model_evaluate_goods->gettotalpage();
        $goodsevallist = $model_evaluate_goods->getEvaluateGoodsList($condition, $this->page);
        foreach ($goodsevallist as $key => $val){
            $image = explode(',', $val['geval_image']);
//            print_R($image);exit;
            foreach($image as $k=>$v){
                $imgArr[] = UPLOAD_SITE_URL.DS.ATTACH_MALBUM.DS.$val['geval_frommemberid'].DS.$v;
            }
          
            $goodsevallist[$key]['geval_image'] = $imgArr;
        }
        //echo BASE_UPLOAD_PATH.DS.ATTACH_MALBUM.DS.$member_id.DS.$img_path;
         echo '<pre>'; print_r($goodsevallist);
        output_data(array('goodsevallist' => $goodsevallist), mobile_page($page_count));
	
    }
    
     /**
     * 订单添加评价
     */
    public function addOp(){
         //$order_id = intval($_GET['order_id']);
       $order_id = intval($_POST['order_id']);
        if (!$order_id){
            output_error('参数错误!');
        }

        $model_order = Model('order');
        $model_store = Model('store');
        $model_evaluate_goods = Model('evaluate_goods');
        $model_evaluate_store = Model('evaluate_store');

        //获取订单信息
        //订单为'已收货'状态，并且未评论
        $order_info = $model_order->getOrderInfo(array('order_id' => $order_id));
        $order_info['evaluate_able'] = $model_order->getOrderOperateState('evaluation',$order_info);
        if (empty($order_info) || !$order_info['evaluate_able']){
           output_error('订单信息错误!');
        }

     
        //查询店铺信息
        $store_info = $model_store->getStoreInfoByID($order_info['store_id']);
        if(empty($store_info)){
           output_error('店铺信息错误!');
        }
        
        //获取订单商品
        $order_goods = $model_order->getOrderGoodsList(array('order_id'=>$order_id));
        if(empty($order_goods)){
           output_error('订单信息错误!');
        }
     // echo '<pre>';  print_r($this->member_info);exit;
        
     
          
            $evaluate_goods_array = array();
            foreach ($order_goods as $value){
                //如果未评分，默认为5分
                $evaluate_score = intval($_POST['goods'][$value['goods_id']]['score']);
                if($evaluate_score <= 0 || $evaluate_score > 5) {
                    $evaluate_score = 5;
                }
                //默认评语
                $evaluate_comment = $_POST['goods'][$value['goods_id']]['comment'];
                if(empty($evaluate_comment)) {
                    $evaluate_comment = '不错哦';
                }

                $evaluate_goods_info = array();
                $evaluate_goods_info['geval_orderid'] = $order_id;
                $evaluate_goods_info['geval_orderno'] = $order_info['order_sn'];
                $evaluate_goods_info['geval_ordergoodsid'] = $value['rec_id'];
                $evaluate_goods_info['geval_goodsid'] = $value['goods_id'];
                $evaluate_goods_info['geval_goodsname'] = $value['goods_name'];
                $evaluate_goods_info['geval_goodsprice'] = $value['goods_price'];
                $evaluate_goods_info['geval_scores'] = $evaluate_score;
                $evaluate_goods_info['geval_content'] = $evaluate_comment;
                $evaluate_goods_info['geval_isanonymous'] = $_POST['anony']?1:0;
                $evaluate_goods_info['geval_addtime'] = TIMESTAMP;
                $evaluate_goods_info['geval_storeid'] = $store_info['store_id'];
                $evaluate_goods_info['geval_storename'] = $store_info['store_name'];
                $evaluate_goods_info['geval_frommemberid'] = $this->member_info['member_id'];
                $evaluate_goods_info['geval_frommembername'] = $this->member_info['member_name'];

                $evaluate_goods_array[] = $evaluate_goods_info;
            }
            $model_evaluate_goods->addEvaluateGoodsArray($evaluate_goods_array);

           

            //更新订单信息并记录订单日志
            $state = $model_order->editOrder(array('evaluation_state'=>1), array('order_id' => $order_id));
            $model_order->editOrderCommon(array('evaluation_time'=>TIMESTAMP), array('order_id' => $order_id));
            if ($state){
                $data = array();
                $data['order_id'] = $order_id;
                $data['log_role'] = 'buyer';
                $data['log_msg'] = L('order_log_eval');
                $model_order->addOrderLog($data);
            }

            //添加会员积分
            if ($GLOBALS['setting_config']['points_isuse'] == 1){
                $points_model = Model('points');
                $points_model->savePointsLog('comments',array('pl_memberid'=>$this->member_info['member_id'],'pl_membername'=>$this->member_info['member_name']));
            }

            output_data('1');
        
    }
    
     public function add_image_saveOp() {
        $geval_id = intval($_POST['geval_id']);
        $model_evaluate_goods = Model('evaluate_goods');
        $geval_info = $model_evaluate_goods->getEvaluateGoodsInfoByID($geval_id);
        if(empty($geval_info)) {
             output_data('参数错误');
        }
        $geval_info = $model_evaluate_goods->getEvaluateGoodsInfoByID($geval_id);

        if(!empty($geval_info['geval_image'])) {
            output_error('该商品已经发表过晒单!');
        }
        $geval_image = '';
        foreach ($_POST['evaluate_image'] as $value) {
            if(!empty($value)) {
                $geval_image .= $value . ',';
            } 
        }
        $geval_image = rtrim($geval_image, ',');
        $update = array();
        $update['geval_image'] = $geval_image;
        $condition = array();
        $condition['geval_id'] = $geval_id;
        $result = $model_evaluate_goods->editEvaluateGoods($update, $condition);

        list($sns_image) = explode(',', $geval_image);
        $goods_url = urlShop('goods', 'index', array('goods_id' => $geval_info['geval_goodsid']));
        //同步到sns
        $content = "
            <div class='fd-media'>
            <div class='goodsimg'><a target=\"_blank\" href=\"{$goods_url}\"><img src=\"".snsThumb($sns_image, 240)."\" title=\"{$geval_info['geval_goodsname']}\" alt=\"{$geval_info['geval_goodsname']}\"></a></div>
            <div class='goodsinfo'>
            <dl>
            <dt><a target=\"_blank\" href=\"{$goods_url}\">{$geval_info['geval_goodsname']}</a></dt>
            <dd>价格".Language::get('nc_colon').Language::get('currency').$geval_info['geval_goodsprice']."</dd>
            <dd><a target=\"_blank\" href=\"{$goods_url}\">去看看</a></dd>
            </dl>
            </div>
            </div>
            ";

        $tracelog_model = Model('sns_tracelog');
        $insert_arr = array();
        $insert_arr['trace_originalid'] = '0';
        $insert_arr['trace_originalmemberid'] = '0';
        $insert_arr['trace_memberid'] = $_SESSION['member_id'];
        $insert_arr['trace_membername'] = $_SESSION['member_name'];
        $insert_arr['trace_memberavatar'] = $_SESSION['member_avatar'];
        $insert_arr['trace_title'] = '发表了商品晒单'; 
        $insert_arr['trace_content'] = $content;
        $insert_arr['trace_addtime'] = TIMESTAMP;
        $insert_arr['trace_state'] = '0';
        $insert_arr['trace_privacy'] = 0; 
        $insert_arr['trace_commentcount'] = 0;
        $insert_arr['trace_copycount'] = 0;
        $insert_arr['trace_from'] = '1';
        $result = $tracelog_model->tracelogAdd($insert_arr);

        if($result) {
            showDialog(L('nc_common_save_succ'), urlShop('member_evaluate', 'list'), 'succ');
        } else {
            showDialog(L('nc_common_save_succ'), urlShop('member_evaluate', 'list'));
        }
    }
    
   



 

   

}
