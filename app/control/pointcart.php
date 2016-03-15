<?php
/**
 * 积分兑换商品
 *
 * @author QT
 */
defined('InShopNC') or exit('Access Invalid!');
define('APP_SITE_URL', 'http://ahxbgw.com/app');
define('TPL_SHOP_NAME','default');
define('SHOP_TEMPLATES_URL',APP_SITE_URL.'/templates/'.TPL_SHOP_NAME);
class pointcartControl extends mobilePointControl{
    
    /**
     * 兑换订单流程第一步
     */
    public function step1Op(){
        $pgoods_id = intval($_GET['pgid']);
        $buynum = intval($_GET['buynum']);
        if(empty($_SESSION['member_id'])){
            Tpl::output('errorInfo','参数错误，请登录后操作');
        }else{
            //获取符合条件的兑换礼品和总积分及运费
            $pointprod_arr = $this->getLegalPointGoods($pgoods_id,$buynum);
            Tpl::output('pointprod_arr',$pointprod_arr);

            //实例化收货地址模型
            $mode_address	= Model('address');
            $address_info	= $mode_address->getDefaultAddressInfo(array('member_id'=>$_SESSION['member_id']));
            Tpl::output('address_info',$address_info);
        }
		Tpl::output('title','已选择的兑换礼品');
        Tpl::showpage('pointcart_step1');
    }
    
        /**
	 * 兑换订单流程第二步
	 */
	public function step2Op() {
            $cart_id = trim($_POST['cart_id']);
            $address_id = intval($_POST['address_id']);
            $cart_arr = explode('|', $cart_id);
            $pgoods_id = intval($cart_arr[0]);
            $buynum = intval($cart_arr[1]);
			if (C('pointprod_isuse') != 1){
                output_data(array('error'=>1,'msg'=>'未开启积分兑换功能'));die;
            }
            //验证参数是否合法
            if(empty($pgoods_id) || empty($buynum) || empty($_SESSION['member_id'])){
                output_data(array('error'=>1,'msg'=>'参数错误，稍后重试'));die;
            }
            if(empty($address_id)){
                output_data(array('error'=>1,'msg'=>'请选择收货地址'));die;
            }
            //获取符合条件的兑换礼品和总积分及运费
            $pointprod_arr = $this->getLegalPointGoods($pgoods_id,$buynum);
            //验证积分数是否足够
            $member_model = Model('member');
            $member_info = $member_model->infoMember(array('member_id'=>$_SESSION['member_id']),'member_points');
            if (intval($member_info['member_points']) < $pointprod_arr['totlepoints']){
                output_data(array('error'=>1,'msg'=>'积分不足，请兑换其他礼品'));die;
            }
            //实例化兑换订单模型
            $pointorder_model= Model('pointorder');
            //实例化店铺模型
            $order_array		= array();
            $order_array['point_ordersn']		= $pointorder_model->point_snOrder();
            $order_array['point_buyerid']		= $_SESSION['member_id'];
            $order_array['point_buyername']		= $_SESSION['member_name'];
            $order_array['point_buyeremail']	= $_SESSION['member_email'];
            $order_array['point_addtime']		= time();
            $order_array['point_outsn']			= $pointorder_model->point_outSnOrder();
            $order_array['point_allpoint']		= $pointprod_arr['totlepoints'];
            $order_array['point_orderamount']	= $pointprod_arr['pgoods_freightall'];
            $order_array['point_shippingcharge']= $pointprod_arr['pgoods_freightcharge'];
            $order_array['point_shippingfee']	= $pointprod_arr['pgoods_freightall'];
            $order_array['point_ordermessage']	= trim($_POST['pcart_message']);
            $order_array['point_orderstate']	= 20;//状态为已经确认收款
            $order_id	= $pointorder_model->addPointOrder($order_array);
            if (!$order_id){
                    output_data(array('error'=>1,'msg'=>'兑换操作失败'));die;
            }
            //扣除会员积分
            $points_model = Model('points');
            $insert_arr['pl_memberid'] = $_SESSION['member_id'];
            $insert_arr['pl_membername'] = $_SESSION['member_name'];
            $insert_arr['pl_points'] = -$pointprod_arr['totlepoints'];
            $insert_arr['point_ordersn'] = $order_array['point_ordersn'];
            $points_model->savePointsLog('pointorder',$insert_arr,true);
            
            //添加订单中的礼品信息
            $pointprod_model = Model('pointprod');
            $order_goods_array	= array();
            $order_goods_array['point_orderid']		= $order_id;
            $order_goods_array['point_goodsid']		= $pointprod_arr['pgoods_id'];
            $order_goods_array['point_goodsname']	= $pointprod_arr['pgoods_name'];
            $order_goods_array['point_goodspoints']	= $pointprod_arr['pgoods_points'];
            $order_goods_array['point_goodsnum']	= $pointprod_arr['quantity'];
            $order_goods_array['point_goodsimage']	= $pointprod_arr['pgoods_image'];
            $pointorder_model->addPointOrderProd($order_goods_array);
            
            //更新积分礼品库存
            $pointprod_uparr = array();
            $pointprod_uparr['pgoods_salenum'] = array('value'=>$pointprod_arr['quantity'],'sign'=>'increase');
            $pointprod_uparr['pgoods_storage'] = array('value'=>$pointprod_arr['quantity'],'sign'=>'decrease');
            $pointprod_model->updatePointProd($pointprod_uparr,array('pgoods_id'=>$pointprod_arr['pgoods_id']));
            unset($pointprod_uparr);
            unset($order_goods_array);

            //保存买家收货地址
            $address_model = Model('address');
            if($address_id > 0) {
                    $address_info = $address_model->getOneAddress($address_id);
                    //sql注入过滤转义
                    if (!empty($address_info) && !get_magic_quotes_gpc()){
                            foreach ($address_info as $k=>$v){
                                    $address_info[$k] = addslashes(trim($v));
                            }
                    }
            }
            
            //添加订单收货地址
            if (is_array($address_info) && count($address_info)>0){
                    $address_array		= array();
                    $address_array['point_orderid']		= $order_id;
                    $address_array['point_truename']	= $address_info['true_name'];
                    $address_array['point_areaid']		= $address_info['area_id'];
                    $address_array['point_areainfo']	= $address_info['area_info'];
                    $address_array['point_address']		= $address_info['address'];
                    $address_array['point_zipcode']		= $address_info['zip_code'];
                    $address_array['point_telphone']	= $address_info['tel_phone'];
                    $address_array['point_mobphone']	= $address_info['mob_phone'];
                    $pointorder_model->addPointOrderAddress($address_array);
            }
            output_data(array('error'=>0,'msg'=>'兑换礼品成功','order_id'=>$order_id));die;
//            @header("Location:index.php?act=pointcart&op=step3&order_id=".$order_id);
    }
    
    /**
     * 流程第三步
     */
    public function step3Op($order_arr=array()) {
        
    }
    
    
    /**
     * 验证商品是否符合兑换条件，并返回符合条件的积分礼品和对应的总积分总运费及其他信息
     * @return array
     */
    private function getLegalPointGoods($pgoods_id,$buynum) {
        //查询积分礼品信息
        $pointprod_model = Model('pointprod');
        $pointprod_info = $pointprod_model->getPointProdInfo(array('pgoods_id' => $pgoods_id));
        if (!is_array($pointprod_info) || count($pointprod_info) <= 0) {
            Tpl::output('errorInfo','记录信息错误');
        }
        //是否限购
        if($pointprod_info['pgoods_islimit'] && $buynum > $pointprod_info['pgoods_limitnum']){
            Tpl::output('errorInfo','每人限购一件');
        }
        //验证积分礼品兑换状态
        $ex_state = $pointprod_model->getPointProdExstate($pointprod_info);
        switch ($ex_state) {
            case 'going':
                //验证兑换数量是否合法
                $quantity = $pointprod_model->getPointProdExnum($pointprod_info, $buynum);
                if($quantity){
                    $pointprod_info['quantity'] = $quantity;
                    //计算礼品积分数
                    $pointprod_info['totlepoints'] = intval($quantity) * intval($pointprod_info['pgoods_points']);
                }
                break;
            default:
                Tpl::output('errorInfo','无法兑换当前商品');
        }
        return $pointprod_info;
    }
    
    /**
     * 获取用户地址详情
     */
    public function addressInfoOp(){
        //输出用户默认收货地址
        $addressInfo = Model('address')->getDefaultAddressInfo(array('member_id'=>$_SESSION['member_id']));
        output_data($addressInfo);
    }
}
