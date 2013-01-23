<?php
	/**
	 * 游戏区域
	 *
	 */
	class Area extends spModel {
		public $name 	= '';		// 区域名称
		public $id		= 0;		// 区域ID
		
		function addArea($area = 0) {
			
		}
		
		function get($areaid = 0) {
			$cond = array('areaid' => $areaid);
			$m = new spModel();
			$m->tbl_name = 'wow_area';
			$re = $m->find($cond, 'areaid');
			if (empty($re)) {
				return array();
			}
			return $re;
		}
	}