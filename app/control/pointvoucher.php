<?php

/**
 * 积分兑换代金券
 *
 * @author QT
 */
defined('InShopNC') or exit('Access Invalid!');
define('APP_SITE_URL', 'http://ahxbgw.com/app');
define('TPL_SHOP_NAME','default');
define('SHOP_TEMPLATES_URL',APP_SITE_URL.'/templates/'.TPL_SHOP_NAME);
class pointvoucherControl extends mobilePointControl{
    
    /**
     *  代金券列表
     */
    public function vlistOp(){
        $model = Model();
        //查询代金券列表
        $field = 'voucher_template.*,store.store_id,store.store_label,store.store_name,store.store_domain';
        $on = 'voucher_template.voucher_t_store_id=store.store_id';
        $voucherlist = $model->table('voucher_template,store')->field($field)->join('left')->on($on)->where(array('voucher_t_state'=>1,'voucher_t_end_date'=>array('gt',time())))->order('voucher_t_id DESC')->page(10)->select();
        $page_total = $model->gettotalpage();
        $pageInfo = mobile_page($page_total);
        if (!empty($voucherlist)){
            foreach ($voucherlist as $k=>$v){
                if (!empty($v['voucher_t_customimg'])){
                    $voucherlist[$k]['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.ATTACH_VOUCHER.DS.$v['voucher_t_store_id'].DS.$v['voucher_t_customimg'];
                }else{
                    $voucherlist[$k]['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(240);
                }
                $voucherlist[$k]['voucher_t_limit'] = intval($v['voucher_t_limit']);
            }
        }
        Tpl::output('title','代金券兑换');
        Tpl::output('voucherlist',$voucherlist);
        Tpl::output('pageInfo',$pageInfo);
        Tpl::output('curpage',$_GET['curpage']);
        Tpl::showpage('pointvoucher_list');
    }
    /**
     * 代金券详情
     */
    public function vinfoOp(){
        $vid = intval($_GET['vid']);
        if(empty($vid)){
            Tpl::output('errorInfo','参数错误，稍后重试');
        }
        //查询代金券信息
        $model = Model();
        $field = 'voucher_template.*,store.store_id,store.store_label,store.store_name,store.store_domain';
        $on = 'voucher_template.voucher_t_store_id=store.store_id';
        $voucher_info = $model->table('voucher_template,store')->field($field)->join('left')->on($on)->where(array('voucher_t_id'=>$vid,'voucher_t_state'=>1,'voucher_t_end_date'=>array('gt',time())))->find();
        if (empty($voucher_info)){
            Tpl::output('errorInfo','代金券信息错误');
        }else{
			if (!empty($voucher_info['voucher_t_customimg'])){
                $voucher_info['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.ATTACH_VOUCHER.DS.$voucher_info['voucher_t_store_id'].DS.$voucher_info['voucher_t_customimg'];
            }else{
                $voucher_info['voucher_t_customimg'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(360);
            }
            Tpl::output('voucher_info',$voucher_info);
        }
        Tpl::output('title','代金券详情');
        Tpl::showpage('pointvoucher_info');
    }
    
    /**
     * 兑换代金券
     */
    public function voucherexchange_saveOp() {
		if (C('pointprod_isuse') != 1){
            output_data(array('error'=>1,'msg'=>'未开启积分兑换功能'));die;
        }
        if (empty($_SESSION['member_id'])) {
            output_data(array('error'=>1,'msg'=>'参数错误，请登录后操作'));die;
        }
        $vid = intval($_POST['vid']);
        if ($vid <= 0) {
            output_data(array('error'=>1,'msg'=>'参数错误，稍后重试'));die;
        }
        $model = Model('voucher');
        //查询代金券信息
        $voucher_info = $model->getUsableTemplateInfo($vid);
        if (empty($voucher_info)) {
            output_data(array('error'=>1,'msg'=>'代金券信息错误'));die;
        }
        //获取用户是否有店铺
        $storeInfo = Model('store')->field('store_id')->where(array('member_id'=>$_SESSION['member_id']))->find();
        //验证是否为店铺自己
        if ($storeInfo['store_id'] == $voucher_info['voucher_t_store_id']) {
            output_data(array('error'=>1,'msg'=>'不可以兑换自己店铺的代金券'));die;
        }
        $voucher_list = $model->table('voucher')->where(array('voucher_owner_id' => $_SESSION['member_id'], 'voucher_store_id' => $voucher_info['voucher_t_store_id'], 'voucher_end_date' => array('gt', time())))->select();
        if (!empty($voucher_list)) {
            $voucher_count = 0; //在该店铺兑换的代金券数量
            $voucherone_count = 0; //该张代金券兑换的次数
            foreach ($voucher_list as $k => $v) {
                if ($v['voucher_state'] == 1) {
                    $voucher_count += 1;
                }
                if ($v['voucher_t_id'] == $voucher_info['voucher_t_id']) {
                    $voucherone_count += 1;
                }
            }
            //买家最多只能拥有同一个店铺尚未消费抵用的店铺代金券最大数量的验证
            if ($voucher_count >= intval(C('promotion_voucher_buyertimes_limit'))) {
                $message = sprintf('您的可用代金券已有%s张,不可再兑换了', C('promotion_voucher_buyertimes_limit'));
                output_data(array('error'=>1,'msg'=>$message));die;
            }
            //同一张代金券最多能兑换的次数
            if (!empty($voucher_info['voucher_t_eachlimit']) && $voucherone_count >= $voucher_info['voucher_t_eachlimit']) {
                $message = sprintf('该代金券您已兑换%s次，不可再兑换了', $voucher_info['voucher_t_eachlimit']);
                output_data(array('error'=>1,'msg'=>$message));die;
            }
        }
        //查询会员信息
        $member_info = $model->table('member')->field('member_points')->where(array('member_id' => $_SESSION['member_id']))->find();
        if (empty($member_info)) {
            output_data(array('error'=>1,'msg'=>'参数错误，稍后重试'));die;
        }
        if (intval($member_info['member_points']) < intval($voucher_info['voucher_t_points'])) {
            output_data(array('error'=>1,'msg'=>'您的积分不足，暂时不能兑换该代金券'));die;
        }
        //添加代金券信息
        $insert_arr = array();
        $insert_arr['voucher_code'] = $model->get_voucher_code();
        $insert_arr['voucher_t_id'] = $voucher_info['voucher_t_id'];
        $insert_arr['voucher_title'] = $voucher_info['voucher_t_title'];
        $insert_arr['voucher_desc'] = $voucher_info['voucher_t_desc'];
        $insert_arr['voucher_start_date'] = time();
        $insert_arr['voucher_end_date'] = $voucher_info['voucher_t_end_date'];
        $insert_arr['voucher_price'] = $voucher_info['voucher_t_price'];
        $insert_arr['voucher_limit'] = $voucher_info['voucher_t_limit'];
        $insert_arr['voucher_store_id'] = $voucher_info['voucher_t_store_id'];
        $insert_arr['voucher_state'] = 1;
        $insert_arr['voucher_active_date'] = time();
        $insert_arr['voucher_owner_id'] = $_SESSION['member_id'];
        $insert_arr['voucher_owner_name'] = $_SESSION['member_name'];
        //扣除会员积分
        $points_model = Model('points');
        $points_arr['pl_memberid'] = $_SESSION['member_id'];
        $points_arr['pl_membername'] = $_SESSION['member_name'];
        $points_arr['pl_points'] = -$voucher_info['voucher_t_points'];
        $points_arr['point_ordersn'] = $insert_arr['voucher_code'];
        $points_arr['pl_desc'] = Language::get('home_voucher') . $insert_arr['voucher_code'] . Language::get('points_pointorderdesc');
        $points_model->savePointsLog('app', $points_arr, true);
        $result = $model->table('voucher')->insert($insert_arr);
        if ($result) {
            //代金券模板的兑换数增加
            $model->table('voucher_template')->where(array('voucher_t_id' => $voucher_info['voucher_t_id']))->update(array('voucher_t_giveout' => array('exp', 'voucher_t_giveout+1')));
            output_data(array('error'=>0,'msg'=>'兑换成功'));die;
        } else {
            output_data(array('error'=>1,'msg'=>'兑换失败'));die;
        }
    }
}
