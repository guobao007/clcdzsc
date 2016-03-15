<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>sfaasdf</title>
<style>
.back2top,#back2top,.public-nav-layout,.header-wrap,.public-top-layout,.fuwu,.faq,.footer,.back2top,.public-nav-layout{ display:none}
#footerb,#faq,#footer,.fullimg,.home-focus-layout{ display:none}
</style><!---->
</head>
<body>
<?php 
echo $output['web_content'];
?>
<script>
$(function() { 

  for ($x=0; $x<<?php echo $output['pic_count'];?>; $x++) {
     $w=$("#picture_"+$x).attr('width');
     $h=$("#picture_"+$x).attr('height');    
     $("#picture_"+$x).parent().attr("href",'JavaScript:show_webdialog('+$x+',"'+$w+'X'+$h+'");');
     $("#picture_"+$x).parent().attr("target","");        
  }
}); 
</script>


<div id="upload_act_dialog" class="upload_act_dialog" style="display:none">

  <table class="table tb-type2">
    <tbody>
      <tr class="space odd" id="prompt">
        <th class="nobg" colspan="12"><div class="title">
            <h5>操作提示</h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li>请按照操作注释要求，上传设置板块区域左侧的活动图片。</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form id="upload_act_form" name="upload_act_form" enctype="multipart/form-data" method="post" action="index.php?act=web_api&op=upload_webpic" target="upload_pic">
    <input type="hidden" name="form_submit" value="ok" />
    <input name="web_id" value="<?php echo $_GET['web_id'] ;?>" type="hidden">   
    <input name="var_name"  id="var_name" value="" type="hidden">
   <table class="table tb-type2" id="upload_act_type_pic" >
      <tbody>
        <tr>
          <td colspan="2" class="required">商品名称：</td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          	<input class="txt" type="text" name="web_title" value="" id="web_title">
       	  </td>
          <td class="vatop tips"></td>
        </tr>
        
          <tr>
          <td colspan="2" class="required">描述：</td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
         <textarea name="web_content" cols="45" rows="5" id="web_content"></textarea>
       	  </td>
          <td class="vatop tips"></td>
        </tr>
                <tr>
          <td colspan="2" class="required">商品价格：</td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          	<input class="txt" type="text" name="web_price" value="" id="web_price">
       	  </td>
          <td class="vatop tips"></td>
        </tr>
        
           <tr>
          <td colspan="2" class="required">市场价格：</td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          	<input class="txt" type="text" name="web_priceold" value="" id="web_priceold">
       	  </td>
          <td class="vatop tips"></td>
        </tr>
            <tr>
          <td colspan="2" class="required">库存：</td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          	<input class="txt" type="text" name="web_storage" value="" id="web_storage">
       	  </td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><label>图片跳转链接：</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input name="web_url" type="text" class="txt" id="web_url" value="<?php echo !empty($output['code_act']['code_info']['url']) ? $output['code_act']['code_info']['url']:SHOP_SITE_URL;?>"></td>
          <td class="vatop tips">输入点击该图片后所要跳转的链接地址。</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label>图片上传：</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><span class="type-file-box">
            <input type='text' name='textfield' id='textfield1' class='type-file-text' />
            <input type='button' name='button' id='button1' value='' class='type-file-button' />
            <input name="pic" id="pic" type="file" class="type-file-file" size="30">          </span></td>
          <td class="vatop tips">建议上传大小尺寸：<span id="chicun"></span>像素GIF\JPG\PNG格式图片，超出规定范围的图片部分将被自动隐藏。</td>
        </tr>
      </tbody>
    </table>
    <a href="JavaScript:void(0);" onClick="$('#upload_act_form').submit();" class="btn"><span>提交</span></a>
  </form>

</div>

<iframe style="display:none;" src="" name="upload_pic"></iframe>

<script>

$('#edit_store').on('click',function(){
  //  $(this).hide();
		
   // disableOtherEdit('如需修改，请先保存');
   // $(this).parent().parent().addClass('current_box');	
   // $('#upload_act_dialog').load('index.php?act=web_config&op=web_mbupload');
});

</script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/perfect-scrollbar.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/web_config/web_index.js"></script>
</body>
</html>