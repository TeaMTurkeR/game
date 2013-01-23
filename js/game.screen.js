// 游戏聊天窗口
var ChatWindow = (function(){
	var _window = null;
	var _count = 0;
	var _index = 0;
	var _str = '';
	var CW = {};
	CW.setWindow = function(obj){
		_window = $(obj);
	}
	CW.append = function(str) {
		_count++;
		if(_count > 50) {
			/**
			 * 找到第一个 >
			 */
			 var sp = '</p>';
			 var end = _str.indexOf(sp) + sp.length;
			 _str = _str.substr(end, _str.length);
			 _count--;
		}
  	    _str += str;
		CW.render();
	}
	CW.render = function() {
		$(_window).html(_str);
	}
	
	return CW;
})();

ChatWindow.setWindow($("#screen"));