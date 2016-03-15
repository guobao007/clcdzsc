<?php

/**
 * 积分兑换API
 *
 * @author QT
 */
defined('InShopNC') or exit('Access Invalid!');
class member_redeemControl extends mobileMemberControl {
    /**
     * 兑换商品列表
     */
    public function orderlistOp() {
        //条件
        $condition_arr = array();
        $condition_arr['point_buyerid'] = $this->member_info['member_id'];
        //兑换信息列表
        $pointorder_model = Model('pointorder');
        $order_list = $pointorder_model->getPointOrderList($condition_arr, $this->page, 'simple','point_orderid,point_ordersn,point_buyerid,point_buyername,point_buyeremail,point_addtime,point_allpoint,point_orderstate');
        $page_count = $pointorder_model->gettotalpage();
        $order_idarr = array();
        $order_listnew = array();
        if (is_array($order_list) && count($order_list) > 0) {
            foreach ($order_list as $k => $v) {
                $order_idarr[] = $v['point_orderid'];
                $order_listnew[$v['point_orderid']] = $v;
            }
        }
        //查询兑换商品
        if (is_array($order_idarr) && count($order_idarr) > 0) {
            $order_idstr = implode(',', $order_idarr);
            $prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid_in' => $order_idstr), '');
            if (is_array($prod_list) && count($prod_list) > 0) {
                foreach ($prod_list as $v) {
                    if (isset($order_listnew[$v['point_orderid']])) {
                        if($v['point_goodsimage']){
                            $v['point_goodsimage'] = UPLOAD_SITE_URL.DS.ATTACH_POINTPROD . DS . str_ireplace('.', '_small.', $v['point_goodsimage']);
                        }else{
                            $v['point_goodsimage'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(240);
                        }
                        $order_listnew[$v['point_orderid']]['prodlist'][] = $v;
                    }
                }
            }
        }
        foreach ($order_listnew as $key => $v) {
        	$order_listnew[$key]['point_addtime'] = date('Y-m-d',$v['point_addtime']);
        }
        
        //信息输出
        output_data(array('order_list' => array_values($order_listnew)), mobile_page($page_count));
    }
    
    /**
     * 兑换商品详情
     */
    public function order_infoOp() {
        $order_id = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('参数错误');
        }
        //查询订单信息
        $pointorder_model = Model('pointorder');
        $condition_arr['point_orderid'] = $order_id;
        $condition_arr['point_buyerid'] = $this->member_info['member_id'];
        $order_info = $pointorder_model->getPointOrderInfo($condition_arr, 'all', '*');
        if (!is_array($order_info) || count($order_info) <= 0) {
            output_error('记录信息错误');
        }
        //整理数据
        unset($order_info['point_paymentid'],$order_info['point_paymentname'],$order_info['point_paymentcode'],$order_info['point_paymentdirect'],$order_info['point_outsn'],$order_info['point_paymenttime'],$order_info['point_paymessage'],$order_info['point_shippingtime'],$order_info['point_shippingcode'],$order_info['point_shippingdesc'],$order_info['point_outpaymentcode'],$order_info['point_finnshedtime'],$order_info['point_orderamount'],$order_info['point_shippingcharge'],$order_info['point_shippingfee'],$order_info['point_ordermessage'],$order_info['point_oaid']);
        //兑换商品信息
        $prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid' => "{$order_id}"),'');
        foreach ($prod_list as $k=>$v){
            unset($prod_list[$k]['point_recid']);
        }
        //信息输出
        output_data(array('order_info' => $order_info, 'prod_list' => $prod_list));
    }
    
    /**
     * 确认收货
     */
    public function receiving_orderOp(){
        $order_id = intval($_POST['order_id']);
        if ($order_id <= 0){
            output_error('参数错误');
        }
        $pointorder_model = Model('pointorder');
        $condition_arr = array();		
        $condition_arr['point_orderid'] = "$order_id";
        $condition_arr['point_buyerid'] = $this->member_info['member_id'];
        $condition_arr['point_orderstate'] = '30';//待收货
        //更新订单状态
        $state = $pointorder_model->updatePointOrder($condition_arr,array('point_orderstate'=>'40','point_finnshedtime'=>time()));
        if ($state){
            output_data('1');
        }else {
            output_error('确认收货失败');
        }
    }
    
    /**
     * 取消兑换
     */
    public function cancel_orderOp() {
        $order_id = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('参数错误');
        }
        $pointorder_model = Model('pointorder');
        $condition_arr = array();
        $condition_arr['point_orderid'] = "$order_id";
        $condition_arr['point_buyerid'] = $this->member_info['member_id'];
        $condition_arr['point_order_enablecancel'] = '1'; //可取消
        //查询兑换信息
        $order_info = $pointorder_model->getPointOrderInfo($condition_arr, 'simple', 'point_ordersn,point_buyerid,point_buyername,point_allpoint');
        if (!is_array($order_info) || count($order_info) <= 0) {
            output_error('记录信息错误');
        }
        //更改订单状态 - 2已取消
        $state = $pointorder_model->updatePointOrder($condition_arr, array('point_orderstate' => '2'));
        if ($state) {
            //退还会员积分
            $points_model = Model('points');
            $insert_arr['pl_memberid'] = $order_info['point_buyerid'];
            $insert_arr['pl_membername'] = $order_info['point_buyername'];
            $insert_arr['pl_points'] = $order_info['point_allpoint'];
            $insert_arr['point_ordersn'] = $order_info['point_ordersn'];
            $insert_arr['pl_desc'] = '取消兑换礼品信息' . $order_info['point_ordersn'] . '增加积分';
            $points_model->savePointsLog('pointorder', $insert_arr, true);
            //更改兑换礼品库存
            $prod_list = $pointorder_model->getPointOrderProdList(array('prod_orderid' => $order_id), '', 'point_goodsid,point_goodsnum');
            if (is_array($prod_list) && count($prod_list) > 0) {
                $pointprod_model = Model('pointprod');
                foreach ($prod_list as $v) {
                    $update_arr = array();
                    $update_arr['pgoods_storage'] = array('sign' => 'increase', 'value' => $v['point_goodsnum']);
                    $update_arr['pgoods_salenum'] = array('sign' => 'decrease', 'value' => $v['point_goodsnum']);
                    $pointprod_model->updatePointProd($update_arr, array('pgoods_id' => $v['point_goodsid']));
                    unset($update_arr);
                }
            }
            output_data('1');
        } else {
            output_error('取消兑换失败');
        }
    }
    
    /**
     * 我的代金券列表
     */
    public function voucher_listOp() {
        //判断系统是否开启代金券功能
        if (intval(C('voucher_allow')) !== 1) {
            output_error('代金券功能尚未开启');
        }
        //检查过期的代金券，状态设置为过期(voucher_state=3)
        $this->check_voucher_expire();
        $model = Model();
        $where = array('voucher_owner_id' => $this->member_info['member_id']);
        if (intval($_POST['select_detail_state']) > 0) {
            $where['voucher_state'] = intval($_POST['select_detail_state']);
        }
        $field = "voucher_id,voucher.voucher_t_id,voucher_code,voucher_title,voucher_desc,voucher_start_date,voucher_end_date,voucher_price,voucher_limit,voucher_state,voucher_order_id,voucher_store_id,store_name,store_id,voucher_t_customimg";
        $list = $model->table('voucher,store,voucher_template')->field($field)->join('inner,inner')->on('voucher.voucher_store_id = store.store_id,voucher.voucher_t_id=voucher_template.voucher_t_id')->where($where)->order('voucher_id desc')->page($this->page)->select();
        $page_count = $model->gettotalpage();
        if (is_array($list)) {
            foreach ($list as $key => $val) {
                if (empty($val['voucher_t_customimg']) || !file_exists(BASE_UPLOAD_PATH . DS . ATTACH_VOUCHER . DS . $val['store_id'] . DS . $val['voucher_t_customimg'])) {
                    $list[$key]['voucher_t_customimg'] = UPLOAD_SITE_URL . DS . defaultGoodsImage(60);
                } else {
                    $list[$key]['voucher_t_customimg'] = UPLOAD_SITE_URL . DS . ATTACH_VOUCHER . DS . $val['store_id'] . DS . str_ireplace('.', '_small.', $val['voucher_t_customimg']);
                }
                $list[$key]['voucher_start_date'] = date('Y-m-d',$val['voucher_start_date']);
                $list[$key]['voucher_end_date'] = date('Y-m-d',$val['voucher_end_date']);

            }
        }
       //echo '<pre>'; print_R($list);
        output_data(array('voucher_list' => $list), mobile_page($page_count));
    }

    //检查过期的代金券，状态设置为过期(vouchet_state=3)
    private function check_voucher_expire() {
        $model = Model();
        $model->table('voucher')->where(array('voucher_owner_id' => $this->member_info['member_id'], 'voucher_state' => 1, 'voucher_end_date' => array('lt', time())))->update(array('voucher_state' => 3));
    }

}
