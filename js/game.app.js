// JavaScript Document
var GameRender = (function(){
	/*PlayerRender : function(player) {
		$("#level").html(player.level);
		$("#healthy").html(player.hp);
		$("#mana").html(player.mp);
		$("#exp").html(player.expr);
	}*/
	var Render = {
		levelup : function(lv) {
			$("#level").html(lv);
			ChatWindow.append('<p class="fontw">恭喜你升到'+lv+'级</p>');
		},
		hpnum	: function(hp) {
			$("#attrheal").html(hp);
		},
		mpnum	: function(mp) {
			$("#mana").html(mp);
		},
		exprnum	: function(expr) {
			$("#exp").html(expr);
		},
		fight	: function(damage) {
			ChatWindow.append('<p class="fontred">你造成了'+damage+'点伤害</p>');
		},
		sell	: function(gold) {
			ChatWindow.append('<p class="fontw">一共卖得金币'+gold+'个</p>');
		},
		mygold	: function(gold) {
			$("#gold").html(gold);
		},
		charea	: function(area) {
			ChatWindow.append('<p class="fontw">你进入'+area+'</p>');
		}
	};
	
	return Render;
})();

var Player = (function(){
	var _attr = {
		_level	: 0,
		_hp		: 0,
		_mp		: 0,
		_expr	: 0,
		levelup	: function(level) {
			_level = level;
			GameRender.levelup(_level);
		},
		hpnum	: function(hp) {
			_hp = hp;
			GameRender.hpnum(_hp);
		},
		mpnum	: function(mp) {
			_mp = mp;
			GameRender.mpnum(_mp);
		},
		exprnum	: function(expr) {
			_expr = expr;
			GameRender.exprnum(_expr);
		},
		fight	: function(damage) {
			GameRender.fight(damage);
		},
		sell	: function(gold) {
			GameRender.sell(gold);
		},
		mygold	: function(gold) {
			GameRender.mygold(gold);
		},
		charea : function(area) {
			GameRender.charea(area);
		}
	};
	
	return _attr;
})();
var Game = (function(){
	var _level = 0;
	var Game = {};
	Game.PlayerEvents = {
		charea : function(areaid) {
			Game.ajax({type:'charea', data:areaid});
		},
	};
	/**
	 游戏事件处理
	 */
	Game.Events = {
		init	: function(msg) {
			if(msg.type != 'init') {
				return;
			}
			alert('游戏开始');
		},
		
		levelup : function(msg) {
			if(msg.type != 'levelup') return;
			Player.levelup(msg.lv);
		},
		
		hpnum : function(msg) {
			if(msg.type != 'hpnum') return;
			Player.hpnum(msg.hp);
		},
		
		mpnum : function(msg) {
			if(msg.type != 'mpnum') return;
			Player.mpnum(msg.mp);
		},
		
		exprnum : function(msg) {
			if(msg.type != 'exprnum') return;
			Player.exprnum(msg.expr);
		},
		
		fight : function(msg) {
			if(msg.type != 'fight') return;
			Player.fight(msg.damage);
		},
		
		sell : function(msg) {
			if(msg.type != 'sell') return;
			Player.sell(msg.gold);
		},
		
		mygold : function(msg) {
			if(msg.type != 'mygold') return;
			Player.mygold(msg.gold);
		},
		
		charea : function(msg) {
			if(msg.type != 'charea') return;
			Player.charea(msg.name);
		}
	};
	
	/**
	 * 游戏属性
	 */
	Game.Attr = {
		getlevel : function() {
			return _level;
		}
	}
	
	Game.msg = function(msg) {
		for(var i = 0; i < msg.length; i++) {
			for(k in Game.Events) {
				Game.Events[k](msg[i]);
			}
		}
	}
	
	Game.ajax = function(msg) {
		$.post('/?a=recv', msg, function(data) {
			Game.msg(data);
		}, 'JSON');
	}
	return Game;
})();