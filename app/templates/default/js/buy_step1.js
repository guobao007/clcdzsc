$(function() {
	var pgid = GetQueryString("pgid");
        var number = GetQueryString("buynum");
        var cart_id = pgid+'|'+number;
        var data = {key:key,cart_id:cart_id};
        
        
        $.ajax({//获取地址信息
            type: 'post',
            url: ApiUrl + '/index.php?act=pointcart&op=addressInfo',
            dataType: 'json',
            data: data,
            success: function (result) {
                var data = result.datas;
                if (data == '') {//收获地址是否存在
                    var thisPrarent = $(".buys1-address-cnt");
                    hideDetail(thisPrarent);
                }
            }
        });
        
        $.ajax({//获取区域列表
		type:'post',
		url:ApiUrl+'/index.php?act=member_address&op=area_list',
		data:{key:key},
		dataType:'json',
		success:function(result){
			var data = result.datas;
			var prov_html = '';
			for(var i=0;i<data.area_list.length;i++){
				prov_html+='<option value="'+data.area_list[i].area_id+'">'+data.area_list[i].area_name+'</option>';
			}
			$("select[name=prov]").append(prov_html);
		}
	});
        
	$("select[name=prov]").change(function(){//选择省市
		var prov_id = $(this).val();
		$.ajax({
			type:'post',
			url:ApiUrl+'/index.php?act=member_address&op=area_list',
			data:{key:key,area_id:prov_id},
			dataType:'json',  	
			success:function(result){
				var data = result.datas;
				var city_html = '<option value="">请选择...</option>';
				for(var i=0;i<data.area_list.length;i++){
					city_html+='<option value="'+data.area_list[i].area_id+'">'+data.area_list[i].area_name+'</option>';
				}
				$("select[name=city]").html(city_html);
				$("select[name=region]").html('<option value="">请选择...</option>');
			}
		});
	});
	
	$("select[name=city]").change(function(){//选择城市
		var city_id = $(this).val();
		$.ajax({
			type:'post',
			url:ApiUrl+'/index.php?act=member_address&op=area_list',
			data:{key:key,area_id:city_id},
			dataType:'json',  	
			success:function(result){
				var data = result.datas;
				var region_html = '<option value="">请选择...</option>';
				for(var i=0;i<data.area_list.length;i++){
					region_html+='<option value="'+data.area_list[i].area_id+'">'+data.area_list[i].area_name+'</option>';
				}
				$("select[name=region]").html(region_html);
			}
		});
	});	
	
    $(".buys1-edit-address").click(function(){//修改收获地址
        var self = this;
        $.ajax({
        	url:ApiUrl+"/index.php?act=member_address&op=address_list",
        	type:'post',
        	data:{key:key},
        	dataType:'json',
        	success:function(result){
        		var data = result.datas;
        		var html = '';
        		for(var i=0;i<data.address_list.length;i++){
        			html+='<li class="current">'
			                    +'<label>'
			                        +'<input type="radio" name="address" checked="checked" class="rdo address-radio" value="'+data.address_list[i].address_id+'"/>'
			                        +'<span class="mr5 rdo-span"><span class="true_name_'+data.address_list[i].address_id+'">'+data.address_list[i].true_name+'</span> <span class="address_id_'+data.address_list[i].address_id+'">'+data.address_list[i].area_info+' '+data.address_list[i].address+'</span> <span class="mob_phone_'+data.address_list[i].address_id+'">'+data.address_list[i].mob_phone+'</span></span>'
			                    +'</label>'
			                    +'<a class="del-address" href="javascript:void(0);" address_id="'+data.address_list[i].address_id+'">[删除]</a>'
                    		+'</li>';
        		}
        		$('li[class=current]').remove();
        		$('#addresslist').before(html);
        		
        		$('.del-address').click(function(){
                    var $this = $(this);
        			var address_id = $(this).attr('address_id');
        			$.ajax({
        				type:'post',
        				url:ApiUrl+'/index.php?act=member_address&op=address_del',
        				data:{key:key,address_id:address_id},
        				dataType:'json',
        				success:function(result){
        					$this.parent('li').remove();
        				}
        			});
        		});
        	}
        });
        var thisPrarent = $(this).parents(".buys1-address-cnt");
        hideDetail(thisPrarent);
    });
    $(".buys1-edit-invoice").click(function(){
        var self = this;

        var thisPrarent = $(this).parents(".buys1-invoice-cnt");
        hideDetail(thisPrarent);
    });
    
	$.sValid.init({//地址验证
        rules:{
        	vtrue_name:"required",
        	vmob_phone:"required",
            vprov:"required",
            vcity:"required",
            vregion:"required",
            vaddress:"required",
        },
        messages:{
        	vtrue_name:"姓名必填！",
        	vmob_phone:"手机号必填！",
            vprov:"省份必填！",
            vcity:"城市必填！",
            vregion:"区县必填！",
            vaddress:"街道必填！",
        },
        callback:function (eId,eMsg,eRules){
            if(eId.length >0){
                var errorHtml = "";
                $.map(eMsg,function (idx,item){
                    errorHtml += "<p>"+idx+"</p>";
                });
                $(".error-tips").html(errorHtml).show();
            }else{
                 $(".error-tips").html("").hide();
            }
        }  
    });
	
    $(".save-address").click(function (){//更换收获地址	
        var self = this;
        var selfPr
        //获取address_id
        var addressRadio = $('.address-radio');
        var address_id;
        for(var i =0;i<addressRadio.length;i++){
            if(addressRadio[i].checked){
                address_id = addressRadio[i].value;
            }
        }
        if(address_id>0){//变更地址
        	$('#address').html($('.address_id_'+address_id).html());
                $('#true_name').html($('.true_name_'+address_id).html());
                $('#mob_phone').html($('.mob_phone_'+address_id).html());
                $('#address_id').val(address_id);
        }else{//保存地址
			if($.sValid()){
				var index = $('select[name=prov]')[0].selectedIndex;
				var aa = $('select[name=prov]')[0].options[index].innerHTML;
				
				
				var true_name = $('input[name=true_name]').val();
				var mob_phone = $('input[name=mob_phone]').val();
				var tel_phone = $('input[name=tel_phone]').val();
				var city_id = $('select[name=city]').val();
				var area_id = $('select[name=region]').val();
				var address = $('textarea[name=address]').val();
				
				var prov_index = $('select[name=prov]')[0].selectedIndex;
				var city_index = $('select[name=city]')[0].selectedIndex;
				var region_index = $('select[name=region]')[0].selectedIndex;	
				var area_info = $('select[name=prov]')[0].options[prov_index].innerHTML+' '+$('select[name=city]')[0].options[city_index].innerHTML+' '+$('select[name=region]')[0].options[region_index].innerHTML;

				//ajax 提交收货地址
				$.ajax({
					type:'post', 
					url:ApiUrl+'/index.php?act=member_address&op=address_add',
					data:{key:key,true_name:true_name,mob_phone:mob_phone,tel_phone:tel_phone,city_id:city_id,area_id:area_id,address:address,area_info:area_info},
					dataType:'json',
					success:function(result){
						if(result){
							$.ajax({//获取收货地址信息
								type:'post',
								url:ApiUrl+'/index.php?act=member_address&op=address_info',
								data:{key:key,address_id:result.datas.address_id},
								dataType:'json',
								success:function(result1){
									var data1 = result1.datas;
									$('#true_name').html(data1.address_info.true_name);
									$('#address').html(data1.address_info.area_info+' '+data1.address_info.address);
									$('#mob_phone').html(data1.address_info.mob_phone);
									
									$('input[name=address_id]').val(data1.address_info.address_id);
									$('input[name=area_id]').val(data1.address_info.area_id);
									$('input[name=city_id]').val(data1.address_info.city_id);
									
									var area_id = data1.address_info.area_id;
									var city_id = data1.address_info.city_id;
									var freight_hash = $('input[name=freight_hash]').val();
									
									$.ajax({//保存收货地址
										type:'post', 
										url:ApiUrl+'/index.php?act=member_buy&op=change_address',
										data:{key:key,area_id:area_id,city_id:city_id,freight_hash:freight_hash},
										dataType:'json',
										success:function(result){
											var data = result.datas;																						
											var sp_s_total = 0;
											$.each(result.datas.content,function(k,v){
												$('#store'+k).html(v);
						        				var sp_toal = parseInt($('#st'+k).attr('store_price'));//店铺商品价格
						        				sp_s_total = v+sp_s_total;
						        				$('#st'+k).html(eval(sp_toal+v));
											});	

											var total_price = eval(parseInt($('input[name=total_price]').val())+sp_s_total);
											$('#total_price').html(total_price);	
											return false;
										}
									});
								}
							});
						}
					}
				});
			}else{
				return false;
			}
        }
        
        var thisPrarent = $(this).parents(".buys1-address-cnt");
        showDetial(thisPrarent);
    });
    
    $('#buy_step2').click(function(){//提交订单step2
    	var data = {};
    	data.key = key;
    	data.cart_id = cart_id;
    	//获取address_id
    	var address_id = $('input[name=address_id]').val();
    	data.address_id = address_id;
    	
        $.ajax({
        	type:'post',
        	url:ApiUrl+'/index.php?act=pointcart&op=step2',
        	data:data,
        	dataType:'json',
        	success:function(result){
                    var data = result.datas;
                    if(data.error == 1){
                        $.sDialog({
                            skin:"red",
                            content:'<p>'+data.msg+'</p>',
                            okBtn:false,
                            cancelBtn:false
                        });
                    }else{
                        $.sDialog({
                            autoTime: '5000',
                            skin:"red",
                            content:'<p>'+data.msg+'</p>',
                            okBtn:true,
                            cancelBtn:false,
                            "okFn": function() {
                                window.location.href = ApiUrl+'/index.php?act=pointprod&key='+key;
                            }
                        });
                    }
        	}
        });
    });
    
    function showDetial(parent){
        $(parent).find(".buys1-edit-btn").show();
        $(parent).find(".buys1-hide-list").addClass("hide");
        $(parent).find(".buys1-hide-detail").removeClass("hide");
    }
    function hideDetail(parent){
        $(parent).find(".buys1-edit-btn").hide();
        $(parent).find(".buys1-hide-list").removeClass("hide");
        $(parent).find(".buys1-hide-detail").addClass("hide");
    }
    
    function GetQueryString(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r!=null) return unescape(r[2]); return null;
    } 
});