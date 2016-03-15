<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>微信设置</h3>    
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" enctype="multipart/form-data" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label for="site_name">Token:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input id="token" name="token" value="<?php echo $output['list_setting']['token'];?>" class="txt" type="text" /></td>
          <td class="vatop tips">&nbsp;</td>
        </tr>
        <!-- 商家中心logo -->
        <!-- 商家中心logo -->
        <tr>
          <td colspan="2" class="required"><label for="icp_number">AppId:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input id="appid" name="appid" value="<?php echo $output['list_setting']['appid'];?>" class="txt" type="text" /></td>
          <td class="vatop tips">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="site_phone">AppSecret :</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input id="appsecret" name="appsecret" value="<?php echo $output['list_setting']['appsecret'];?>" class="txt" type="text" /></td>
          <td class="vatop tips">&nbsp;</td>
        </tr>
   
      </tbody>
      <tfoot id="submit-holder">
        <tr class="tfoot">
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" onclick="document.form1.submit()"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>

