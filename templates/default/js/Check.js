var issend=false;

//验证邮箱
function CheckEmail() {
	var email = $('#email').val();
	var emailRegExp = new RegExp(
			"^\w+([-+.]\w+)*@(\w+([-.]\w+)*\.)+([a-zA-Z]+)+$");
	if (!emailRegExp.test(email) && email.indexOf('.') == -1)
		return "请输入正确的Email地址！";
	if (email == '')
		return '请输入Email地址！';
	else
		return '可以使用！';
}

// 验证手机号码长度
function CheckPhone() {
	var phone = $('#b_mobile').val();
	if (phone.length != 11)
		return false;
	return true;
}

// 验证密码强度

//测试某个字符是属于哪一类.  
function CharMode(iN){  
if (iN>=48 && iN <=57) //数字  
return 1;  
if (iN>=65 && iN <=90) //大写字母  
return 2;  
if (iN>=97 && iN <=122) //小写  
return 4;  
else  
return 8; //特殊字符  
}  

//bitTotal函数  
//计算出当前密码当中一共有多少种模式  
function bitTotal(num){  
modes=0;  
for (i=0;i<4;i++){  
if (num & 1) modes++;  
num>>>=1;  
}  
return modes;  
}  

//checkStrong函数  
//返回密码的强度级别  

function checkStrong(sPW){  
if (sPW.length<=4)  
return 0; //密码太短  
Modes=0;  
for (i=0;i<sPW.length;i++){  
//测试每一个字符的类别并统计一共有多少种模式.  
Modes|=CharMode(sPW.charCodeAt(i));  
}  

return bitTotal(Modes);  

}  

//邮箱
//pwStrength函数  
//当用户放开键盘或密码输入框失去焦点时,根据不同的级别显示不同的颜色  

function pwStrengthForEmail(pwd){  
O_color="#eeeeee";  
L_color="#FF0000";  
M_color="#FF9900";  
H_color="#33CC00";  
if (pwd==null||pwd==''){  
Lcolor=Mcolor=Hcolor=O_color;  
}  
else{  
S_level=checkStrong(pwd);  
switch(S_level) {  
case 0:  
Lcolor=Mcolor=Hcolor=O_color;  
case 1:  
Lcolor=L_color;  
Mcolor=Hcolor=O_color;  
break;  
case 2:  
Lcolor=Mcolor=M_color;  
Hcolor=O_color;  
break;  
default:  
Lcolor=Mcolor=Hcolor=H_color;  
}  
}  

document.getElementById("strength_L").style.background=Lcolor;  
document.getElementById("strength_M").style.background=Mcolor;  
document.getElementById("strength_H").style.background=Hcolor;  
return;  
}

//手机
//pwStrength函数  
//当用户放开键盘或密码输入框失去焦点时,根据不同的级别显示不同的颜色  

function pwStrengthForPhone(pwd){  
O_color="#eeeeee";  
L_color="#FF0000";  
M_color="#FF9900";  
H_color="#33CC00";  
if (pwd==null||pwd==''){  
Lcolor=Mcolor=Hcolor=O_color;  
}  
else{  
S_level=checkStrong(pwd);  
switch(S_level) {  
case 0:  
Lcolor=Mcolor=Hcolor=O_color;  
case 1:  
Lcolor=L_color;  
Mcolor=Hcolor=O_color;  
break;  
case 2:  
Lcolor=Mcolor=M_color;  
Hcolor=O_color;  
break;  
default:  
Lcolor=Mcolor=Hcolor=H_color;  
}  
}  

document.getElementById("strength_PL").style.background=Lcolor;  
document.getElementById("strength_PM").style.background=Mcolor;  
document.getElementById("strength_PH").style.background=Hcolor;  
return;  
}

// 验证值是否为空
function CheckIsNull(element){
	var target=document.getElementById(element);
	var targetvalue=target.value;
	if(targetvalue==""||targetvalue==null)
		return false;
	return true;
}

function CheckCode(element){
	if(!CheckIsNull(element))
		alert("短信验证码未填");
	else if(!issend)
		alert("请选先获取短信验证码");
}

function SendCode(element,second){
	var phone = $("#login-mobile").val();
	if(phone == '' || phone.length <= 0){
		$("#J-mobile-login-tip").html("请先输入手机号");
		$("#J-mobile-login-tip").show();
	}else{
		$("#J-mobile-login-tip").hide();
		issend=true;
		countDown(element,second);
		$.ajax({
			type : "GET",
			data : "type=1&phone="+phone,
			url : "?mod=myaccount&code=account_sms"
		});
	}
}

function countDown(obj,second){
    // 如果秒数还是大于0，则表示倒计时还没结束
    if (second >= 0) {
        // 获取默认按钮上的文字
        if (typeof buttonDefaultValue === 'undefined') {
            buttonDefaultValue = obj.defaultValue;
        }
        // 按钮置为不可点击状态
        obj.disabled = true;
        // 按钮里的内容呈现倒计时状态
        obj.value = buttonDefaultValue + '(' + second + ')';
        // 时间减一
        second--;
        // 一秒后重复执行
        setTimeout(function () { countDown(obj, second); }, 1000);
        // 否则，按钮重置为初始状态
    } else {
        // 按钮置未可点击状态
        obj.disabled = false;
        // 按钮里的内容恢复初始状态
        obj.value = buttonDefaultValue;
        issend=false;
    }
}

/***********************************************************************************/
//正常登陆检查
function normalLoginCK(){
	var u = $("#login-email").val();
	var p = $("#login-password").val();
	if(u == ''|| u.length <= 0){
		$("#J-normal-login-tip").html("用户名不允许为空");
		$("#J-normal-login-tip").show();
		return false;
	}
	if(p == ''|| p.length <= 0){
		$("#J-normal-login-tip").html("密码不允许为空");
		$("#J-normal-login-tip").show();
		return false;
	}
	$("#J-normal-login-tip").hide();
	return true;
}
//手机号登陆检查
function smsLoginCK(){
	if(issend){
		var u = $("#login-mobile").val();
		var p = $("#login-verify-code").val();
		if(u == ''|| u.length <= 0){
			$("#J-mobile-login-tip").html("请先输入手机号");
			$("#J-mobile-login-tip").show();
			return false;
		}
		if(p == ''|| p.length <= 0){
			$("#J-mobile-login-tip").html("手机动态码不允许为空");
			$("#J-mobile-login-tip").show();
			return false;
		}
		$("#J-mobile-login-tip").hide();
		return true;
	}else{
		$("#J-mobile-login-tip").html("请先获取验证码");
		$("#J-mobile-login-tip").show();
		return false;	
	}
}
