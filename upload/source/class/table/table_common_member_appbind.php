<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_common_member.php 31849 2012-10-17 04:39:16Z zhangguosheng $
 */

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_appbind extends discuz_table
{
	public function __construct() {
		$this->_table = 'common_member_appbind';

		parent::__construct();
	}

	public function fetch_all_data() {
		return DB::fetch_all("SELECT * FROM %t", array($this->_table));
	}

	public function bind($identifier, $subtype, $appid = 0) {
		$pluginid = $this->_get_pluginid($identifier);
		if (!$pluginid) {
			return false;
		}
		return DB::insert($this->_table, array(
			'pluginid' => $pluginid,
			'subtype' => $subtype,
			'appid' => $appid,
		), false, true);
	}

	public function unbind($identifier, $subtype) {
		$pluginid = $this->_get_pluginid($identifier);
		if (!$pluginid) {
			return false;
		}
		return DB::query("DELETE FROM %t WHERE `pluginid` = %d AND `subtype` = %d", array($this->_table, $pluginid, $subtype));
	}

	private function _get_pluginid($identifier) {
		global $_G;
		if (!empty($_G['setting']['plugins']['pluginid'][$identifier])) {
			return $_G['setting']['plugins']['pluginid'][$identifier];
		}
		return 0;
	}
}