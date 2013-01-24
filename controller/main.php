<?php
class Person {
	var $m_hp	  = 200;			// 血量
	var $m_attack = array(0, 0);	// 攻击上限下限
	var $m_object;					// 人物的攻击目标
	/**
	 * 构造一个任务
	 *
	 * @param 血量 $hp
	 * @param 攻击力 $attack
	 */
	function __construct($hp = 0, $attack = array(0, 0)) {
		$this->m_hp		= $hp;
		$this->m_attack = $attack;
	}
	
	/**
	 * 设置当前人物的攻击目标
	 *
	 * @param unknown_type $obj
	 */
	function setAttackObject($obj = 0) {
		$this->m_object = $obj;
	}
	
	/**
	 * 受到伤害
	 *
	 * @param 减少相应的血量 $hp
	 */
	function Hurt($hp = 0) {
		$this->m_hp -= $hp;
		if ($this->m_hp < 0) {
			$this->m_hp = 0;
		}
	}
	
	/**
	 * 人物是否死亡
	 *
	 * @return 死亡返回true，否则返回false
	 */
	public function isDeath() {
		return $this->m_hp == 0;
	}
}

class Game {
	var $m_p1;
	var $m_p2;
	
	/**
	 * 战斗系统，两个人对战
	 *
	 * @param 人物1 $p1
	 * @param 人物2 $p2
	 */
	function Init($p1 = 0, $p2 = 0) {
		$this->m_p1 = $p1;
		$this->m_p2 = $p2;
	}
	
	function Attack() {
		
	}
	/**
	 * 两个人对战过程
	 *
	 */
	function Fight() {
		$damage = array();
		while (!$this->m_p1->isDeath() && !$this->m_p2->isDeath()) {
			$v = rand($this->m_p2->m_attack[0], $this->m_p2->m_attack[1]);
			$damage[] = array('type' => 'r', 'value' => $v);
			$this->m_p1->Hurt($v);
			
			$v = rand($this->m_p1->m_attack[0], $this->m_p1->m_attack[1]);
			$damage[] = array('type' => 'l', 'value' => $v);
			$this->m_p2->Hurt($v);
		}
		return $damage;
	}
}
class main extends spController {
	function index(){
		$this->display('index.html');
	}
	
    function map() {
        $this->display('map.html');
    }

	function fight() {
		import('Fighter.php');
		$fighting = new Fighting();
		$fighting->Init();
		for ($i = 0; $i < 1; $i++) {
			$fighter = new Fighter();
			$fighter->hp = 200;
			$fighter->name = 'Red_1'.$i;
			$fighter->damage = array(50,100);
			$fighting->SetFighters($fighter, RED);
		}
		
		for ($i = 0; $i < 1; $i++) {
			$fighter = new Fighter();
			$fighter->hp = 100;
			$fighter->name = 'Blue_1'.$i;
			$fighting->SetFighters($fighter, BLUE);
		}
		$fighting->setFighterTarget(RED);
		$fighting->setFighterTarget(BLUE);
		
		$log = $fighting->teamFighting();
		echo json_encode($log);
	}
	
	function ptest() {
		import('Fighter.php');
		import('Monster.php');
		$fighting = new Fighting();
		$fighting->Init();
		
		$player = new Player();
		$monster = new Monster();
		
		$player->getInfo(1);

		$player->hp = 200;
		$player->name = 'player';
		$player->damage = array(50, 100);
		$fighting->SetFighters($player, RED);

		$monster->hp = 100;
		$monster->name = 'monster';
		$monster->damage = array(50, 100);
		$fighting->SetFighters($monster, BLUE);
		
		$fighting->setFighterTarget(RED);
		$fighting->setFighterTarget(BLUE);
		
		$log = $fighting->teamFighting();
		$player->gold += 100;

		$player->save();
		echo json_encode($log);
	}
	function test() {
		$_SESSION['aaa'] = 'aa';
	}
	
	function arrTest() {
		import('Fighter.php');
		$redTeam = new FTeam();
		$blueTeam = new FTeam();
		for($i = 0; $i < 3; $i++) {
			$fighter = new Fighter();
			$fighter->hp = 50;
			$fighter->name = 'Red_'.$i;
			
			$redTeam->setFighter($fighter);
			$blueTeam->setFighter($fighter);
		}
		
		$redTeam->Death(1);
		$redTeam->output();
		print_r($redTeam->RandomTarget());
	}
	
	function addTask() {
		import('Task.php');
		$task = new Task();

		import('Fighter.php');
		$f = new Fighter();
		$f->uid = 1;
		$task->setPlayer($f);
		
		$newtask = array('taskid' => 88);
		print_r($task->addTask($newtask));
		$newtask = array('taskid' => 3);
		print_r($task->addTask($newtask));
		$task->taskExist(1);
	}
	
	function doTask() {
		import('Fighter.php');
		$p = new Fighter();
		$p->uid = 1;
		
		import('Task.php');
		$task = new Task();
		$task->setPlayer($p);
		$t = array('taskid' => 3);
		$task->addTask($t);
	}
	
	function addGameTask() {
		import('Task.php');
		$tasksys = new Task();
		$newtask = array(
			'name'	=> '豺狼人之灾',
			'cfg'	=> json_encode(array('npcstart'	=> '1000', 'npcend'	=> '1001', 'cond' =>  array('2000' => 10, '2001' => 5), 'prize' => array('gold' => 100, 'exp' => 1000))),
			'lowlevel'	=> 10
		);
		
		$tasksys->addGameTask($newtask);
	}
	
	function player() {
		import('Monster.php');
		$m = new Monster();
		$m->output();
	}
	function sess() {
		$_SESSION['uid'] = 1000;
	}

	function getsess() {
		print_r($_SESSION);
	}
	function area() {
		import('Fighter.php');
		$m = new Player();
		$area = $m->charea(2000000);
		print_r($area);
	}
	function recv() {
		$type = POST('type', '');
		$data = POST('data', '');
		
		import('Fighter.php');
		$player = new Player();
		$msgarr = array();
		switch ($type) {
			// 杀怪
			case 'fight' :
				$msg = $player->fight($data);
				break;
			// 售卖
			case 'sell' :
				$msg = $player->sell($data);
				break;
			// 组队
			case 'team' :
				$msg = $player->team($data);
				break;
			case 'charea' :
				$msg = $player->charea($data);
				break;
			// 聊天
			case 'chat' :
				$msg = $player->chat($data);
				break;
			// 退出登录
			case 'logout' :
				$msg = $player->logout($data);
				break;
			// 挂机
			case 'autofight' :
				$msg = $player->autofight($data);
				break;
			default:
				break;
		}
        if (!empty($msg)) {
            $msgarr += $msg;
        }
		echo json_encode($msgarr);
		return;
	}
}