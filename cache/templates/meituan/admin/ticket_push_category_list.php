<? include handler('template')->file('@admin/header'); ?>
<?=ui('loader')->js('#admin/js/sdb.parser')?> 
<form method="post"  action="<?=$action?>">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/>
	<table cellspacing="1" cellpadding="4" width="100%" align="center"
		class="tableborder">
		<tr class="header">
			<td colspan="12"><b style="float: left;">优惠券推送管理</b> <a
				style="float: left; margin-left: 5px"
				href="?mod=ticket_push&code=Add&id=<?=$id?>" class="back1 back2">添加优惠券推送</a></td>
		</tr>
<? if(!empty($list)) { ?>
		<tr>
			<td colspan="12">
				<div class="tr_select">
					请输入您要搜索的优惠券关键词： <input name="keyword" value="<?=$keyword?>" id="keyword" type="text" class="isearcher_input_words" />
					<input name="bottom" type="submit" id="bottom" value="搜索" class="btn btn-primary btn-small" />
				</div>
			</td>
		</tr>
		
<? } ?>
<tr class="tr_nav">
			<td width="10%">ID</td>
			<td width="15%">优惠券推送名称</td>
			<td width="10%">图片</td>
			<td width="10%">添加日期</td>
			<td width="5%">默认推送</td>
			<td width="5%">启用/禁用</td>
			<td width="5%">同时推送</td>
			<td width="10%">推送时间</td>
			<td width="20%" align="center">管理</td>
		</tr>
<? if(empty($list)) { ?>
		<tr>
			<td colspan="12">暂时还没有优惠券分类，请<a
				href="?mod=ticket_push&code=Add&id=<?=$id?>">点此添加优惠券分类</a>。
			</td>
		</tr>
		
<? } ?>
<? if(is_array($list)) { foreach($list as $i => $value) { ?>
<tr onmouseover="this.className='tr_hover'" onmouseout="this.className='tr_normal'">
			<td><?=$value['id']?></td>
			<td><?=$value['name']?></td>
			<td><?=$value['pic']?></td>
			<td><? echo date('Y-m-d', $value['addtime']); ?></td>
			<td>
<!-- 				<a id="pushs_<?=$value['id']?>" onclick="pushSelect(<?=$value['id']?>,this)"> <img
					src="<? echo ($value['is_default']?'templates/admin/images/btn_enable.png':'templates/admin/images/btn_disable.gif'); ?>"> -->
<? if($value['is_default'] == '0') { ?>
否<? } elseif($value['is_default'] == '1') { ?>是
<? } ?>
</td>
			<td>
				<a id="enables_<?=$value['id']?>" onclick="enableSelect(<?=$value['id']?>,this)"> <img
					src="<? echo ($value['is_enable']?'templates/admin/images/btn_enable.png':'templates/admin/images/btn_disable.gif'); ?>">
			</td>
			<td>
				<a id="times_<?=$value['id']?>" onclick="timeSelect(<?=$value['id']?>,this)"> <img
					src="<? echo ($value['is_time']?'templates/admin/images/btn_enable.png':'templates/admin/images/btn_disable.gif'); ?>">
			</td>
			<td>关注<?=$value['pushtime']?>分钟后</td>
			<td align="center">
				<a href="?mod=ticket_push&code=Edit&id=<?=$value['id']?>">[ 修改 ]</a> 
				<a href="javascript:vold(0);" title="点此删除该优惠券推送"
				onclick="if(confirm('您确认要删除该优惠券推送吗？')){window.location.href='?mod=ticket_push&code=Deleteticket&id=<?=$value['id']?>'}">[ 删除 ]</a> 
				<a href="?mod=ticket_push&code=toPush&id=<?=$value['id']?>">[ 推送管理 ]</a>
				<a target="_blank" href="<? echo ini ( 'settings.iweb_site_url' ); ?>/?mod=couponticket&code=batchTicket&keys=<? echo encrypt($value['id']); ?>">[ 批量领取 ]</a>
			</td>
		</tr>
<? } } ?>
</table>
	<center><?=page_moyo()?></center>
	<table>
		<tr>
			<td colspan="12">请注意：<br>1、商家各项金额数据中，均不包括实物订单中用户支付的商品运输费用。<br>2、添加商家时绑定的商家用户将成为商家分类<?=TUANGOU_STR?>券的管理者，<font
				color=red>商家用户可前台登陆、查看旗下<?=TUANGOU_STR?>券、核对和消费</font>！
			</td>
		</tr>
	</table>
	<a href="?mod=ticket_push&code=Add&id=<?=$id?>" class="back1 back2">添加优惠券推送</a> <br>
</form>
<script type="application/javascript">
	var eimg = 'templates/admin/images/btn_enable.png';
	var dimg = 'templates/admin/images/btn_disable.gif';
    function pushSelect(id,a){
    	var e = jQuery(a).find('img').attr('src')==eimg;
        jQuery.get(
                'admin.php?mod=ticket_push&code=getPush',
                {pushid:id,is_default:e},
                function(){
                    jQuery(a).find('img').attr('src',e?dimg:eimg);
                }
        );
    }
    function enableSelect(id,a){
    	var e = jQuery(a).find('img').attr('src')==eimg;
    	var tr = jQuery("#times_"+id).find('img').attr('src')==eimg;
        jQuery.get(
                'admin.php?mod=ticket_push&code=getEnable',
                {pushid:id,is_enable:e,is_time:tr},
                function(){
                    //jQuery(a).find('img').attr('src',e?dimg:eimg);
                    if(e){
                    	jQuery(a).find('img').attr('src',dimg);
                    	if(tr){
                    		jQuery("#times_"+id).find('img').attr('src',dimg);
                    	}
                    }else{
                    	jQuery(a).find('img').attr('src',eimg);
                    } 
                }
        );
    }
    function timeSelect(id,a){
    	var e = jQuery(a).find('img').attr('src')==eimg;
    	var er = jQuery("#enables_"+id).find('img').attr('src')==eimg;
        jQuery.get(
                'admin.php?mod=ticket_push&code=getTime',
                {pushid:id,is_time:e,is_enable:er},
                function(){
                    //jQuery(a).find('img').attr('src',e?dimg:eimg);
                    if(er){
                    	jQuery(a).find('img').attr('src',e?dimg:eimg);
                    }else{
                    	alert("未启用！");
                    }
                }
        );
    }
</script>
<? include handler('template')->file('@admin/footer'); ?>