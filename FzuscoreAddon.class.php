<?php

namespace Addons\Fzuscore;
use Common\Controller\Addon;

/**
 * 教务处成绩查询插件
 * @author 泽泽
 */

    class FzuscoreAddon extends Addon{

        public $info = array(
            'name'=>'Fzuscore',
            'title'=>'教务处成绩查询',
            'description'=>'福州大学教务处成绩查询',
            'status'=>1,
            'author'=>'泽泽',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Fzuscore/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Fzuscore/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }