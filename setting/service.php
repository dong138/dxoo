<?php
/**
 * @package php
 * @name service.php
 * @date 2014-09-01 17:24:23
 */
 


$config["service"] =  array (
  'mail' =>
  array (
    'balance' => true,
  ),
  'sms' =>
  array (
    'driver' =>
    array (
      'ls' =>
      array (
        'name' => '电信通道',
        'intro' => '075开头，网关短信直发（自动重发功能暂时只支持此通道）<br/><a href="'.ihelper('tg.shop.sms.qxt').'" target="_blank"><font color="red">点此在线购买</font></a>',
      ),
      'qxt' =>
      array (
        'name' => 'GD106通道',
        'intro' => '通知通道，禁发营销信息，67字/条，免签名<br/><a href="'.ihelper('tg.shop.sms.qxt').'" target="_blank"><font color="red">点此在线购买</font></a>',
      ),
      'wnd' =>
      array (
        'name' => 'WN106通道',
        'intro' => '订单通知通道，禁发营销信息，70字单条，长短信67字条，须签名（签名联系客服QQ862530928设置）<br/><a href="'.ihelper('tg.shop.sms.qxt').'" target="_blank"><font color="red">点此在线购买</font></a>',
      ),
      'zt' =>
      array (
        'name' => 'ZT106通道',
        'intro' => '<font color="red">【推荐】</font>团购订单直连通知通道，专用订单通知通道，禁发营销信息，64字/条，须签名（签名接口设置页自行设置，推荐用站点名称）<br/><a href="'.ihelper('tg.shop.sms.qxt').'" target="_blank"><font color="red">点此在线购买</font></a>',
      ),
	  'ums' =>
      array (
        'name' => 'ZJ165通道',
        'intro' => '<font color="red">【备用通道】</font>目前暂不支持开通',
      ),
	  'tyx' =>
      array (
        'name' => 'TP106通道',
        'intro' => '<font color="red">【推荐】</font>团购触发式优质短信通道，禁止营销，抽奖类短信禁止下发，下发速度快，专用订单，验证码通知通道，必须签名，67字/条。（签名接口设置页自行设置，推荐用站点名称）<br/><a href="'.ihelper('tg.shop.sms.qxt').'" target="_blank"><font color="red">点此在线购买</font></a>',
      ),
    ),
    'autoERSend' => true,
  ),
  'push' =>
  array (
    'mthread' => false,
  ),
);
?>