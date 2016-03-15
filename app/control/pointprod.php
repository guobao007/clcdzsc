<?php
/**
 * 积分兑换
 */

defined('InShopNC') or exit('Access Invalid!');
define('APP_SITE_URL', 'http://ahxbgw.com/app');
define('TPL_SHOP_NAME','default');
define('SHOP_TEMPLATES_URL',APP_SITE_URL.'/templates/'.TPL_SHOP_NAME);
class pointprodControl extends mobilePointControl{
    
    /**
     * 积分兑换列表
     */
    public function indexOp(){
        $model = Model();
        //查询最新代金券列表
        $field = 'voucher_template.*,store.store_id,store.store_label,store.store_name,store.store_domain';
        $on = 'voucher_template.voucher_t_store_id=store.store_id';
        $voucherlist = $model->table('voucher_template,store')->field($field)->join('left')->on($on)->where(array('voucher_t_state'=>1,'voucher_t_end_date'=>array('gt',time())))->order('voucher_t_id DESC')->limit(4)->select();
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

        //查询最新积分商品列表
        $model_pointsprod = Model('pointprod');
        $pointprod_list = $model_pointsprod->getPointProdListNew('*',array('pgoods_show'=>1,'pgoods_state'=>0),'pgoods_sort asc,pgoods_id desc','4',10);

        Tpl::output('title','积分兑换');
		Tpl::output('hideTop',1);
        Tpl::output('voucherlist',$voucherlist);
        Tpl::output('pointprod_list',$pointprod_list);
        Tpl::showpage('pointprod_index');
    }
    
    /**
     * 积分兑换商品列表
     */
    public function plistOp(){
        $model_pointsprod = Model('pointprod');
        $pointprod_list = $model_pointsprod->getPointProdListNew('*',array('pgoods_show'=>1,'pgoods_state'=>0),'pgoods_sort asc,pgoods_id desc','',10);
        $page_total = $model_pointsprod->gettotalpage();
        $pageInfo = mobile_page($page_total);
        Tpl::output('title','礼品兑换');
        Tpl::output('pointprod_list',$pointprod_list);
        Tpl::output('pageInfo',$pageInfo);
        Tpl::output('curpage',$_GET['curpage']);
        Tpl::showpage('pointprod_list');
    }
    
    /**
     * 积分兑换商品详情
     */
    public function pinfoOp(){
        $pid = intval($_GET['id']);
        if (!$pid){
            Tpl::output('errorInfo','参数错误');
        }
        $model = Model('pointprod');
        $prodinfo = $model->getPointProdInfoNew(array('pgoods_id'=>$pid));
        if (!is_array($prodinfo) || count($prodinfo)<=0){
            Tpl::output('errorInfo','记录信息错误');
        }
		if(!$prodinfo['pgoods_image']){
            $prodinfo['pgoods_image'] = UPLOAD_SITE_URL.DS.defaultGoodsImage(360);
        }
        //兑换按钮的可用状态
        $ex_state = $model->getPointProdExstate($prodinfo);
        
        //更新礼品浏览次数
        $model->table('points_goods')->where(array('pgoods_id'=>$pid))->update(array('pgoods_view'=>array('exp','pgoods_view+1')));
//        p($prodinfo);die;
        Tpl::output('title','礼品兑换详情');
        Tpl::output('prodinfo',$prodinfo);
        Tpl::output('ex_state',$ex_state);
        Tpl::showpage('pointprod_info');
    }
    
}