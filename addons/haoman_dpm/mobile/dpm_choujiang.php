<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['rid']);
$themes = $_GPC['themes'];
$reply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_reply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$yyyreply = pdo_fetch("SELECT isyyy FROM " . tablename('haoman_dpm_yyyreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$bpreply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_bpreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$xysreply = pdo_fetch("SELECT isxys,is_pair,is_turntable,xys_backcolor FROM " . tablename('haoman_dpm_xysreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$xyhreply = pdo_fetch("SELECT is_xysjh,is_xyh FROM " . tablename('haoman_dpm_xyhreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$shouqian = pdo_fetch("SELECT status,pm_status FROM " . tablename('haoman_dpm_shouqianBase') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$video = pdo_fetch("SELECT vodio_bg5 FROM " . tablename('haoman_dpm_mp4') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));

//检查登陆状态
if(!empty($reply['loginpassword'])){
    $cookieid = '__cookie_haoman_dpmweb_201606186_' . $rid;
    $cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
    if($cookie['loginpassword'] != $reply['loginpassword']){
        message('登陆密码错误或已超时，请重新输入',$this->createMobileUrl("login",array('id'=>$rid)),'error');
    }
}

//检查登陆状态

if($reply['ischoujiang']==1){
	message('未开启抽奖功能，请先后台开启！', '', 'error');
}

if($reply['ischoujiang']==2){
    $themes = 'normal';
}

load()->model('reply');
$keywords = reply_single($rid);

$totaldata = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('haoman_dpm_fans') . " WHERE uniacid = :uniacid AND rid = :rid and isbaoming=0 and is_back !=1", array(':uniacid' => $uniacid,':rid'=>$rid));

$fanslist = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_fans') . " WHERE uniacid = :uniacid AND rid = :rid and isbaoming=0 and is_back !=1 ORDER BY id DESC limit 100 ", array(':uniacid' => $uniacid,':rid'=>$rid));
foreach ($fanslist as $k => $v) {
    $fanslist[$k]['nickname'] = $this->strFilter($v['nickname']);
}

$list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_prize') . " WHERE rid = :rid and uniacid = :uniacid and turntable = :turntable ORDER BY sort DESC",array(':rid'=>$rid,':uniacid'=>$uniacid,':turntable'=>1));

if($reply['iscjnum']==0){
    $awardslists = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_award') . " WHERE rid = :rid and uniacid = :uniacid and turntable = :turntable  ORDER BY id DESC",array(':rid'=>$rid,':uniacid'=>$uniacid,':turntable'=>1));

}

$awardslist = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_award') . " WHERE rid = :rid and uniacid = :uniacid and turntable = :turntable and titleid = :titleid ORDER BY id DESC",array(':rid'=>$rid,':uniacid'=>$uniacid,':turntable'=>1,':titleid'=>$list[0]['id']));


if($reply['iscjnum']==0){
    $winUserNum = 0;
    foreach ($awardslists as $v) {

            $winUserNum++;
    }
}else{
    $winUserNum = 0;
    foreach ($awardslist as $v) {
        $winUserNum++;
    }
}


if(empty($reply['start_picurl'])){
	$bg = "../addons/haoman_base/dpm/bg2.jpg";
}else{
	$bg = tomedia($reply['start_picurl']);
}
$music = tomedia($reply['backpicurl']);

if($themes == 'normal'){
    include $this->template('dpm_index2');
}else{
    include $this->template('dpm_3dnewindex2');
}
        