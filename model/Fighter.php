<?php
define(RED,		0);
define(BLUE,	1);
/**
 * 战斗对象类，玩家，怪物，NPC等的基类
 */
class Fighter {
    var $hp		= 0;				// 血量
    var $mp		= 0;				// 魔法量
    var $damage	= array(0, 100);	// 攻击力
    var $exp	= 0;				// 经验值
    var $uid	= 0;
    var $name	= 'default';		// 人物名称
    var $target = 0;				// 人物当前目标
		
    var $gold	= 0;				// 人物金钱数量
    var $task_list = array();
		
    public function output() {
        echo $this->name . '的生命是'. $this->hp. '他的目标是' . $this->target->name . '<br />';
    }
		
    public function SetTarget($fighter = array()) {
        $this->target = $fighter;
    }
		
    /**
     * 战斗对象造成的伤害
     */
    public function getDamage() {
        return rand($this->damage[0], $this->damage[1]);
    }
		
    /**
     * 目标受伤，生命值减去相应数值
     *
     * @param unknown_type $damage
     */
    public function Hurt($damage = 0) {
        $this->hp -= $damage;
        if ($this->hp <= 0) {
            $this->hp = 0;
        }
    }
		
    /**
     * 目标是否存活
     *
     * @return unknown
     */
    public function isLive() {
        return $this->hp > 0;
    }
}
	
class FTeam extends stdClass {
    // 战斗成员数据
    var $fighters = array();
		
    // 战斗成员的ID
    var $fighterid = array();	
		
    // 存活的战斗成员个数
    var $live = 0;
		
    /**
     * 添加战斗成员
     *
     * @param unknown_type $fighter
     */
    public function setFighter($fighter = array()) {
        $this->fighters[] 	= $fighter;
        $this->fighterid[] 	= count($this->fighterid);
        $this->live++;
    }
		
    /**
     * 某成员死亡，将其从存活ID列表中去除
     * 将其与存活ID列表最后一位交换，再将最后一个元素unset掉即可
     * @param 死亡成员的下标 $fighterid
     */
    public function Death($fighterid = 0) {
        $index = 0;
        for ($i = 0; $i < $this->live; $i++) {
            if ($this->fighterid[$i] == $fighterid) {
                $index = $i;
                break;
            }
        }
        $this->fighterid[$index] = $this->fighterid[$this->live-1];
        unset($this->fighterid[$this->live-1]);
			
        // 存活人数减少
        $this->live--;
    }
		
    /**
     * 从存活的成员中随机挑选一位作为被攻击目标
     *
     * @return unknown
     */
    public function RandomTarget() {
        if ($this->live < 1) {
            return false;
        }

        $rand = rand(0, $this->live-1);			
        $fightid = $this->fighterid[$rand];
        return $this->fighters[$fightid];
    }
		
    /**
     * 队伍存活,至少有一人存活
     *
     */
    public function teamLive() {
        return $this->live > 0;
    }
		
    public function output() {
        print_r($this->fighters);
        print_r($this->fighterid);
        echo $this->live.'存活';
    }
}
	
/**
 * 战斗类，多人战斗过程
 *
 */
class Fighting {
    var $redTeam = null;	// 红方队员
    var $blueTeam = null;	// 蓝方队员
		
    /**
     * 初始化战斗人员
     *
     * @param Fighter Object $fighter
     * @param Team type $type
     */
    public function SetFighters($fighter = array(), $type = RED) {
        $team 	= &$this->redTeam;
        if ($type == BLUE) {
            $team 	= &$this->blueTeam;
        }
			
        $team->setFighter($fighter);
    }
		
    /**
     * 取得一个随机目标
     *
     * @param 目标类别（红/蓝） $type
     * @return 返回一个随机目标
     */
    public function RandomTarget($type = RED) {
        $team = $this->redTeam;
        if ($type == BLUE) {
            $team = $this->blueTeam;
        }
			
        return $team->RandomTarget();
    }
    /**
     * 战斗之前初始化
     *
     */
    public function Init() {
        $this->redTeam 	= new FTeam();
        $this->blueTeam = new FTeam();
    }
		
    public function setFighterTarget($type = RED) {
        $team 		= $this->redTeam;
        $targetteam = $this->blueTeam;
        if ($type == BLUE) {
            $team 		= $this->blueTeam;
            $targetteam = $this->redTeam;
        }
        foreach ($team->fighters as $key => $fighter) {
            $fighter->SetTarget($targetteam->RandomTarget());
        }
    }
    /**
     * 模拟战斗过程
     *
     * @return 返回战斗结果
     */
    public function teamFighting() {
        $fightlog = array();
        $i = 0;
        while ($this->redTeam->teamLive() && $this->blueTeam->teamLive()) {
            /**
             * 红方对蓝方发起战斗
             */
            foreach ($this->redTeam->fighters as $fighter) {
                $damage = $fighter->getDamage();
                $fighter->target->Hurt($damage);
                $fightlog[$i][] = array('type'		=> 'damage',
										'from' 		=> $fighter->name,
										'to'		=> $fighter->target->name,
										'damage'	=> $damage);
            }
				
            /**
             * 蓝方对红方发起战斗
             */
            foreach ($this->blueTeam->fighters as $fighter) {
                $damage = $fighter->getDamage();
                $fighter->target->Hurt($damage);
                $fightlog[$i][] = array('type'		=> 'damage',
										'from' 		=> $fighter->name,
										'to'		=> $fighter->target->name,
										'damage'	=> $damage);
            }
				
            /**
             * 检查双方存活人数
             */
            foreach ($this->redTeam->fighterid as $fighterid) {
                $fighter = $this->redTeam->fighters[$fighterid];
                if (!$fighter->isLive()) {
                    $this->redTeam->Death($fighterid);
                    $fightlog[$i][] = array('type'		=> 'death',
											'from' 		=> $fighter->name);
                }
            }
				
            foreach ($this->blueTeam->fighterid as $fighterid) {
                $fighter = $this->blueTeam->fighters[$fighterid];
                if (!$fighter->isLive()) {
                    $this->blueTeam->Death($fighterid);
                    $fightlog[$i][] = array('type'		=> 'death',
											'from' 		=> $fighter->name);
                }
            }
				
            /**
             * 检查战斗对象的目标是否存活，不存活则更换目标
             */
            foreach ($this->redTeam->fighterid as $fighterid) {
                $fighter = $this->redTeam->fighters[$fighterid];
                if(!$fighter->target->isLive()) {
                    $fighter->SetTarget($this->blueTeam->RandomTarget(BLUE));
                    $fightlog[$i][] = array('type'		=> 'changetarget',
											'from' 		=> $fighter->name,
											'to'		=> $fighter->target->name);
                }
            }
				
            foreach ($this->blueTeam->fighterid as $fighterid) {
                $fighter = $this->blueTeam->fighters[$fighterid];
                if(!$fighter->target->isLive()) {
                    $fighter->SetTarget($this->redTeam->RandomTarget(RED));
                    $fightlog[$i][] = array('type'		=> 'changetarget',
											'from' 		=> $fighter->name,
											'to'		=> $fighter->target->name);
                }
            }
            $i++;
        }
        return $fightlog;
    }
		
    public function ViewFighters($type = RED) {
        $team = &$this->redTeam;
        if ($type == BLUE) {
            $team = &$this->blueTeam;
        }
        $team->output();
    }
}
	
class Player extends Fighter {
    /**
     * 取玩家个人信息
     *
     * @param unknown_type $uid
     * @return unknown
     */
    function getInfo($uid = 0) {
        $cond = array('uid' => $uid);
        $m = new spModel();
        $m->tbl_name = 'wow_player';
        $re = $m->find($cond, 'uid');
        if (empty($re)) {
            return array();
        }
        $info = unserialize($re['attribute']);
        $this->gold = $info['gold'];
        $this->uid = $uid;
        $this->name = $re['name'];
        $this->exp = $info['exp'];
        return $this;
    }
		
    /**
     * 保存玩家信息
     *
     */
    function save() {
        $m = new spModel();
        $m->tbl_name = 'wow_player';
			
        $info = array(
            'hp'	=> $this->hp,
            'mp'	=> $this->mp,
            'gold'	=> $this->gold,
            'attack' => $this->damage,
            'exp'	=> intval($this->exp)
                      );
        $attr = serialize($info);
        $m->update(array('uid' => $this->uid), array('attribute' => $attr));
    }
		
    function fight($data) {
        return array(
            array('type' => 'fight', 'damage' => rand(100, 200)),
            array('type' => 'exprnum', 'expr' => rand(100, 200)),
                     );
    }
		
    function sell($data) {
        return array(
            array('type' => 'sell', 'gold' => rand(10, 20)),
            array('type' => 'mygold', 'gold' => 5988)
                     );
    }
		
    function charea($data) {
        if(isset($_SESSION['areaid'])||$_SESSION['areaid'] == $data) {
            return array(array('type' => 'charea', 'name' => '已经在该地区', 'areaid' => $data));
        }
        import('Area.php');
        $m = new Area();
        $area = $m->get($data);
        if (empty($area)) {
            return array(
                array('type' => 'charea', 'name' => '未知地区', 'areaid' => $data)
                         );
        }

        $_SESSION['areaid'] = $data;
        return array(
            array('type' => 'charea', 'name' => $area['name'], 'areaid' => $area['areaid'])
                     );
    }
}