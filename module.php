<?php
/**
 * 大转盘模块定义
 *
 * @author yuexiage
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Yuexiage_bigwheelModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		if(!empty($rid)){
			$repinf = pdo_fetch('SELECT * FROM '.tablename('bigwheel_rep').' WHERE uniacid=:wid AND rid=:rid', array(':wid'=>$_W['uniacid'],':rid'=>$rid));
		}
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_W, $_GPC;

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
				'rid'   => $rid,
                'c_type_six'   => $_GPC['c_type_six'],
                'c_name_six'   => $_GPC['c_name_six'],
                'c_num_six'    => $_GPC['c_num_six'],
                'c_rate_six'   => $_GPC['c_rate_six'],
                'award_times'   => $_GPC['award_times'],
                'number_times'   => $_GPC['number_times'],                
                'most_num_times'   => $_GPC['most_num_times'],
				
				'new_title'         => $_GPC['new_title'],
				'img'               => $_GPC['img'],
				'new_description'   => $_GPC['new_description'],
                'bg_img'            => $_GPC['bg_img'],
                'ok_description'    => $_GPC['ok_description'],
                'start_img'         => $_GPC['start_img'],
                'show_num'   => $_GPC['show_num']
            );

			
            if (!empty($rid)) {
				$repinf = pdo_fetch('SELECT * FROM '.tablename('bigwheel_rep').' WHERE uniacid=:wid AND rid=:rid', array(':wid'=>$_W['uniacid'],':rid'=>$rid));
                if($repinf){
					pdo_update('bigwheel_rep', $data, array('rid' => $rid));
				}else{
					pdo_insert('bigwheel_rep', $data);
				}
				
            }
	}

	public function ruleDeleted($rid) {
		global $_W;
        //删除规则时调用，这里 $rid 为对应的规则编号
        $sql = 'DELETE FROM '.tablename('bigwheel_rep')." WHERE rid='{$rid}' AND uniacid='{$_W['uniacid']}'";
        pdo_query($sql);
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现） 
		if(checksubmit()) {
            $dat = array(
                'base_num'      => $_GPC['base_num']
            );
			//字段验证, 并获得正确的数据$dat
			if ($this->saveSettings($dat)) {
                message('保存成功', 'refresh');
            }
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}