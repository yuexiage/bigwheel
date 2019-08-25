<?php
/**
 * 通用表单模块订阅器
 *
 * @author yuexiage
 * @url http://bbs.we7.cc/
 */
 
defined('IN_IA') or exit('Access Denied');
class Yuexiage_bigwheelModuleSite extends WeModuleSite {
    
    public function __construct(){
        global $_GPC, $_W;
        
        $sql = 'SELECT `settings` FROM ' . tablename('uni_account_modules') . ' WHERE `uniacid` = :uniacid AND `module` = :module';
        $settings = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid'], ':module' => 'yuexiage_bigwheel'));
        $this->settings = iunserializer($settings);
    }

    public function doWebBigwheel(){
        global $_GPC, $_W;
        $do = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $do = in_array($do, array('display', 'post', 'receive_bigwheel')) ? $do : 'display';
        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $action = $_GPC['action']?$_GPC['action']:'bigwheel';
        load()->func('tpl');
        if ($do == 'display') {
            if ($action=='bigwheel') {
                //大转盘
                $activity = pdo_fetchall("SELECT * FROM ".tablename('bigwheel_rep')." WHERE  uniacid = {$_W['uniacid']}");

                include $this->template('bigwheel');
            }elseif($action=='bigwheellist'){

                if (!empty($_GPC['keyword'])) {
                    $condition .= " AND (realname LIKE :realname) OR  (tel LIKE :tel)";
                    $params[':realname'] = "%{$_GPC['keyword']}%";
                    $params[':tel'] = "%{$_GPC['keyword']}%";
                }

                $bigwheellist = pdo_fetchall("SELECT * FROM ".tablename('bigwheel_rep_award')." WHERE uniacid = '{$_W['uniacid']}' $condition and ruid={$_GPC['rid']} ORDER BY id LIMIT ".($pindex - 1) * $psize.','.$psize,$params);
                $gifts=array('0'=>'one','1'=>'two','2'=>'three','3'=>'four','4'=>'five','5'=>'six');
                

                $total=pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('bigwheel_rep_award') . " WHERE uniacid = {$_W['uniacid']} $condition and ruid={$_GPC['rid']} ",$params);


                $pager = pagination($total, $pindex, $psize);
                include $this->template('bigwheellist');
            }
        }elseif ($do == 'post') {
            $id = intval($_GPC['rid']);
            if ($action=='bigwheel') {
                if(!empty($id)) {
                    $activity = pdo_fetch("SELECT * FROM ".tablename('bigwheel_rep')." WHERE rid = $id AND uniacid = {$_W['uniacid']}");
                    if(empty($activity)) {
                        message('活动不存在或已删除', '', 'error');
                    }
                }else{
                    $activity = array(
                        'displayorder' => 0
                    );
                }

                if (checksubmit('submit')) {
                    if (empty($_GPC['title'])) {
                        message('抱歉，请输入活动名称！');
                    }

                    $data = array(
                        'uniacid'           => $_W['uniacid'],
                        'title'             => $_GPC['title'],
                        'enabled'           => intval($_GPC['enabled']),
                        'description'       => $_GPC['description'],
                        'datelimit_start'   => $_GPC['datelimit']['start'],
                        'datelimit_end'   => $_GPC['datelimit']['end'],
                        'follow'            => $_GPC['follow'],
                       

                        'c_type_one'   => $_GPC['c_type_one'],
                        'c_name_one'   => $_GPC['c_name_one'],
                        'c_num_one'    => $_GPC['c_num_one'],
                        'c_rate_one'   => $_GPC['c_rate_one'],

                        'c_type_two'   => $_GPC['c_type_two'],
                        'c_name_two'   => $_GPC['c_name_two'],
                        'c_num_two'    => $_GPC['c_num_two'],
                        'c_rate_two'   => $_GPC['c_rate_two'],

                        'c_type_three'   => $_GPC['c_type_three'],
                        'c_name_three'   => $_GPC['c_name_three'],
                        'c_num_three'    => $_GPC['c_num_three'],
                        'c_rate_three'   => $_GPC['c_rate_three'],

                        'c_type_four'   => $_GPC['c_type_four'],
                        'c_name_four'   => $_GPC['c_name_four'],
                        'c_num_four'    => $_GPC['c_num_four'],
                        'c_rate_four'   => $_GPC['c_rate_four'],

                        'c_type_five'   => $_GPC['c_type_five'],
                        'c_name_five'   => $_GPC['c_name_five'],
                        'c_num_five'    => $_GPC['c_num_five'],
                        'c_rate_five'   => $_GPC['c_rate_five'],
                        'rid'   => $id,
                        'c_type_six'   => $_GPC['c_type_six'],
                        'c_name_six'   => $_GPC['c_name_six'],
                        'c_num_six'    => $_GPC['c_num_six'],
                        'c_rate_six'   => $_GPC['c_rate_six'],
                        'award_times'   => $_GPC['award_times'],
                        'number_times'   => $_GPC['number_times'],                
                        'most_num_times'   => $_GPC['most_num_times'],
                        
                        'new_title'   => $_GPC['new_title'],
                        'img'   => $_GPC['img'],
                        'new_description'   => $_GPC['new_description'],
                        'show_num'   => $_GPC['show_num']
                    );

                    if (!empty($id)) {
                        pdo_update('bigwheel_rep', $data, array('rid' => $id));
                    } else {
                        pdo_insert('bigwheel_rep', $data);
                        $id = pdo_insertid();
                    }
                    message('更新活动成功！', $this->createWebUrl('bigwheel', array('op' => 'display','action'=>'bigwheel')), 'success');
                }

            include $this->template('bigwheel');
            }
        }elseif ($do == 'receive_bigwheel') { 
            $rid = intval($_GPC['rid']);
            $id = intval($_GPC['id']);
            if(!empty($id)) {
                $activity = pdo_fetch("SELECT * FROM ".tablename('bigwheel_rep_award')." WHERE ruid = {$rid} AND uniacid = {$_W['uniacid']}");
                if(empty($activity)) {
                    message('名单不存在或已删除', '', 'error');
                }
            }else{
                $activity = array(
                    'displayorder' => 0
                );
            }
            $data = array();
            $data['status'] = 2;
            $data['receive'] = time();
            pdo_update('bigwheel_rep_award',$data,  array('id' => $id));
            message('更新成功！', $this->createWebUrl('bigwheel', array('op' => 'display','action'=>'bigwheellist','rid'=>$rid)), 'success');
        } 
    }

    public function doMobileBigwheel(){
        global $_GPC,$_W;
		
        $id = $_GPC['id'];//rid
        $bigwheel = pdo_fetch("SELECT * FROM ".tablename('bigwheel_rep')." WHERE  enabled=1 and rid = {$id} and uniacid = {$_W['uniacid']}");
        $title = $bigwheel['title'];
        if($bigwheel['follow']){
            //如果必须关注，验证关注
            checkauth();
        }
        $gifts=array('1'=>'one','2'=>'two','3'=>'three','4'=>'four','5'=>'five','6'=>'six');

        //每人最多获奖次数
        $award_times  = $bigwheel['award_times'];
        //每人最多抽奖次数
        $number_times = $bigwheel['number_times'];
        //每人每天最多抽奖次数
        $most_num_times = $bigwheel['most_num_times'];

        //查询已参加次数
        $uid = $_W['member']['uid'];
        $winner = pdo_fetchall("SELECT * FROM ".tablename('bigwheel_rep_award')." WHERE  from_user={$uid} and ruid = {$id} and uniacid = {$_W['uniacid']}");


        //已经参加次数
        $number_times_uid = count($winner);

        foreach ($winner as $key => $value) {
            if($value['status']>0){
                //获奖次数
                $award_times_uid += 1;
                $winners[]=$winner[$key];
            }
            if(date("Y-m-d",$value['createtime'])==date("Y-m-d")){
                //今日抽奖次数
                $most_num_times_uid += 1;
            } 
        }

        //判断时间
        $datelimit_start = strtotime($bigwheel['datelimit_start']);
        $datelimit_end   = strtotime($bigwheel['datelimit_end']);
        $start=0;
        if($datelimit_start>time()){
            //未开始
            $start = 1;
        }elseif($datelimit_end<time()){
            //活动已结束
            $start = 2;
        }
        $end = 0;
        if(isset($number_times_uid) && $number_times_uid>=$number_times){
            //不能再参加
            $end = 1;
        }
        if(isset($award_times_uid) && $award_times_uid>=$award_times){
            //不能再参加
            $end = 2;
        }
        if(isset($most_num_times_uid) && $most_num_times_uid>=$most_num_times){
            //不能再参加
            $end = 3;
        }

        if($_GPC['wid']){
            $update=array();
            $update['realname']=$_GPC['realname'];
            $update['tel']=$_GPC['tel'];
            pdo_update('bigwheel_rep_award', $update, array('id' => $_GPC['wid']));
            mc_update($_W['member']['uid'],array('realname' => $_GPC['realname'],'mobile'=>$_GPC['tel']));
            message('保存成功', referer(), 'success');
        }
        $_share = array(
            'title'   => $bigwheel['title'],
            'link'    => $_W['siteurl'],
            'imgUrl'  => $_W['siteroot'].'addons/yuexiage_touristmall/images/images/p.png',
            'content' => $bigwheel['title']
        );

        include $this->template('bigwheel');
    }

    public function doMobileChecklottery(){
        global $_GPC, $_W;
        $activity = 'bigwheel_rep';
        $rid = $_GPC['rid'];
        $activity_winner = 'bigwheel_rep_award';
        $bigwheel = pdo_fetch("SELECT * FROM ".tablename($activity)." WHERE  enabled=1 and rid = {$rid} and uniacid = {$_W['uniacid']}");
        if($bigwheel['follow']){
            //如果必须关注，验证关注
            checkauth();
        }


        //判断时间
        $datelimit_start = strtotime($bigwheel['datelimit_start']);
        $datelimit_end   = strtotime($bigwheel['datelimit_end']);
        $end=0;

        if($datelimit_start>time()){
            //未开始
           echo $end = 4;
            exit;
        }elseif($datelimit_end<time()){
            //活动已结束

            echo $end = 5;
            exit;
        }

        //每人最多获奖次数
        $award_times  = $bigwheel['award_times'];
        //每人最多抽奖次数
        $number_times = $bigwheel['number_times'];
        //每人每天最多抽奖次数
        $most_num_times = $bigwheel['most_num_times'];

        //查询已参加次数
        $uid = $_W['member']['uid'];
        $winner = pdo_fetchall("SELECT * FROM ".tablename($activity_winner)." WHERE  from_user={$uid} and ruid = {$rid} and uniacid = {$_W['uniacid']}");

        //已经参加次数
        $number_times_uid = count($winner);

        
        foreach ($winner as $key => $value) {
            if($value['status']>0){
                //获奖次数
                $award_times_uid += 1;
            }
            if(date("Y-m-d",$value['createtime'])==date("Y-m-d")){
                //今日抽奖次数
                $most_num_times_uid += 1;
            } 
        }

        if(isset($number_times_uid) && $number_times_uid>=$number_times){
            //不能再参加
           echo  $end = 1;
            exit;
        }
        if(isset($award_times_uid) && $award_times_uid>=$award_times){
            //不能再参加
            echo  $end = 2;
            exit;
        }
        if(isset($most_num_times_uid) && $most_num_times_uid>=$most_num_times){
            //不能再参加
            echo $end = 3;
            exit;
        }
        echo $end;
    }

    public function doMobilegetAward(){
        global $_GPC, $_W;
        $rid = $_GPC['rid'];
        $activity = 'bigwheel_rep';
        $activity_winner = 'bigwheel_rep_award';
        //计算每个礼物的概率
        $bigwheel = pdo_fetch("SELECT * FROM ".tablename($activity)." WHERE  enabled=1 and rid=$rid and uniacid = {$_W['uniacid']}");
        $award = array();
        $gifts=array('1'=>'one','2'=>'two','3'=>'three','4'=>'four','5'=>'five','6'=>'six');

        foreach ($gifts as $key=> $gift){
            if (empty($bigwheel['c_rate_'.$gift])) {
                //如果几率为空
                continue;
            }
            for($i=0;$i<$bigwheel['c_rate_'.$gift];$i++){
                $award[]=$key;
            }
        }
		$base_num = $this->settings['base_num']?$this->settings['base_num']:'100';
        $s= $base_num-count($award);
        for($i=0;$i<$s;$i++){
            $award[]=0;
        }

        $z = $award[array_rand($award)];
        $column = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($activity_winner)." WHERE  rid={$z} and ruid = {$rid} and uniacid = {$_W['uniacid']}");
        for($i=0;$i<6;$i++){
            if( ($bigwheel['c_num_'.$gifts[$z]]-$column)<=0 ){
                continue;
            }else{
                break;
            }
        }

        if( ($bigwheel['c_num_'.$gifts[$z]]-$column)<=0 ){
            $z =0;
        }

        $credit = array(
            'uniacid' => $_W['uniacid'],
            'rid'=>$z,
            'aid'     => $bigwheel['c_type_'.$gifts[$z]]?$bigwheel['c_type_'.$gifts[$z]]:'0',
            'award'   => $bigwheel['c_name_'.$gifts[$z]]?$bigwheel['c_name_'.$gifts[$z]]:'0',
            'description' => $z?'您获得'.$bigwheel['c_name_'.$gifts[$z]]:'很遗憾，感谢您的参与!',
            'from_user'=>$_W['member']['uid'],
            'status'=>$z?'1':'0',
            'ruid'=>$rid,
            'createtime'=>time()
        );
		
        switch ($z) {
            case '3':
                $credit['description'] = '恭喜您  200元旅游抵扣券，请截图保留此页面兑奖码，联系工作人员参与兑奖';
                break;
            case '4':
                $credit['description'] = '恭喜您  10元微信红包，请截图保留此页面兑奖码，联系工作人员参与兑奖！ 微信联系方式：522607761';
                break;
            case '5':
                $credit['description'] = '恭喜您  0.88元微信红包，请截图保留此页面兑奖码，联系工作人员参与兑奖！ 微信联系方式：522607761';
                break;
            case '6':
                $credit['description'] = '恭喜您，获得一次抽奖机会';
                break;
            default:
                # code...
                break;
        }

        pdo_insert($activity_winner, $credit);
        $credit['wid'] = pdo_insertid();
        load()->model('mc');
        switch ($credit['aid']) {
            case 0:
                # code...
                break;
            case 1:
                //积分
mc_credit_update($_W['member']['uid'], 'credit1', $bigwheel['c_name_'.$gifts[$z]], array($_W['member']['uid'], '抽奖'.$credit['wid'].'增加积分'.$bigwheel['c_name_'.$gifts[$z]]));
            load()->func('logging');
            logging_run('积分'.$ra);
                break;
            case 3:
                //其他
                break;
            default:
                break;
        }
        echo json_encode($credit);
    }
}