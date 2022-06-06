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

class table_common_member_plugin extends discuz_table
{
	public function __construct() {
		$this->_table = 'common_member_plugin';

		parent::__construct();
	}

	public function fetch_all_loginid($identifier, $subtype, $loginid) {
		if ($identifier == '') {
			if ($subtype > 0) {
				return DB::fetch_all("SELECT * FROM %t WHERE `loginid`=%s AND subtype=%d", array($this->_table, $loginid, $subtype));
			}
			return DB::fetch_all("SELECT * FROM %t WHERE `loginid`=%s", array($this->_table, $loginid));
		}
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return array();
		}
		if (!$subtype) {
			return DB::fetch_all("SELECT * FROM %t WHERE loginid=%s AND `appid` IN (%i)", array($this->_table, $loginid, dimplode($appid)));
		}
		return DB::fetch_all("SELECT * FROM %t WHERE loginid=%s AND `appid`=%d AND `subtype`=%d", array($this->_table, $loginid, $appid, $subtype));
	}

	public function fetch_all_by_uid($identifier, $subtype, $uid) {
		if ($identifier == '') {
			if ($subtype > 0) {
				return DB::fetch_all("SELECT * FROM %t WHERE `uid`=%d AND subtype=%d", array($this->_table, $uid, $subtype));
			}
			return DB::fetch_all("SELECT * FROM %t WHERE `uid`=%d", array($this->_table, $uid));
		}
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return array();
		}
		if (!$subtype) {
			return DB::fetch_all("SELECT * FROM %t WHERE `uid`=%d AND `appid` IN (%i)", array($this->_table, $uid, dimplode($appid)));
		}
		return DB::fetch_all("SELECT * FROM %t WHERE `uid`=%d AND `appid`=%d AND `subtype`=%d", array($this->_table, $uid, $appid, $subtype));
	}

	public function delete_by_loginid($identifier, $subtype, $loginid) {
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return false;
		}
		if (!$subtype) {
			return DB::query("DELETE FROM %t WHERE loginid=%s AND `appid` IN (%i)", array($this->_table, $loginid, dimplode($appid)));
		}
		return DB::query("DELETE FROM %t WHERE loginid=%s AND `appid`=%d AND `subtype`=%d", array($this->_table, $loginid, $appid, $subtype));
	}

	public function delete_by_uid($identifier, $subtype, $uid) {
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return false;
		}
		if (!$subtype) {
			return DB::fetch_all("DELETE FROM %t WHERE `uid`=%s AND `appid` IN (%i)", array($this->_table, $uid, dimplode($appid)));
		}
		return DB::fetch_all("DELETE FROM %t WHERE `uid`=%s AND `appid`=%d AND `subtype`=%d", array($this->_table, $uid, $appid, $subtype));
	}

	public function update_by_loginid($identifier, $subtype, $loginid, $data) {
		if ($subtype == 0) {
			return false;
		}
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return false;
		}
		$update_data = array();
		if (isset($data['status'])) {
			$update_data['status'] = $data['status'];
		}
		if (isset($data['extra'])) {
			$update_data['extra'] = $data['extra'];
		}
		if (!$update_data) {
			return false;
		}
		return DB::UPDATE($this->_table, $update_data, array('loginid' => $loginid, 'appid' => $appid, 'subtype' => $subtype), 'UNBUFFERED');
	}

	public function update_by_uid($identifier, $subtype, $uid, $data) {
		if ($subtype == 0) {
			return false;
		}
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return false;
		}
		$update_data = array();
		if (isset($data['status'])) {
			$update_data['status'] = $data['status'];
		}
		if (isset($data['extra'])) {
			$update_data['extra'] = $data['extra'];
		}
		if (!$update_data) {
			return false;
		}
		return DB::UPDATE($this->_table, $update_data, array('uid' => $uid, 'appid' => $appid, 'subtype' => $subtype), 'UNBUFFERED');
	}

	public function register($identifier, $subtype, $data) {
		$appid = $this->_get_appid($identifier, $subtype);
		if (!$appid) {
			return false;
		}
		return DB::insert($this->_table, array(
			'uid' => $data['uid'],
			'appid' => $appid,
			'subtype' => $subtype,
			'loginid' => $data['loginid'],
			'regdate' => TIMESTAMP,
			'extra' => $data['extra'],
			'status' => 0,
		), false, true);
	}

	private function _get_appid($identifier, $subtype = 0) {
		global $_G;
		if (!$subtype) {
			return array_unique(array_values($_G['setting']['plugins']['appbind'][$identifier]));
		}
		if (!empty($_G['setting']['plugins']['appbind'][$identifier][$subtype])) {
			return $_G['setting']['plugins']['appbind'][$identifier][$subtype];
		}
		return 0;
	}
}