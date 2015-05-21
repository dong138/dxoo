<?php
class ModuleObject extends MasterObject {
	public $Username = '';
	public $Password = '';
	public $Secques = '';
	public $IsAdmin = false;
	function ModuleObject($config) {
		$this->MasterObject ( $config );
		$this->Username = post ( 'username', 'string' );
		$this->Password = post ( 'password', 'string' );
		$this->Secques = quescrypt ( $this->Post ['question'], $this->Post ['answer'] );
		if (MEMBER_ID > 0) {
			$this->IsAdmin = true;
		}
		if (strlen ( $_GET ['code'] ) == 32 && strlen ( $_GET ['state'] ) == 32) {
			$this->Code = 'qqlogin';
		}
		$runCode = Load::moduleCode ( $this );
		$this->$runCode ();
	}
	public function Main() {
		header ( 'Location: ' . rewrite ( '?mod=me' ) );
	}
	public function InfoJson($status,$msg) {
		return jsonEncode( array (
				'status' => $status,
				'msg' => $msg
		));
	}
	function Register() {
		$this->Title = __ ( '注册' );
		$city = logic ( 'misc' )->CityList ();
		$action1 = '?mod=myaccount&code=register&op=done';
		$action2 = '?mod=myaccount&code=smsregister&op=done';
		$home_uid = $_GET ['u'];
		include ($this->TemplateHandler->Template ( "my_account_register" ));
	}
	function Login() {
		if ((MEMBER_ID != 0 and false == $this->IsAdmin) || MEMBER_ID > 0) {
			$this->Messager ( "您已经使用用户名 " . MEMBER_NAME . " 登录系统，无需再次登录！", null );
		}
		$this->Title = __ ( '登录' );
		$city = logic ( 'misc' )->CityList ();
		$action1 = '?mod=myaccount&code=login&op=done';
		$action2 = '?mod=myaccount&code=smslogin&op=done';
		$un = handler ( 'cookie' )->GetVar('ysun');
		include ($this->TemplateHandler->Template ( "my_account_login" ));
	}
	
	//邮箱注册
	function register_done() {
		session_start();
		$email = post('a_email');
		if (logic ( 'myaccount' )->Exists ( 'mail', $email )) {
			$this->Messager ( "该邮箱已经被使用", - 1 );
		}
		$username = post('a_username');
		if (logic ( 'myaccount' )->Exists ( 'name', $username )) {
			$this->Messager ( "该用户名已经被使用", - 1 );
		}
		$code = post('a_code');
		$register_code = $_SESSION['register_code'];
		if ($register_code != $code) {
			$this->Messager ( "验证码错误！", - 1 );
		}
		$repassword = post('a_repassword');
		$password = post('a_password');
		if ($repassword != $password) {
			$this->Messager ( "两次密码输入不一致！", - 1 );
		}
		if($username){
			$rresult = myaccount ()->Register ( $username, $password, $email,'','',false,0,true);
			if ($rresult ['error']) {
				$this->Messager ( $rresult ['result'], - 1 );
			}
			$this->registmail($username, $email);
			//订阅
			if(post ( 'subscribe' )){
				logic ( 'subscribe' )->Validate ( logic ( 'subscribe' )->Add ( '0', 'mail', $email) );
			}
			$this->Messager ( $r ['result'] . "感谢您的注册，我们已经给您的邮箱发送了一封邮件请您登录邮箱激活账号！", 0 );
		}else{
			$this->Messager ( "注册失败", - 1 );
		}

	}
	
	/**
	 * 发送邮箱验证
	 * @param unknown $truename
	 * @param unknown $email
	 */
	function registmail($truename, $email) {
		$key = authcode ( $truename, 'ENCODE', ini ( 'settings.auth_key' ) );
		//处理url的+问题
		$key = str_replace('+','-',$key);
		$key = str_replace('/','_',$key);
		$mail ['title'] = $this->Config ['site_name'] . '欢迎您！';
		$mail ['content'] = $this->Config ['site_name'] . '欢迎您的注册 ，<a href="' . $this->Config ['site_url'] . '/?mod=myaccount&code=confirm&pwd=' . urlencode ( $key ) . '">请点击这里激活账号</a>，或者复制 <br/>' . $this->Config ['site_url'] . '/?mod=myaccount&code=confirm&pwd=' . urlencode ( $key ) . '到浏览器中';
		logic ( 'service' )->mail ()->Send ( $email, $mail ['title'], $mail ['content'] );
	}
	
	/**
	 ******************20141022新增CQX********************************************
	 * 绑定新邮箱，发送邮箱验证
	 * @param unknown $truename
	 * @param unknown $email
	 * @param unknown $captcha
	 */
	function bindMail() {
		session_start();
		$email = post('email');
		if (logic ( 'myaccount' )->Exists ( 'mail', $email )) {
			exit ( $this->InfoJson ( 1, '邮箱地址已存在！' ));
		}
		if (! check_email ( $email )){
			exit ( $this->InfoJson ( 1, '邮箱地址不正确！' ));
		}
		$username = post('username');
		if (!logic ( 'myaccount' )->Exists ( 'name', $username )) {
			exit ( $this->InfoJson ( 1, '用户名不存在！' ));
		}
		$code = post('a_code');
		$register_code = $_SESSION['register_code'];
		if ($register_code != $code) {
			exit ( $this->InfoJson ( 1, '验证码不正确！' ));
		}
		$this->registmail($username, $email);
		$uid = user ()->get ( 'id' );
		$ary = array ( 'email' => $email );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'uid = ' . $uid  );
		exit ( $this->InfoJson ( 2, '我们已经给您的邮箱发送了一封邮件请您登录邮箱绑定账号！' ));
	}
	//绑定手机
	function bindPhone()
	{
		session_start();
		$phone = post('phone');
		$code = post('a_code');
		if(!check_phone($phone)){
			exit('{"info":"手机格式不正确！","status":"n"}');
		}
		$register_code = $_SESSION['hb_phone_code'];
		if ($register_code != $code) {
			exit('{"info":"验证码不正确！","status":"n"}');
		}
		$uid = user ()->get ( 'id' );
		$ary = array ( 'phone' => $phone );
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'uid = ' . $uid  );
		exit('{"info":"绑定成功！","status":"y"}');
	}
	//多次发送验证
	function againMail()
	{
		$email = user ()->get('email');
		$username = user ()->get('name');
		$this->registmail($username, $email);
	}
	function send_mail()
	{
		$this->Title = __ ( '发送验证邮箱' );
		$way = get ( 'way', 'int' );
		$cs=get ( 'cs', 'int' );
		if(!$way||!$cs){$this->Messager ( '请求错误！');}
		$backurl='?mod=me&code=choice_verify&way='.$way;
		$sendmail='?mod=myaccount&code=send_mail&way='.$way.'&cs='.$cs;
		$title= $way==1?'手机号码':'邮箱地址';
		$model= $way==1?'me':'myaccount';
		$code= $way==1?'new_phone':'oldConfirm';
		$member=user ()->get ();
		$this->valEmail($member);
		$this->sendmail ( $member['name'], $member['email'],$model,$code,'【换绑'.$title.'】的验证邮件','您申请了【换绑'.$title.'】的邮箱验证' );
		include handler ( 'template' )->file ( 'send_mail' );
	}
	//公共邮件验证
	function valEmail($member)
	{
		$sql="
		SELECT
			M.uid,MF.authstr,M.email,M.phone,M.username
		FROM
			".TABLE_PREFIX. 'system_members'." M LEFT JOIN ".TABLE_PREFIX.'system_memberfields'." MF ON(M.uid=MF.uid)
					WHERE
					BINARY M.uid=".user ()->get ('id');
		$query = $this->DatabaseHandler->Query($sql);
		$member=$query->GetRow();
		if ($member==false)$this->Messager("用户已经不存在", 0);
		$timestamp=time();
		if ($member['authstr']!='')
		{
			list($dateline, $operation, $idstring) = explode("\t", $member['authstr']);
			$inteval=600;			
			if ($dateline+$inteval>$timestamp)
			{
				$this->Messager("服务器繁忙,你操作太快,请坐下来喝杯咖啡。您的请求已经发送到您的信箱中，如有问题，请与管理员联系。", 0);
			}
		}
		
		$idstring = random(32);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX.'system_memberfields');
		$member['authstr'] = "$timestamp\t1\t$idstring";
		$member['auth_try_times'] = 0;
		$result=$this->DatabaseHandler->Update($member,"uid={$member['uid']}");
		if ($result==false)
		{
			$this->DatabaseHandler->Insert($member);
		}
	}
	
	function _resetCheck()
	{
		$uid=user ()->get ('id');
		if ($uid<1) $this->Messager("请求错误",0);
	
		$sql="
		SELECT
			M.uid,M.username,MF.authstr,M.email,M.email2,M.secques,MF.auth_try_times
		FROM
			".TABLE_PREFIX. 'system_members'." M LEFT JOIN ".TABLE_PREFIX.'system_memberfields'." MF ON(M.uid=MF.uid)
				WHERE
				BINARY M.uid=$uid";
		$query = $this->DatabaseHandler->Query($sql);
		$member=$query->GetRow();
		if ($member==false)$this->Messager("用户已经不存在",0);
	
		$member['auth_try_times'] = (max(0, (int) $member['auth_try_times']) + 1);
		dbc(DBCMax)->Update('memberfields')->data(array('auth_try_times'=>$member['auth_try_times']))->where(array('uid'=>$uid))->done();
		if($member['auth_try_times']>=10) {$this->Messager('您尝试的错误次数太多了，请重新发起请求!', null);}
	
		$timestamp=time();
		list($dateline, $operation) = explode("\t", $member['authstr']);
		if($dateline < $timestamp - 86400 || $operation != 1)
		{
			$message=array("请求不存在或已经过期，无法取绑定操作。");
			$this->Messager($message,0);
		}
	}
	//公共邮件发送
	function sendmail($truename, $email,$model,$code,$title,$content) {
		$key = authcode ( $truename, 'ENCODE', ini ( 'settings.auth_key' ) );
		//处理url的+问题
		$key = str_replace('+','-',$key);
		$key = str_replace('/','_',$key);
		$mail ['title'] = $this->Config ['site_name'] . $title;
		$mail ['content'] = $this->Config ['site_name'] . $content.'<a href="' . $this->Config ['site_url'] . '/?mod='.$model.'&code='.$code.'&pwd=' . urlencode ( $key ) . '">请点击这里！</a>，或者复制 <br/>' . $this->Config ['site_url'] . '/?mod='.$model.'&code='.$code.'&pwd=' . urlencode ( $key ) . '到浏览器中';
		logic ( 'service' )->mail ()->Send ( $email, $mail ['title'], $mail ['content'] );
	}
	//更换绑定新邮件，旧邮件验证确定
	function oldConfirm() {
		$this->_resetCheck();
		$pwd = get ( 'pwd', 'txt' );
		$msg='';
		if ($pwd == '')
			$msg='验证错误！';
		$kk = urldecode ( $pwd );
		//处理url的+问题
		$kk = str_replace('-','+',$kk);
		$kk = str_replace('_','/',$kk);
		$pwdT = authcode ( $kk, 'DECODE', ini ( 'settings.auth_key' ) );
		if ($pwdT == '') {
			$pwd = authcode ( $pwd, 'DECODE', ini ( 'settings.auth_key' ) );
		} else {
			$pwd = $pwdT;
		}
		if ($pwd == '')
			$msg='邮箱认证失败，请重发认证邮件或联系网站管理员进行人工审核！';
		$sql = 'select * from ' . TABLE_PREFIX . 'system_members where truename = \'' . $pwd . '\'';
		$query = $this->DatabaseHandler->Query ( $sql );
		$user = $query->GetRow ();
		if ($user == '' || $user ['checked'] == 1)
			$msg='用户不存在或已经通过验证！';
		$msg='您的验证已经成功通过，请立即修改您的绑定邮箱';
		include handler ( 'template' )->file ( 'new_mail' );
	}
	//保存新邮箱
	function newConfirm()
	{
		$email = post('email');
		if (! check_email ( $email )){
			$this->Messager ( "该邮箱错误请重新更换！", - 1 );
		}
		$uid = user ()->get ( 'id' );
		$username=user ()->get ('name');
		$username.='|'.$email;
		$this->sendmail ( $username, $email,'myaccount','finish','【邮箱新绑定】的验证邮件','您申请了【邮箱新绑定】的邮箱验证' );
		$msg='我们发送了一封验证邮件到<label>'.$email.'</label>，请到您的邮箱收信，并点击其中的链接验证您的邮箱';
		include handler ( 'template' )->file ( 'msg_email' );
	}
	//邮件绑定完成
	function finish()
	{
		$this->_resetCheck();
		$pwd = get ( 'pwd', 'txt' ); $msg='';
		if ($pwd == '')
			$msg='验证错误！';
		$kk = urldecode ( $pwd );
		//处理url的+问题
		$kk = str_replace('-','+',$kk);
		$kk = str_replace('_','/',$kk);
		$pwdT = authcode ( $kk, 'DECODE', ini ( 'settings.auth_key' ) );
		if ($pwdT == '') {
			$pwd = authcode ( $pwd, 'DECODE', ini ( 'settings.auth_key' ) );
		} else {
			$pwd = $pwdT;
		}
		$arr= explode("|",$pwd);
		var_dump($arr);
		if ($arr==null){
			$this->Messager ( "邮箱验证错误请重新更换！", - 1 );
		}
		$pwd=$arr[0]; $email=$arr[1];
		if ($pwd == '' && $email=='')
			$msg='邮箱认证失败，请重发认证邮件或联系网站管理员进行人工审核！';
		$sql = 'select * from ' . TABLE_PREFIX . 'system_members where truename = \'' . $pwd . '\'';
		$query = $this->DatabaseHandler->Query ( $sql );
		$user = $query->GetRow ();
		if ($user == '' || $user ['checked'] == 1)
			$msg='用户不存在或已经通过验证！';
		
		$uid = user ()->get ( 'id' );
		$ary = array (
		 		'email'=>$email,
				'checked' => 1
		);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'uid = ' . $uid );
		$fieldssql="UPDATE ".TABLE_PREFIX.'system_memberfields'." SET `authstr`='',`auth_try_times`='0' WHERE uid='$uid'";
		$this->DatabaseHandler->Query($fieldssql);
		$msg='您的绑定邮箱已修改成功';$title='手机';
		include handler ( 'template' )->file ( 'bind_finish' );
	}
	
	function UpdatePhone()
	{
		$phone = post('newPhone');
		if (! check_phone ( $phone )){
			$this->Messager ( "该手机号错误请重新更换！", - 1 );
		}
		$uid = user ()->get ( 'id' );
		$ary = array (
				'phone'=>$phone
				//'checked' => 1
		);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'uid = ' . $uid );
		$msg='您的绑定的手机已修改成功';$title='手机';
		include handler ( 'template' )->file ( 'bind_finish' );
	}
	
	function confirmAccount()
	{
		$email = post('account');
		if (! $email){
			$this->Messager ( "请填写找回密码的凭据！", - 1 );
		}
		$uid = user ()->get ( 'id' );
		$phoneurl='?mod=me&code=phone_verify&way=1&cs=1';
		$emailurl='?mod=myaccount&code=send_mail&way=1&cs=2';
		include handler ( 'template' )->file ( 'choice_password' );
	}
	
	function editUsername()
	{
		$username = post('newUserName');
		if(!$username){
			exit ( $this->InfoJson ( 1, '账号不能为空！' ));
		}
		$uid = user ()->get ( 'id' );
		$ary = array ( 
				'username' => $username ,
				'truename' =>$username
		);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'uid = ' . $uid  );
		exit ( $this->InfoJson ( 2, '修改成功！' ));
	}
	
	function editPassword()
	{
		$password = post('txtPassword');
		$password2 = post('txtPasswor2');
		if(!$password || ($password!=$password2)){
			exit ( $this->InfoJson ( 1, '新密码错误！' ));
		}
		$uid = user ()->get ( 'id' );
		$ary = array (
				'password' => md5($password)
				);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'uid = ' . $uid  );
		exit ( $this->InfoJson ( 2, '修改成功！' ));
	}
	
	function isEmail()
	{
		$email = post('param');
		if (logic ( 'myaccount' )->Exists ( 'mail', $email )) {
			exit('{"info":"邮箱已被使用！","status":"n"}');
		}
		exit('{"info":"邮箱可用！","status":"y"}');
	}
	function isCode()
	{
		session_start();
		$code = post('param');
		$register_code = $_SESSION['register_code'];
		if ($register_code != $code) {
			exit('{"info":"验证码错误！","status":"n"}');
		}
		exit('{"info":"验证码正确！","status":"y"}');
	}
	function isValCode()
	{
		session_start();
		$code = post('param');
		//短信验证码的判断
		$register_code = $_SESSION['hb_phone_code'];
		if($code != $register_code){
			exit('{"info":"验证码错误！","status":"n"}');
		}
		exit('{"info":"验证码正确！","status":"y"}');
	}
	function isSamePhone() {
		$phone = post('param');
		if (logic ( 'myaccount' )->Exists ( 'phone', $phone )) {
			exit('{"info":"手机号已被使用！","status":"n"}');
		}
		exit('{"info":"手机号可用！","status":"y"}');
	}
	function isUserName() {
		$username = post('param');
		if (logic ( 'myaccount' )->Exists ( 'name', $username )) {
			exit('{"info":"账号已存在！","status":"n"}');
		}
		exit('{"info":"账号可用！","status":"y"}');
	}
	function isPassword()
	{
		$password = post('param');
		$result = dbc ( DBCMax )->select ( 'members' )->where (
				array('uid'=>user ()->get ( 'id' ), 'password'=>md5($password))
				)->done ();
		if ($result) {
			exit('{"info":"密码正确！","status":"y"}');
		}
		exit('{"info":"密码错误！","status":"n"}');
	}
	/****************************************************************************/
	
	//手机验证码注册
	function smsregister_done() {
		session_start();
		$phone = post('b_mobile');
		if(!check_phone($phone)){
			$this->Messager ( "手机号格式不正确", - 1 );
		}
		if (logic ( 'myaccount' )->Exists ( 'phone', $phone )) {
			$this->Messager ( "该手机号已被使用", - 1 );
		}
		$repassword = post('b_repassword');
		$password = post('b_password');
		if($repassword != $password){
			$this->Messager ( "两次输入密码不一致", - 1 );
		}
 		$code = post('b_code');
		//手机号的判断
 		$register_code = $_SESSION['register_phone_code'];
 		if($code != $register_code){
 			$this->Messager ( "你输入的手机动态码错误", - 1 );
 		}
		$username = logic('code')->__createUsername();
		if($username){
			$rresult = myaccount ()->Register ( $username, $password, "", $phone,'',false,0,false,1);
			if ($rresult ['error']) {
				$this->Messager ( $rresult ['result'], - 1 );
			}
			$loginR = myaccount ()->Login ( $username, $password);
			if ($loginR ['error']) {
				$this->_loginfailed ( $loginperm );
				$this->Messager ( $loginR ['result'], - 1 );
			}
			$ref = myaccount ()->loginReferer ();
			$ref || $ref = '?mod=me';
			$this->Messager ( __ ( '注册并登录成功！' ) . $loginR ['result'], $ref );
		}else{
			$this->Messager ( "注册失败", - 1 );
		}
	}
	
	public function qqgetuserinfo() {
		myaccount ( 'ulogin' )->reg_and_login ( 'qq' );
		$ref = myaccount ()->loginReferer ();
		$ref || $ref = '?mod=me';
		$this->Messager ( __ ( '登录成功！' ) . $loginR ['result'], $ref );
	}
	
	//用户名/手机号/账号登陆
	//密码登录
	//已经成功
	function Login_done(){
		$loginperm = $this->_logincheck ();
		if (! $loginperm) {
			$this->Messager ( "累计 5 次错误尝试，15 分钟内您将不能登录。", null );
		}
		if( $this->Username == null || strlen($this->Username) <= 0){
			$this->Messager ( "用户名不允许为空", - 1 );
		}
		if( $this->Password == null || strlen($this->Password) <= 0){
			$this->Messager ( "密码不允许为空", - 1 );
		}
		if(check_email($this->Username)){
			//是邮箱，则检测是否有激活
			$user = myaccount ()->Search ( 'email', $this->Username, 1 );
			if ($user && $user ['role_type'] != 'admin' && $user ['checked'] == 0) {
				header ( 'Location: ' . rewrite ( '?mod=myaccount&code=activate&email='.$this->Username ) );
				exit ();
			}
		}
		$loginR = myaccount ()->Login ( $this->Username, $this->Password, ($_POST ['keeplogin'] == 'on') );
		if ($loginR ['error']) {
			$this->_loginfailed ( $loginperm );
			$this->Messager ( $loginR ['result'], - 1 );
		}
		//记住账号
		if ($_POST ['remember_username'] == 'on') {
			$expires = ( int ) ini ( 'settings.cookie_expire' ) * 86400;
		} else {
			$expires = false;
		}
		handler ( 'cookie' )->SetVar ( 'ysun', $this->Username, $expires );
		$ref = myaccount ()->loginReferer ();
		$ref || $ref = '?mod=me';
		$this->Messager ( __ ( '登录成功！' ) . $loginR ['result'], $ref );
	}
	
	//手机验证码登录
	function smslogin_done(){
		session_start();
		$loginperm = $this->_logincheck ();
		if (! $loginperm) {
			$this->Messager ( "累计 5 次错误尝试，15 分钟内您将不能登录。", null );
		}
		if( $this->Username == null || strlen($this->Username) <= 0 || !check_phone($this->Username)){
			$this->Messager ( "手机号为空或格式不正确", - 1 );
		}
		if( $this->Password == null || strlen($this->Password) <= 0){
			$this->Messager ( "动态码不允许为空", - 1 );
		}
		if( $_SESSION['login_phone_code'] != $this->Password){
			$this->Messager ( "动态码不正确", - 1 );
		}
		//将验证码验证//快速登录标志
		$loginR = myaccount ()->Login ( $this->Username, null, ($_POST ['keeplogin'] == 'on'),true);//快速登录标志
		if ($loginR ['error']) {
			//用户不存在，则需要先注册再登录
			$username = logic('code')->__createUsername();
			if($username){
				$rresult = myaccount ()->Register ( $username, "", "", $this->Username,'',false,0,false,2);//$this->Username是电话
				if ($rresult ['error']) {
					$this->Messager ( "登录失败", - 1 );
				}
				$loginR = myaccount ()->Login ( $this->Username, null, ($_POST ['keeplogin'] == 'on'),true);
				if ($loginR ['error']) {
					$this->_loginfailed ( $loginperm );
					$this->Messager ( "登录失败", - 1 );
				}
				$ref = myaccount ()->loginReferer ();
				$ref || $ref = '?mod=me';
				$this->Messager ( __ ( '登录成功！' ) . $loginR ['result'], $ref );
			}else{
				$this->Messager ( "登录失败", - 1 );
			}
		}
		$ref = myaccount ()->loginReferer ();
		$ref || $ref = '?mod=me';
		$this->Messager ( __ ( '登录成功！' ) . $loginR ['result'], $ref );
	}
	
	//发送动态码
	//1：登陆2：注册
	function account_sms(){
		session_start();
		$type = get ( 'type', 'int' );
		$phone = get ( 'phone', 'txt' );
		//生成短信码
//  		$code = "123456";
  		$code = logic('code')->__random_num(6);

		//登陆
		if($type == 1){
			$_SESSION['login_phone_code'] = $code;
		}
		//注册
		if($type == 2){
			$_SESSION['register_phone_code'] = $code;
		}
		//换绑手机
		if($type == 4){
			$_SESSION['hb_phone_code'] = $code;
		}
		//换绑邮箱
		if($type == 5){
			$_SESSION['hb_email_code'] = $code;
		}
		//发送
		
 		logic('ci')->sms_code($phone,$code,$type);
	}
	/**
	 * 验证码检查
	 */
	function smsRegisterCk(){
		session_start();
		$code = get('cp','txt');
		if($code == null || $code ==''){
			exit('no');
		}
		if($code == $_SESSION['register_code']){
			exit('ok');
		}else{
			exit('no');
		}
	}
	
	/**
	 * 手机，邮箱，用户名唯一性检查
	 */
	function registerCkBy(){
		$tp = get('tp','int');
		$v = get('v','txt');
		if($tp == 1){
			if (logic ( 'myaccount' )->Exists ( 'name', $v )) {
				exit("no");
			}
		}
		if($tp == 2){
			if (logic ( 'myaccount' )->Exists ( 'mail', $v )) {
				exit("no");
			}	
		}
		if($tp == 3){
			if (logic ( 'myaccount' )->Exists ( 'phone', $v )) {
				exit("no");
			}	
		}
		exit("ok");
	}
	
	function Logout() {
		$this->_fix_failedlogins ();
		$logoutR = myaccount ()->Logout ( MEMBER_NAME );
		$this->Messager ( $logoutR ['result'] . '退出成功', $this->Config ['site_url'] );
	}
	
	function _fix_failedlogins($uid = MEMBER_ID) {
		$onlineip = $_SERVER ['REMOTE_ADDR'];
		$timestamp = time ();
		$this->DatabaseHandler->Query ( "DELETE FROM " . TABLE_PREFIX . 'system_failedlogins' . " WHERE lastupdate<$timestamp-901", 'UNBUFFERED' );
		if ($uid > 0) {
			$this->DatabaseHandler->Query ( "DELETE FROM " . TABLE_PREFIX . 'system_failedlogins' . " WHERE `ip`='{$onlineip}'", 'UNBUFFERED' );
		}
	}
	
	function _loginfailed($permission) {
		$onlineip = $_SERVER ['REMOTE_ADDR'];
		$timestamp = time ();
		switch ($permission) {
			case 1 :
				$this->DatabaseHandler->Query ( "REPLACE INTO " . TABLE_PREFIX . 'system_failedlogins' . " (ip, count, lastupdate) VALUES ('$onlineip', '1', '$timestamp')" );
				break;
			case 2 :
				$this->DatabaseHandler->Query ( "UPDATE " . TABLE_PREFIX . 'system_failedlogins' . " SET count=count+1, lastupdate='$timestamp' WHERE ip='$onlineip'" );
				break;
			case 3 :
				$this->DatabaseHandler->Query ( "UPDATE " . TABLE_PREFIX . 'system_failedlogins' . " SET count='1', lastupdate='$timestamp' WHERE ip='$onlineip'" );
				$this->DatabaseHandler->Query ( "DELETE FROM " . TABLE_PREFIX . 'system_failedlogins' . " WHERE lastupdate<$timestamp-901", 'UNBUFFERED' );
				break;
		}
	}

	function _logincheck() {
		$onlineip = $_SERVER ['REMOTE_ADDR'];
		$timestamp = time ();
		$query = $this->DatabaseHandler->Query ( "SELECT count, lastupdate FROM " . TABLE_PREFIX . 'system_failedlogins' . " WHERE ip='$onlineip'" );
		if ($login = $query->GetRow ()) {
			if ($timestamp - $login ['lastupdate'] > 900) {
				return 3;
			} elseif ($login ['count'] < 5) {
				return 2;
			} else {
				return 0;
			}
		} else {
			return 1;
		}
	}
	
	//新增验证码
	function create_code(){
		session_start();
		//生成验证码图片
		header("Content-type: image/png");
		// 全数字
		$str = "1,2,3,4,5,6,7,8,9,a,b,c,d,f,g,h,j,k,m,n,r,s,t,w,x,y,z";      //要显示的字符，可自己进行增删
		$list = explode(",", $str);
		$cmax = count($list) - 1;
		$verifyCode = '';
		for ( $i=0; $i < 5; $i++ ){
			$randnum = mt_rand(0, $cmax);
			$verifyCode .= $list[$randnum];           //取出字符，组合成为我们要的验证码字符
		}
		$_SESSION['register_code'] = $verifyCode;        //将字符放入SESSION中
// 		handler ( 'member' )->session ['register_code'] = $verifyCode;
		$im = imagecreate(58,28);    //生成图片
		$black = imagecolorallocate($im, 0,0,0);     //此条及以下三条为设置的颜色
		$white = imagecolorallocate($im, 255,255,255);
		$gray = imagecolorallocate($im, 200,200,200);
		$red = imagecolorallocate($im, 255, 0, 0);
		imagefill($im,0,0,$white);     //给图片填充颜色
	
		//将验证码绘入图片
		imagestring($im, 5, 10, 8, $verifyCode, $black);    //将验证码写入到图片中
	
		for($i=0;$i<50;$i++)  //加入干扰象素
		{
			imagesetpixel($im, rand() , rand() , $black);    //加入点状干扰素
			imagesetpixel($im, rand() , rand() , $red);
			imagesetpixel($im, rand() , rand() , $gray);
			//imagearc($im, rand()p, rand()p, 20, 20, 75, 170, $black);    //加入弧线状干扰素
			//imageline($im, rand()p, rand()p, rand()p, rand()p, $red);    //加入线条状干扰素
		}
		imagepng($im);
		imagedestroy($im);
	}
	
	function Activate() {
		$this->Messager ( "您还没有通过邮箱验证呢！<a href='?mod=myaccount&code=sendcheckmail&uname=" . get('email') . "'>点这里重新发送认证邮件  </a>", 0 );
	}
		
	function Confirm() {
		$pwd = get ( 'pwd', 'txt' );
		if ($pwd == '')
			$this->Messager ( __ ( "错误！" ) );
		$kk = urldecode ( $pwd );
		//处理url的+问题
		$kk = str_replace('-','+',$kk);
		$kk = str_replace('_','/',$kk);
		$pwdT = authcode ( $kk, 'DECODE', ini ( 'settings.auth_key' ) );
		if ($pwdT == '') {
			$pwd = authcode ( $pwd, 'DECODE', ini ( 'settings.auth_key' ) );
		} else {
			$pwd = $pwdT;
		}
		if ($pwd == '')
			$this->Messager ( '邮箱认证失败，请重发认证邮件或联系网站管理员进行人工审核！', 0 );
		$sql = 'select * from ' . TABLE_PREFIX . 'system_members where truename = \'' . $pwd . '\'';
		$query = $this->DatabaseHandler->Query ( $sql );
		$user = $query->GetRow ();
		if ($user == '' || $user ['checked'] == 1)
			$this->Messager ( __ ( "用户不存在或已经通过验证！" ) );
		$ary = array (
				'checked' => 1
		);
		$this->DatabaseHandler->SetTable ( TABLE_PREFIX . 'system_members' );
		$result = $this->DatabaseHandler->Update ( $ary, 'truename = \'' . $pwd . '\'' );
		$this->Messager ( __ ( "邮箱认证成功！请重新登录" ), rewrite ( '?mod=myaccount&code=login' ) );
	}
	
	function Sendcheckmail() {
		$uname = get ( 'uname', 'string' );
		$uname = preg_replace ( '/[\s\(\)\=]/', '', $uname );
		if (strlen ( $uname ) > 100)
			die ( '非法调用' );
		$user = dbc ( DBCMax )->select ( 'members' )->where ( array (
				'email' => $uname,
				'checked' => 0
		) )->limit ( 1 )->done ();
		if ($user != '') {
			$this->registmail ( $user['truename'], $user ['email'] );
			$this->Messager ( "已经发送一封确认信件到您的邮箱去了，请注意查收！", 0 );
		}
		$this->Messager ( "错误，该用户已确认信箱或不存在！", 0 );
	}

}
?>