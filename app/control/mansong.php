<?php
/**
 * 商户中心-满就送 
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
class mansongControl extends mobileHomeControl {

    public function __construct() {

        parent::__construct() ;

        Language::read('member_layout,promotion_mansong');

        //检查满就送是否开启
        if (intval(C('promotion_allow')) !== 1) {
            output_error('满级送功能尚未开启');

    }

    public function indexOp() {
        $this->mansong_listOp();
    }

    /**
     * 发布的满就送活动列表
     **/
    public function mansong_listOp() {
        $model_mansong = Model('p_mansong');
        $condition = array();
        $condition['state'] = 1;
        $condition['start_time'] = array('lt', TIMESTAMP);
        $condition['end_time'] = array('gt', TIMESTAMP);
        $page_count = $model_mansong->gettotalpage();
        $mansong_list = $model_mansong->getMansongList($condition, $this->page, 'state desc, end_time desc');
        
        //echo '<pre>';print_R($mansong_list);
        output_data(array('mansong_list'=>$mansong_list),mobile_page($page_count));
      

       
    }

   
    /**
     * 满就送活动详细信息
     **/
    public function mansong_detailOp() {
        //$mansong_id = intval($_GET['mansong_id']);
        $mansong_id = intval($_POST['mansong_id']);
       
        if ($mansong_id <= 0) {
            output_error('参数错误');
        }       
        $model_mansong_rule = Model('p_mansong_rule');
        $rule_info = $model_mansong_rule->getMansongRuleListByID($mansong_id);
        
      
     // echo '<pre>';print_R($rule_info);
        output_data(array('rule_info'=>$rule_info));
       
    }

   

}
