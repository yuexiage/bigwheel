<?php
/**
 * 大转盘模块处理程序
 *
 * @author yuexiage
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Yuexiage_bigwheelModuleProcessor extends WeModuleProcessor {
	public function respond() {
        global $_W; 
        $rid = $this->rule;
		$content = $this->message['content'];
		if($rid){
            //查询规则对应的活动数据
            $sql = "SELECT * FROM " . tablename('bigwheel_rep') . " WHERE `rid`=:rid AND uniacid = {$_W['uniacid']} LIMIT 1";
            $row = pdo_fetch($sql, array(':rid' => $rid));
            if ($row==false) {
                //不是此活动的规则
                return array();
            }

            if($row['enabled']==0){
                //活动未启动
                return array();
            }

            if(strtotime($row['datelimit_start'])>time()){
                //活动未开始
                return $this->respText('活动未开始!');
            }elseif(strtotime($row['datelimit_end'])<time()){
                //活动已结束
                return $this->respText('活动已结束!');
            }

            return $this->respNews(array(
                'Title' => $row['new_title'],
                'Description' => $row['new_description'],
                'PicUrl' =>tomedia($row['img']),
                'Url' => $this->createMobileUrl('bigwheel', array('id' => $rid)),
            )); 










        }



        return $this->respText(var_export($row,true));



	}
}