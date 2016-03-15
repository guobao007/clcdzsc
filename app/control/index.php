<?php
/**
 * cms首页
 *
 *
 *
 * @copyright  Copyright (c) 2007-2013 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');
class indexControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }


public function indexOp(){
        $model_mb_ad = Model('mb_ad');
        $model_mb_home = Model('mb_home');
        $datas = array();

        //广告
        $adv_list = array();
        $mb_ad_list = $model_mb_ad->getMbAdList(array(), null, 'link_sort asc');
        foreach ($mb_ad_list as $value) {
            $adv = array();
            $adv['image'] = $value['link_pic_url'];
            $adv['keyword'] = $value['link_keyword'];
            $adv_list[] = $adv;
        }
      //  $datas['adv_list'] = $adv_list;

        //首页
        $home_type1_list = array();
        $home_type2_list = array();
        $mb_home_list = $model_mb_home->getMbHomeList(array(), null, 'h_sort asc');
        foreach ($mb_home_list as $value) {
            $home = array();
            $home['image'] = $value['h_img_url'];
            $home['title'] = $value['h_title'];
            $home['desc'] = $value['h_desc'];
            $home['keyword'] = $value['h_keyword'];
            if($value['h_type'] == 'type1') {
                $home['keyword1'] = $value['h_multi_keyword'];
                $home_type1_list[] = $home;
            } else {
                $home_type2_list[] = $home;
            }
        }
         $model_web_ad=Model();
       	//首页导航链接 APP_导航链接值_2 
        $AdlistC = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>382))->select();
        $adv_content=array();
		foreach($AdlistC as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistC[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistC[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
        
		$AdlistC[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistC[$k]['adv_content']);
		}
        
        //首页导航链接 APP_导航链接值_2 
        $AdlistD = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>381))->select();
        $adv_content=array();
		foreach($AdlistD as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistD[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistD[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
        
		$AdlistD[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistD[$k]['adv_content']);
		}
        
        $AdlistE = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>387))->select();
        $adv_content=array();
		foreach($AdlistE as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistE[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistE[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
		$AdlistE[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistE[$k]['adv_content']);
		}
               
        
        //banner
        $adv_list = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>380))->select();
        $adv_content=array();
		foreach($adv_list as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$adv_list[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $adv_list[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
		$adv_list[$k]['adv_word']=$adv_content['adv_word'];
		unset($adv_list[$k]['adv_content']);
		} 
        $datas['adv_list'] = $adv_list;
         
            //商品列表
        $model_goods = Model('goods');
        $condition = array();
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_price,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count";
         //排序方式
        $order = 'goods_num desc,goods_id desc';
        $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order, 40);
        $page_count = $model_goods->gettotalpage();
        //处理商品列表(团购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);
        
      
        
        $datas['home1'] = $home_type1_list;
        $datas['home2'] = $home_type2_list;
        $datas['home3'] = $AdlistC;
        $datas['home4'] = $AdlistD;
        $datas['home5'] = $AdlistE;
        $datas['goods_list'] = $goods_list;
        output_data($datas);
	}
	public function indexAdOp(){        

        $datas = array();       
        $model_web_ad=Model();
        
    
        
        
        //大图广告 APP_banner_1 
        $AdlistA = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>396))->select();
        $adv_content=array();
		foreach($AdlistA as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistA[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistA[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
		$AdlistA[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistA[$k]['adv_content']);
		}
		//首页小图广告  APP_ad_3 
        $AdlistB = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>398))->select();
        $adv_content=array();
		foreach($AdlistB as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistB[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistB[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
		$AdlistB[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistB[$k]['adv_content']);
		}

      	//首页导航链接 APP_导航链接值_2 
        $AdlistC = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>397))->select();
        $adv_content=array();
		foreach($AdlistC as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistC[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistC[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
        
		$AdlistC[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistC[$k]['adv_content']);
		}
		
         //首页横条广告 APP_ad_4
        $AdlistD = $model_web_ad->table('adv')->field('adv_id,adv_content,adv_title')->where(array('ap_id'=>399))->select();
        $adv_content=array();
		foreach($AdlistD as $k => $value){			
		$adv_content=unserialize($value["adv_content"]);	
		$AdlistD[$k]["adv_pic"]=UPLOAD_SITE_URL.'/shop/adv/'.$adv_content['adv_pic'];
        $AdlistD[$k]['adv_pic_url']=$this->strreplace($adv_content['adv_pic_url']);
		$AdlistD[$k]['adv_word']=$adv_content['adv_word'];
		unset($AdlistD[$k]['adv_content']);
		}
	
        
        
        
       $datas['home1']=$AdlistA;
       $datas['home2']=$AdlistC;
       $datas['home3']=$AdlistB;
       $datas['home4']=$AdlistD;
	  // echo "<pre>";
//	print_r($datas);
       output_data($datas);
	}
    public function strreplace($str){
           $str= str_replace('&amp;','&',$str);
           $str= str_replace('&lt;','<',$str);
           $str= str_replace('&gt;','>',$str);
           return  trim($str);
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

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']); 

            unset($goods_list[$key]['store_id']);
            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['nc_distinct']);
        }

        return $goods_list;
    }

  //更新版本
  public function update_appOP(){    
	  $condition=array();
	  $condition['version']="1";
	  $condition['downurl']=" ";
	  output_data($condition);
  }

  public function store_mdzqOp(){
    $model_store = Model('store');
    $condition = array();
    $condition['store_state']=1;
	$condition['store_mdzq'] =1;
	$store_list=$model_store->field("store_id,store_name,store_address")->where($condition)->select();  
     output_data($store_list);
  }

  public function store_mdpsOp(){
    $model_store = Model('store');
    $condition = array();
    $condition['store_state']=1;
	$condition['store_mdps'] =1;
	$store_list=$model_store->field("store_id,store_name,store_address")->where($condition)->select();  
    output_data($store_list);
  }
 

  
  
}