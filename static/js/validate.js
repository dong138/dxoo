function CheckEmpty(textbox,MsgSpanID) {
	if(textbox.value=="")
		{
		$("#" + MsgSpanID).html("不能为空！");
		return false;
		}
	else
		{
		$("#" + MsgSpanID).html("");
		return true;
		}
		
	
}

//验证数字
function CheckNumber(textbox,MsgSpanID) {
    var patten = /^\+?[1-9][0-9]*$/;
    if (!patten.test(textbox.value)) {
        $("#" + MsgSpanID).html("请输入数字！");
        return false;
    }
    else {
    	 $("#" + MsgSpanID).html("");
        return true;
    }
}



//验证手机
function CheckPhone(textbox,MsgSpanID) {
    var patten = /1[3-8]+\d{9}/;
    if (!patten.test(textbox.value)) {
        $("#" + MsgSpanID).html("请输入正确的手机号码！");
        return false;
    }
    else {
    	 $("#" + MsgSpanID).html("");
        return true;
    }
}

//验证邮箱
function checkEmail(textbox, MsgSpanID) {
	var patten = /^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/;
	if (textbox.value == "") {
		$("#" + MsgSpanID).html("");
		return true;
	}
	if (!patten.test(textbox.value)) {
		// alert("格式不正确！请重新输入");
		$("#" + MsgSpanID).html("邮箱格式不正确！");
		return false;
	}
	$("#" + MsgSpanID).html("");
	return true;
}