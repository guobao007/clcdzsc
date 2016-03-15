<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>微信菜单设置</h3>    
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" enctype="multipart/form-data" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <?php foreach($output['menu_list'] as $key=>$val){?>
		<td><table width="100%" align="center" cellspacing="5" id="one-table">
          <tr>
            <td class="label">级别&nbsp;&nbsp;</td>
            <td><strong>类型</strong></td>
            <td><strong>名称</strong></td>
            <td><strong>值</strong></td>
          </tr>
          <tr>
            <td class="label"><strong>一级菜单</strong>：</td>
            <td><select name="menu_type<?php echo $val['id'];?>" id="menu_type<?php echo $val['id'];?>">
                <option value="click" <?php if($val['menu_type']=='click'){echo 'selected ';}?>>click</option>
                <option value="view"  <?php if($val['menu_type']=='view'){echo 'selected ';}?>>view</option>
              </select>
            </td>
            <td><label>
              <input name="name<?php echo $val['id'];?>" type="text" id="name<?php echo $val['id'];?>" value="<?php echo $val['name'];?>" size="8"/>
              </label>
            </td>
            <td><label>
              <input name="value<?php echo $val['id'];?>" type="text" id="<?php echo $val['id'];?>" value="<?php echo $val['value'];?>" size="8"/>
              </label>
            </td>
          </tr>
		  <?php foreach($val['son_list'] as $k=>$v){?>
          <tr>
            <td class="label">二级菜单：</td>
            <td><select name="menu_type<?php echo $v['id'];?>">
                <option value="click" <?php if($v['menu_type']=='click'){echo 'selected ';}?> >click</option>
                <option value="view"  <?php if($v['menu_type']=='view'){echo 'selected ';}?> >view</option>
              </select>
            </td>
            <td><label>
               <input name="name<?php echo $v['id'];?>" type="text" id="name<?php echo $v['id'];?>" value="<?php echo $v['name'];?>" size="8"/>
              </label>
            </td>
            <td><label>
              <input name="value<?php echo $v['id'];?>" type="text" id="<?php echo $v['id'];?>" value="<?php echo $v['value'];?>" size="8"/>
              </label>
            </td>
          </tr>
		  <?php }?>
        </table></td>
		<?php }?>
		
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"> <a href="JavaScript:void(0);" class="btn" onClick="document.form1.submit()"><span><?php echo $lang['nc_submit'];?></span></a></td>
  </tr>
</table>

  </form>
</div>

