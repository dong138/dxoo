<? include handler('template')->file('@admin/header'); ?>
<?=ui('loader')->addon('picker.date')?>
<?=ui('loader')->js('@jquery.hook')?>
<?=ui('loader')->js('@jquery.idTabs')?>
<?=ui('loader')->js('@jquery.form')?>

<?=ui('loader')->css('#admin/css/product.mgr')?>
<?=ui('loader')->css('#admin/js/product.mgr')?>
<?=ui('loader')->addon('editor.kind')?>
<?=ui('loader')->js('@lhgdialog')?>
<?=ui('loader')->js('@validform')?>
<?=ui('loader')->css('@valid.style')?>
<?=ui('loader')->js('@json2')?>
<?=ui('loader')->js('@jquery.autocomplete')?>
<?=ui('loader')->css('@jquery.autocomplete')?>
<form action="<?=$action?>" id="subForm" method="post"  enctype="multipart/form-data">
<input type="hidden" name="FORMHASH" value='<?=FORMHASH?>'/>
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">商家优惠券信息</td>
		</tr>
		<tr>
			<td width="#F4F8FC" class="td_title">商家：</td>
            <td><input type="hidden" id="sellerids" name="sellerid" value="<?=$list['sellerid']?>">
				<input type="text" id="autocomplete" size="28" value="<?=$list['sellername']?>" placeholder="请输入商家" />
			</td>
			<!-- <td bgcolor="#F4F8FC" class="td_title">商家</td>
			<td align="right"><input type="text" name="autocomplete" class="isearcher_input_words" id="autocomplete" size="28" 
			value="{ : null;}" placeholder="请输入商家" datatype="*"  errormsg="请输入" sucmsg=" "/></td> -->
		</tr>
		<tr>
			<td bgcolor="#F4F8FC" class="td_title">推送年/月</td>
			<td align="right">
			<input type="text"
				onfocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy年MM月',startDate:'<? echo $list['pushtime'] ? date('Y-m', strtotime($list['pushtime'])) : date('Y-m', time());; ?>',vel:'pushtime'})"
				size="28" class="Wdate" value="<? echo $list['pushtime'] ? date('Y年m月', strtotime($list['pushtime'])) : date('Y年m月', time());; ?>"  datatype="*" errormsg="请选择" sucmsg=" "/>
			<input name="pushtime" type="hidden" id="pushtime" value="<? echo $list['pushtime'] ? date('Y-m', strtotime($list['pushtime'])) : date('Y-m', time());; ?>" datatype="*"  errormsg="请输入" sucmsg=" " /></td>
		</tr>
		<tr>
		<tr>
			<td bgcolor="#F4F8FC" class="td_title">已用次数</td>
			<td align="right"><input name="usednum" size="28" type="text" id="usednum" value="<?=$list['usednum']?>" datatype="n" sucmsg=" " /></td>
		</tr>
		<tr>
			<td bgcolor="#F4F8FC" class="td_title">总次数</td>
			<td align="right"><input name="num" size="28" type="text" id="num" value="<?=$list['num']?>" datatype="n" sucmsg=" " /></td>
		<tr>
			<td bgcolor="#F4F8FC" class="td_title">状态</td>
			<td align="right">
				<select name="status" id="status" datatype="*"  errormsg="请输入" sucmsg=" " >
					<option value=" ">===请选择===</option>
					<option value="0" 
<? if($list['status'] == '0') { ?>
selected="selected"
<? } ?>
 >已过期</option>
					<option value="1" 
<? if($list['status'] == '1') { ?>
selected="selected"
<? } ?>
 >未过期</option>
				</select>
			</td>
		</tr>
	</table>
	<br>
	<center>
	<input type="hidden" name="id" value="<?=$list['id']?>"/>
	<input type="hidden" name="sellerid" id="sellerid" value="<?=$list['sellerid']?>"/>
	<input type="submit" class="button" id="submitButton" name="addsubmit" value="提 交">
	</center>
</form>
<script type="text/javascript">
var api = frameElement.api, W = api.opener;
	$(function(){
		$("#subForm").Validform({
			tiptype:3,
			showAllError: true
		});
/* 	    function showResponse() {
	    	api.reload();
	   	}; */
	});
	 $(function() {
			function format(mail) {
				return mail.sellername;
			}
			$("#autocomplete").autocomplete('?mod=search&code=ajaxSeller', {
				multiple: false,
				matchSubset:true,
				multipleSeparator: "",
				dataType: "json",
				extraParams: {},
				parse: function(data) {
					return $.map(data, function(row) {
						return {
							data: row,
							value: row.sellername,
							result: row.sellername
						}
					});
				},
				formatItem: function(item) {
					return format(item);
				}
			}).result(function(e, item) {
				$("#autocomplete").val(item.sellername);
				$("#sellerid").val(item.id);
				//alert($("#sellerid").val()+'====='+$("#autocomplete").val());return;
				//window.hell.location = '?mod=ticket&code=product&id=<?=$projectid?>&type=' + type + '&commonid=' + item.id;
			});
		});
</script>
<? include handler('template')->file('@admin/footer'); ?>