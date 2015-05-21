<!--{template @admin/header}-->

<!-- * 支付宝配置项 * -->

{eval
	$pay = logic('pay')->SrcOne('wechatpay');
	$cfg = unserialize($pay['config']);
}

{eval logic('misc')->alipay_check_xml_file();}

{eval $ssl = function_exists('openssl_open')}

<script type="text/javascript">

</script>

<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">修改微信支付设置</td>
		</tr>
		<tr>
			<td class="td_title">支付接口类型：</td>
			<td>
				<select id="cfg_service_select" name="cfg[service]">
					<option value="js_api"{if $cfg['service']=='js_api'} selected="selected"{/if}>JS API</option>
					<option value="native_api"{if $cfg['service']=='native_api'} selected="selected"{/if}>Native API</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="23%" class="td_title">微信公众号appid：</td>
			<td width="77%">
				<input name="cfg[appid]" type="text" size="38" value="{$cfg['appid']}">
			</td>
		</tr>
		<tr>
			<td class="td_title">受理商ID（mchid）：</td>
			<td>
				<input name="cfg[mchid]" type="text" size="38" value="{$cfg['mchid']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title">商户支付密钥KEY：</td>
			<td>
				<input name="cfg[key]" type="text" size="38" value="{$cfg['key']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title">appsecret：</td>
			<td>
				<input name="cfg[appsecret]" type="text" value="{$cfg['appsecret']}" />
			</td>
		</tr>
	</table>
	<br/>
	<center>
		<input type="hidden" name="id" value="{$pay['id']}" />
		<input type="submit" name="submit" value="提 交" class="button" />
	</center>
</form>
{~ui('loader')->js('#admin/js/sdb.parser')}
<!--{template @admin/footer}-->
