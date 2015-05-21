/** * @package js * @name cash.order.ops.js * @date 2014-05-08 15:05:45 */ function cashOrderConfirm(orderid)
{
	$.notify.loading('正在受理中...');
	$.get('?mod=cash&code=order&op=confirm&orderid='+orderid, function(data){
		$.notify.loading();
		if (data.replace(/^\s+|\s+$/g, "") == 'ok')
		{
			$.notify.success('谢谢您的操作！');location.reload();
		}
		else
		{
			$.notify.failed(data);
		}
	});
}