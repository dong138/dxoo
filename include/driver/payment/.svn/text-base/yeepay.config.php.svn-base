<!--{template @admin/header}-->

<!-- * 易宝一键支付配置项 * -->

{eval
$pay = logic('pay')->SrcOne('yeepay');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">修改易宝一键支付设置</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">商户编号：</td>
            <td width="77%">
                <input name="cfg[merchantaccount]" type="text" size="38" value="{$cfg['merchantaccount']}">
				<span><font color="red">开通易宝支付，直接联系易宝-天天负责人QQ：862530928，或者联系天天客服QQ：862530928</font></span>
			</td>
        </tr>
		<tr>
            <td width="23%" class="td_title">业务开通范围：</td>
            <td width="77%">
                <input name="cfg[yeepayType]" id="yeepayType_1" type="radio" value="1" {if $cfg['yeepayType'] == '1'}checked{/if} class="radio"><label for="yeepayType_1">仅移动业务</label>
				<input name="cfg[yeepayType]" id="yeepayType_2" type="radio" value="2" {if $cfg['yeepayType'] == '2'}checked{/if} class="radio"><label for="yeepayType_2">仅PC业务</label>
				<input name="cfg[yeepayType]" id="yeepayType_3" type="radio" value="3" {if $cfg['yeepayType'] == '3'}checked{/if} class="radio"><label for="yeepayType_3">两者都开通</label>
				<span style="margin-left:42px;">不清楚，请咨询易宝QQ：1060977716</span>
			</td>
        </tr>
		<tr>
            <td width="23%" class="td_title">交易类别码：</td>
            <td width="77%">
				<select name="cfg[productcatalog]">
				<option value="1" {if $cfg['productcatalog'] == '1'}selected{/if}>1-虚拟产品</option>
				<option value="3" {if $cfg['productcatalog'] == '3'}selected{/if}>3-公共事业缴费</option>
				<option value="4" {if $cfg['productcatalog'] == '4'}selected{/if}>4-手机充值</option>
				<option value="6" {if $cfg['productcatalog'] == '6'}selected{/if}>6-公益事业</option>
				<option value="7" {if $cfg['productcatalog'] == '7'}selected{/if}>7-实物电商</option>
				<option value="8" {if $cfg['productcatalog'] == '8'}selected{/if}>8-彩票业务</option>
				<option value="10" {if $cfg['productcatalog'] == '10'}selected{/if}>10-行政教育</option>
				<option value="11" {if $cfg['productcatalog'] == '11'}selected{/if}>11-线下服务业</option>
				<option value="13" {if $cfg['productcatalog'] == '13'}selected{/if}>13-微信实物电商</option>
				<option value="14" {if $cfg['productcatalog'] == '14'}selected{/if}>14-微信虚拟电商</option>
				<option value="15" {if $cfg['productcatalog'] == '15'}selected{/if}>15-保险行业</option>
				<option value="16" {if $cfg['productcatalog'] == '16'}selected{/if}>16-基金行业</option>
				<option value="17" {if $cfg['productcatalog'] == '17'}selected{/if}>17-电子票务</option>
				<option value="18" {if $cfg['productcatalog'] == '18'}selected{/if}>18-金融投资</option>
				</select>
				<span style="margin-left:132px;">不清楚，请咨询易宝QQ：1060977716</span>
			</td>
        </tr>
        <tr>
            <td class="td_title">易宝公钥：</td>
            <td>
                <textarea name="cfg[yeepayPublicKey]" rows="5" cols="100">{$cfg['yeepayPublicKey']}</textarea>
				&nbsp;&nbsp;<a href="http://www.yeepay.com/" target="_blank">查看</a>
            </td>
        </tr>
		<tr>
            <td class="td_title">商户公钥：</td>
            <td>
                <textarea name="cfg[merchantPublicKey]" rows="5" cols="100">{$cfg['merchantPublicKey']}</textarea>
				&nbsp;&nbsp;<a href="http://mobiletest.yeepay.com/file/caculate/to_rsa" target="_blank">生成</a>
            </td>
        </tr>
		<tr>
            <td class="td_title">商户私钥：</td>
            <td>
                <textarea name="cfg[merchantPrivateKey]" rows="15" cols="100">{$cfg['merchantPrivateKey']}</textarea>
				&nbsp;&nbsp;<a href="http://mobiletest.yeepay.com/file/caculate/to_rsa" target="_blank">生成</a>
            </td>
        </tr>
    </table>
    <br/>
    <center>
        <input type="hidden" name="id" value="{$pay['id']}" />
        <input type="submit" name="submit" value="提 交" class="button" />
    </center>
</form>
<!--{template @admin/footer}-->