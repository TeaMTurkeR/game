<?php
	class Task extends spModel {
		public $tbl_name = 'wow_task';
		private $player = 0;
		
		/**
		 * 设置接任务玩家
		 *
		 * @param unknown_type $player
		 * @return unknown
		 */
		function setPlayer($player) {
			if (!is_object($player)) {
				return false;
			}
			$this->player = $player;
			return true;
		}
		
		/**
		 * Enter description here...
		 *
		 * @return unknown
		 */
		function getPlayer() {
			return $this->player;
		}
		
		public function isPlayer() {
			return is_object($this->player);
		}
		/**
		 * 玩家接到一个新任务
		 *
		 * @param unknown_type $task
		 */
		function addTask($task) {
			$status = 0;
			$msg = 'ok';
			if (!$this->isPlayer()) {
				$status = -1;
				$msg	= 'player not exist';
			}
			
			if ($status == 0 && $task['taskid'] < 1) {
				$status = -2;
				$msg	= 'task error';
			}
			
			/**
			 * 判断玩家是否已经接到该任务lo
			 */
			$cond = array('uid' => $this->player->uid, 'taskid' => $task['taskid']);
			$re = parent::find($cond, 'uid');
			if (!empty($re)) {
				$status = -3;
				$msg = 'task exist';
			}
			
			if ($status < 0) {
				return array('status' => $status, 'msg' => $msg);
			}

			$task['uid']	= $this->player->uid;
			$task['status'] = 'INIT';
			$re = parent::create($task);
			return array('status' => $status, 'msg' => $msg);
		}
		
		/**
		 * 判断任务是否有效
		 *
		 * @param unknown_type $taskid
		 */
		function taskExist($taskid = 0) {
			$this->tbl_name = 'wow_gametask';
			$cond = array('id' => $taskid);
			$re = parent::find($cond, 'id');
			if (empty($re)) {
				return false;
			}
			return true;
		}
		
		/**
		 * 系统添加一个新任务
		 *
		 * @param unknown_type $task
		 */
		function addGameTask($task) {
			$this->tbl_name = 'wow_gametask';
			parent::create($task);
		}
	}