function appendZero(a) {
	var b = 0;
	return 10 > a ? b = "0" + a : b = a, b
}
function formatData(a, b) {
	var c = [],
		c = a.split(" "),
		d = [],
		d = c[1].split(":"),
		e = Number(3600 * d[0]) + Number(60 * d[1]) + Number(d[2]) + Number(b),
		c = Math.floor(e / 3600),
		d = Math.floor((e - 3600 * c) / 60),
		e = e - 3600 * c - 60 * d;
	return c = appendZero(c), d = appendZero(d), e = appendZero(e), c + ":" + d + ":" + e
}
var firstTopDisLayer = cc.Layer.extend({
	_qishuLable: null,
	_nextQishuLable: null,
	_nextTimeLable: null,
	_topSprite: null,
	init: function() {
		this._super();
		cc.Director.getInstance().getWinSize();
		this._topSprite = cc.Sprite.create(g_topImage);
		this.addChild(this._topSprite, 0);
		this.setQishu(" ");
		this.setNextQishu(" ");
		this.setNextTime(" ")
	},
	setQishu: function(a) {
		this._qishuLable = cc.LabelTTF.create(a, "Arial", 18);
		this._qishuLable.setColor(cc.c3b(255, 0, 0));
		this._qishuLable.setPosition(cc.p(-405, -34));
		this.addChild(this._qishuLable, 1)
	},
	setNextQishu: function(a) {
		this._nextQishuLable = cc.LabelTTF.create(a, "Arial", 18);
		this._nextQishuLable.setColor(cc.c3b(255, 234, 0));
		this._nextQishuLable.setPosition(cc.p(438, 14));
		this.addChild(this._nextQishuLable, 1)
	},
	setNextTime: function(a) {
		this._nextTimeLable = cc.LabelTTF.create(a, "Arial", 18);
		this._nextTimeLable.setColor(cc.c3b(255, 0, 0));
		this._nextTimeLable.setPosition(cc.p(440, -18));
		this.addChild(this._nextTimeLable, 1)
	}
}),
	firstBtmDisLayer = cc.Layer.extend({
		_curDate: null,
		_curperiodNumber: null,
		_curNumber: null,
		_curDaXiao: null,
		_curDanShuang: null,
		_curLongHu1: null,
		_curLongHu2: null,
		_curLongHu3: null,
		_curLongHu4: null,
		_curLongHu5: null,
		init: function() {
			this._super();
			cc.Director.getInstance().getWinSize();
			var a = cc.Sprite.create(g_btmImage);
			this.addChild(a, 0);
			this._curDate = cc.LabelTTF.create(" ", "Arial", 24);
			this.setNumberText(this._curDate, cc.p(-370, -25));
			this._curperiodNumber = cc.LabelTTF.create(" ", "Arial", 24);
			this.setNumberText(this._curperiodNumber, cc.p(-174, -25));
			this._curNumber = cc.LabelTTF.create("", "Arial", 28);
			this.setNumberText(this._curNumber, cc.p(-85, -24));
			this._curDaXiao = cc.LabelTTF.create(" ", "Arial", 28);
			this.setNumberText(this._curDaXiao, cc.p(0, -24));
			this._curDanShuang = cc.LabelTTF.create("", "Arial", 28);
			this.setNumberText(this._curDanShuang, cc.p(76, -24));
			this._curLongHu1 = cc.LabelTTF.create(" ", "Arial", 28);
			this.setNumberText(this._curLongHu1, cc.p(163, -24));
			this._curLongHu2 = cc.LabelTTF.create(" ", "Arial", 28);
			this.setNumberText(this._curLongHu2, cc.p(232, -24));
			this._curLongHu3 = cc.LabelTTF.create(" ", "Arial", 28);
			this.setNumberText(this._curLongHu3, cc.p(303, -24));
			this._curLongHu4 = cc.LabelTTF.create(" ", "Arial", 28);
			this.setNumberText(this._curLongHu4, cc.p(374, -24));
			this._curLongHu5 = cc.LabelTTF.create(" ", "Arial", 28);
			this.setNumberText(this._curLongHu5, cc.p(444, -24))
		},
		setNumberText: function(a, b) {
			a.setColor(cc.c3b(255, 234, 0));
			a.setPosition(b);
			this.addChild(a, 1)
		},
		setCurData: function(a) {
			var b = Number(a[0]) + Number(a[1]);
			this._curNumber.setString(b);
			if (b<=11) {
				this._curDaXiao.setString("小");
			}else {
				this._curDaXiao.setString("大");
			}

			if (b % 2 ===0) {
				this._curDanShuang.setString("双");
			} else {
				this._curDanShuang.setString("单");
			}
			Number(a[0]) > Number(a[9]) ? this._curLongHu1.setString("龙") : this._curLongHu1.setString("虎");
			Number(a[1]) > Number(a[8]) ? this._curLongHu2.setString("龙") : this._curLongHu2.setString("虎");
			Number(a[2]) > Number(a[7]) ? this._curLongHu3.setString("龙") : this._curLongHu3.setString("虎");
			Number(a[3]) > Number(a[6]) ? this._curLongHu4.setString("龙") : this._curLongHu4.setString("虎");
			Number(a[4]) > Number(a[5]) ? this._curLongHu5.setString("龙") : this._curLongHu5.setString("虎")
		},
		resetData: function() {
			this._curNumber.setString(" ");
			this._curDaXiao.setString(" ");
			this._curDanShuang.setString(" ");
			this._curLongHu1.setString(" ");
			this._curLongHu2.setString(" ");
			this._curLongHu3.setString(" ");
			this._curLongHu4.setString(" ");
			this._curLongHu5.setString(" ")
		}
	}),
	indexMap = [9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
	g_addSpd = 70,
	g_lessSpd = 30,
	g_endSpeed = 70,
	maxCarNumber = 10,
	g_endCarBasePos = 40,
	g_endCarFirstPos = -80,
	g_loadTime = 5,
	g_offsetDis = 100,
	g_isRunOver = !1,
	g_starPlayMusicTime = 3,
	g_isMusicPlay = !1,
	g_isAddMaskSpr = !0,
	g_carNumber = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
	g_hostIp = "http://"+document.domain+"/Home/Get/getXyft?t="+ Math.random(),
	pk10Scene = cc.Scene.extend({
		onEnter: function() {
			this._super();
			var a = new pk10;
			a.init();
			this.addChild(a);
			g_isAddMaskSpr || a.initCarMask(g_carNumber)
		}
	}),
	CarLayer = cc.Layer.extend({
		_isFirstRun: null,
		_carIndex: null,
		_isRunning: null,
		_carSpr: null,
		_firstLunziSpr: null,
		_secondLunziSpr: null,
		_qiLiu: null,
		_huoYan: null,
		init: function() {
			this._super();
			this._isFirstRun = 1;
			this._isRunning = !1
		},
		getCarIndex: function() {
			return this._carIndex
		},
		initAllSprite: function(a) {
			this._carSpr = cc.Sprite.create(carIndexSrc[a]);
			this._carSpr.setPosition(cc.p(870 - 15 * (9 - a), 0));
			this.addChild(this._carSpr, 0);
			this._firstLunziSpr = cc.Sprite.create(s_carLong);
			this._firstLunziSpr.setPosition(cc.p(22, 8));
			this._secondLunziSpr = cc.Sprite.create(s_carLong);
			this._secondLunziSpr.setPosition(cc.p(85, 8));
			this._qiLiu = cc.Sprite.create(g_qiliuImage);
			this._qiLiu.setOpacity(0);
			this._qiLiu.setPosition(cc.p(80, 18));
			this._huoYan = cc.Sprite.create(g_huoyanImage);
			this._huoYan.setPosition(cc.p(115, 9));
			this._huoYan.setOpacity(0)
		},
		endCarToPosition: function(a) {
			this.unscheduleUpdate();
			this._carSpr.stopAllActions();
			var b = 0;
			0 == a ? b = g_endCarFirstPos : b = g_endCarBasePos + 50 * a;
			a = this._carSpr.getPosition().x - b;
			var c = Math.abs(a);
			0 < a && 80 < c && this.addEffect();
			b = cc.MoveTo.create(4.5, cc.p(b, 0));
			b = cc.EaseInOut.create(b, 2);
			a = cc.CallFunc.create(this.moveCallBack, this);
			b = cc.Sequence.create(b, a);
			this._carSpr.runAction(b)
		},
		starGame: function() {
			this._carSpr.addChild(this._firstLunziSpr, 1);
			this._firstLunziSpr.runAction(cc.RepeatForever.create(cc.RotateBy.create(.5, -1440)));
			this._carSpr.addChild(this._secondLunziSpr, 1);
			this._secondLunziSpr.runAction(cc.RepeatForever.create(cc.RotateBy.create(.5, -1440)));
			this.starRun()
		},
		starRun: function() {
			var a = 200 * Math.random() + 100,
				b = a / 50,
				a = this._carSpr.getPosition().x - a;
			this.addEffect();
			b = cc.MoveTo.create(b, cc.p(a, 0));
			b = cc.EaseInOut.create(b, 2);
			this._carSpr.runAction(cc.Sequence.create(b, cc.CallFunc.create(this.starEnd, this)))
		},
		starEnd: function() {
			this._carSpr.removeChild(this._qiLiu);
			this._carSpr.removeChild(this._huoYan);
			this._isRunning = !0;
			this.scheduleUpdate()
		},
		addEffect: function() {
			this._carSpr.addChild(this._qiLiu, 2);
			var a = cc.FadeIn.create(.1),
				b = a.reverse();
			this._qiLiu.runAction(cc.RepeatForever.create(cc.Sequence.create(a, b)));
			this._carSpr.addChild(this._huoYan, 2);
			a = cc.FadeIn.create(.1);
			b = a.reverse();
			this._huoYan.runAction(cc.RepeatForever.create(cc.Sequence.create(a, b)))
		},
		update: function(a) {
			if (this._isRunning) {
				this._isRunning = !1;
				a = 200 * Math.random() + 225;
				var b = this._carSpr.getPosition().x,
					c = 0;
				50 > b - a ? (b += a, c = a / g_lessSpd) : 900 < b + a ? (b -= a, c = a / g_addSpd, this.addEffect()) : 0 == Math.round(10 * Math.random() + 2) % 2 ? (b += a, c = a / g_lessSpd) : (b -= a, c = a / g_addSpd, this.addEffect());
				a = cc.MoveTo.create(c, cc.p(b, 0));
				a = cc.EaseInOut.create(a, 2);
				this._carSpr.runAction(cc.Sequence.create(a, cc.CallFunc.create(this.moveCallBack, this)))
			}
		},
		moveCallBack: function() {
			this._carSpr.removeChild(this._qiLiu);
			this._carSpr.removeChild(this._huoYan);
			this._isRunning = !0
		},
		rmSpr: function() {
			this._carSpr.removeChild(this._qiLiu);
			this._carSpr.removeChild(this._huoYan)
		}
	}),
	CarManagerLayer = cc.Layer.extend({
		_carArray: null,
		init: function() {
			this._super();
			this._carArray = []
		},
		defaultInitCar: function() {
			cc.Director.getInstance().getWinSize();
			for (var a = 0; a < maxCarNumber; a++) {
				var b = new CarLayer;
				b.init();
				b.initAllSprite(a);
				b.setPosition(cc.p(0, 150 + 31.5 * (9 - a)));
				b._carIndex = a + 1;
				this.addChild(b);
				this._carArray[a] = b
			}
		},
		initCarByRankArray: function(a) {
			cc.Director.getInstance().getWinSize();
			for (var b = 0; 10 > b; b++) {
				var c = new CarLayer;
				c.init();
				c.initAllSprite(b);
				c.setPosition(cc.p(0, 150 + 31.5 * b));
				c._carIndex = Number(a[b]);
				this.addChild(c);
				this._carArray[b] = c
			}
			for (b = 0; 10 > b; b++) console.warn("car index is :" + b + ";car tag is:" + this._carArray[b].getCarIndex())
		},
		starGame: function() {
			for (var a = 0; a < maxCarNumber; a++) this._carArray[a].starGame()
		},
		endCarToPoSisiton: function(a) {
			for (var b = 0; b < maxCarNumber; b++) for (var c = Number(a[b]), d = !1; !d;) for (var e = 0; e < maxCarNumber; e++) if (this._carArray[e].getCarIndex() == c) {
				this._carArray[e].endCarToPosition(b);
				d = !0;
				break
			}
		},
		rmSpr: function() {
			for (var a = 0; a < maxCarNumber; a++) this._carArray[a].rmSpr(a)
		}
	}),
	pk10 = cc.Layer.extend({
		_paoDaoSprite: null,
		_winSize: null,
		_carMgrLayer: null,
		_timeLable: null,
		_timeNumber: null,
		_carNumberSpr: null,
		_topLayer: null,
		_btmLayer: null,
		_gradeNumber: null,
		_xScale: null,
		_qian: null,
		_menu1: null,
		_menu2: null,
		_carNumberMask: null,
		_curData: null,
		_nextData: null,
		_bgNumber: null,
		_curPed: null,
		_nextPed: null,
		init: function() {
			this._super();
			var a = this;
			this._carNumberSpr = [];
			this._carNumberMask = [];
			this._curData = [];
			this._nextData = [];
			this._winSize = cc.Director.getInstance().getWinSize();
			this._paoDaoSprite = cc.Sprite.create(s_PaoDao);
			this._paoDaoSprite.setPosition(cc.p(-1200, .5 * this._winSize.height));
			this.addChild(this._paoDaoSprite, 0);
			this._qian = cc.Sprite.create(g_qianmi);
			this._qian.setPosition(cc.p(-200, .5 * this._winSize.height - 30));
			this.addChild(this._qian, 100);
			this._carMgrLayer = new CarManagerLayer;
			this._carMgrLayer.init();
			this._carMgrLayer.defaultInitCar();
			this._carMgrLayer.setPosition(cc.p(100, 0));
			this.addChild(this._carMgrLayer, 1);
			this._timeNumber = "";
			this._timeLable = cc.LabelTTF.create(this._timeNumber, "Arial", 38);
			this._timeLable.setPosition(cc.p(.5 * this._winSize.width, this._winSize.height / 2));
			this.addChild(this._timeLable, 7);
			this._topLayer = new firstTopDisLayer;
			this._topLayer.init();
			this._topLayer.setPosition(cc.p(490, 595));
			this.addChild(this._topLayer);
			this._btmLayer = new firstBtmDisLayer;
			this._btmLayer.init();
			this._btmLayer.setPosition(cc.p(490, 48));
			this.addChild(this._btmLayer, 100);
			for (var b = 0; 10 > b; b++) {
				var c = cc.Sprite.create(numberIndexSrc[b]);
				c.setPosition(cc.p(65 * b + 170, 595));
				this.addChild(c, 4);
				this._carNumberSpr[b] = c
			}
			this._bgNumber = cc.Sprite.create(g_bgTimePro);
			this._bgNumber.setPosition(cc.p(560, 320));
			this.addChild(this._bgNumber, 6);
			$.getJSON(g_hostIp, function(b) {
				a._topLayer._qishuLable.setString(b.current.periodNumber);
				a._topLayer._nextQishuLable.setString(b.next.periodNumber);
				a._topLayer._nextTimeLable.setString(formatData(b.next.awardTime, b.next.delayTimeInterval));
				a._btmLayer._curDate.setString(b.current.awardTime, cc.p(-370, -25), 24);
				a._btmLayer._curperiodNumber.setString(b.current.periodNumber, cc.p(-174, -25), 24);
				a._curPed = b.current.periodNumber;
				a._nextPed = b.next.periodNumber;
				if (null != b.next.awardNumbers || "" != b.next.awardNumbers) {
					var c = [],
						c = b.current.awardNumbers.split(",");
					a._btmLayer.setCurData(c);
					g_isAddMaskSpr && a.initCarMask(c)
				}
				var c = b.next.awardTimeInterval,
					f = b.next.awardTimeInterval / 1E3,
					f = f + Number(b.next.delayTimeInterval);
				a._timeNumber = f.toFixed(0);
				0 < c ? (f += 6, a._timeNumber = f.toFixed(0), a._timeLable.setString(a._timeNumber), a.schedule(a.timeLableRunning, 1)) : (6<f?a._timeNumber = f:a._timeNumber = 6, a._timeLable.setString(a._timeNumber), a.schedule(a.timeLableRunning, 1))
			});
			this.adjustSizeForWindow()
		},
		initCarMask: function(a) {
			for (var b = 0; 10 > b; b++) {
				var c = cc.Sprite.create(numberIndexSrc[Number(a[b]) - 1]);
				c.setPosition(cc.p(65 * b + 170, 595));
				this.addChild(c, 5);
				this._carNumberMask[b] = c
			}
		},
		initNumberSpr: function(a, b) {
			if (b) for (var c = 0; c < a.length; ++c) {
				var d = cc.Sprite.create(numberIndexSrc[Number(a[c]) - 1]);
				d.setPosition(cc.p(65 * c + 170, 595));
				this.addChild(d, 4);
				this._carNumberSpr[c] = d
			} else for (c = 0; 10 > c; c++) d = cc.Sprite.create(numberIndexSrc[c]), d.setPosition(cc.p(65 * c + 170, 595)), this.addChild(d, 4), this._carNumberSpr[c] = d
		},
		timeLableRunning: function() {
			if (0 < this._timeNumber) this._timeNumber--, this._timeLable.setString(this._timeNumber), 5 == this._timeNumber && (g_isMusicPlay ? cc.AudioEngine.getInstance().rewindMusic() : (cc.AudioEngine.getInstance().playMusic(g_music, !0), g_isMusicPlay = !0));
			else {
				"function" == typeof beforeStartRunning && beforeStartRunning();
				this.unschedule(this.timeLableRunning);
				this._timeLable.setString("");
				this.removeChild(this._bgNumber);
				this._carMgrLayer.starGame();
				this.paoDaoGo();
				this.schedule(this.loadDataFromServer, g_loadTime);
				this.scheduleUpdate();
				for (var a = 0; a < this._carNumberMask.length; a++) this.removeChild(this._carNumberMask[a]);
				this._topLayer._nextQishuLable.setString(" ");
				this._topLayer._nextTimeLable.setString(" ");
				this._btmLayer.resetData()
			}
		},
		paoDaoGo: function() {
			var a = cc.MoveTo.create(2, cc.p(-412.5, this._winSize.height / 2));
			this._paoDaoSprite.runAction(cc.Sequence.create(a, cc.CallFunc.create(this.starLoopPaoDao, this)))
		},
		starLoopPaoDao: function() {
			this._paoDaoSprite.stopAllActions();
			var a = cc.MoveTo.create(0, cc.p(-412.5, this._winSize.height / 2)),
				b = cc.MoveTo.create(1.8, cc.p(1162.5, this._winSize.height / 2));
			this.loopPaoDao = cc.Sequence.create(a, b);
			a = cc.RepeatForever.create(this.loopPaoDao);
			this._paoDaoSprite.runAction(a)
		},
		loadDataFromServer: function(a) {
			var b = this;
			$.getJSON(g_hostIp, function(a) {
				if (b._nextPed == a.current.periodNumber) {
					var d = [],
						d = a.current.awardNumbers.split(",");
					b._carMgrLayer.endCarToPoSisiton(d);
					b._paoDaoSprite.stopAllActions();
					var d = cc.MoveTo.create(1.4, cc.p(2250, b._winSize.height / 2)),
						e = cc.CallFunc.create(b.endPaoDao, b),
						d = cc.Sequence.create(b.loopPaoDao, d, e);
					b._paoDaoSprite.runAction(d);
					b.unschedule(b.loadDataFromServer);
					b._topLayer._qishuLable.setString(a.current.periodNumber);
					b._topLayer._nextQishuLable.setString(a.next.periodNumber);
					b._topLayer._nextTimeLable.setString(formatData(a.next.awardTime, a.next.delayTimeInterval));
					b._btmLayer._curDate.setString(a.current.awardTime, cc.p(-370, -25), 24);
					b._btmLayer._curperiodNumber.setString(a.current.periodNumber, cc.p(-174, -25), 24);
					cc.MoveTo.create(17, cc.p(2250, b._winSize.height / 2));
					cc.CallFunc.create(b.endPaoDao, b);
					b._qian.runAction(cc.MoveTo.create(2, cc.p(2250, b._qian.getPosition().y)));
					b._curData = a.current;
					b._nextData = a.next
				}
			})
		},
		endPaoDao: function() {
			cc.AudioEngine.getInstance().pauseMusic();
			var a = cc.MoveTo.create(1.2, cc.p(-960, this._carMgrLayer.getPosition().y)),
				b = cc.DelayTime.create(1),
				c = cc.CallFunc.create(this.gotoResultScene, this);
			this._carMgrLayer.runAction(cc.Sequence.create(a, b, c));
			a = [];
			a = this._curData.awardNumbers.split(",");
			this._btmLayer.setCurData(a)
		},
		gotoResultScene: function() {
			this.unscheduleUpdate();
			var a = new resultScene;
			a.initReseltLayer(this._curData, this._nextData);
			cc.Director.getInstance().replaceScene(a)
		},
		update: function(a) {
			for (var b = a = 0, c = 0; c < maxCarNumber - 1; c++) {
				a = this._carMgrLayer._carArray[c]._carSpr.getPosition().x;
				for (var d = c + 1; d < maxCarNumber; d++) if (this._carMgrLayer._carArray[d]._carSpr.getPosition().x < a) {
					a = this._carMgrLayer._carArray[d]._carSpr.getPosition().x;
					var e = 0,
						f = 0,
						b = this._carMgrLayer._carArray[c];
					this._carMgrLayer._carArray[c] = this._carMgrLayer._carArray[d];
					this._carMgrLayer._carArray[d] = b;
					b = this._carNumberSpr[c];
					e = this._carNumberSpr[c].getPosition();
					f = this._carNumberSpr[d].getPosition();
					this._carNumberSpr[c] = this._carNumberSpr[d];
					this._carNumberSpr[c].setPosition(e);
					this._carNumberSpr[d] = b;
					this._carNumberSpr[d].setPosition(f)
				}
			}
		},
		adjustSizeForWindow: function() {
			var a = document.documentElement.clientWidth - document.body.clientWidth;
			document.documentElement.clientWidth < cc.originalCanvasSize.width ? cc.canvas.width = cc.originalCanvasSize.width : cc.canvas.width = document.documentElement.clientWidth - a;
			document.documentElement.clientHeight < cc.originalCanvasSize.height ? cc.canvas.height = cc.originalCanvasSize.height : cc.canvas.height = document.documentElement.clientHeight - a;
			var a = cc.canvas.width / cc.originalCanvasSize.width,
				b = cc.canvas.height / cc.originalCanvasSize.height;
			a > b && (a = b);
			cc.canvas.width = cc.originalCanvasSize.width * a;
			cc.canvas.height = cc.originalCanvasSize.height * a;
			(b = document.getElementById("Cocos2dGameContainer")) && (b.style.width = cc.canvas.width + "px", b.style.height = cc.canvas.height + "px");
			cc.renderContext.translate(0, cc.canvas.height);
			cc.renderContext.scale(a, a);
			cc.Director.getInstance().setContentScaleFactor(a)
		}
	}),
	result = cc.Layer.extend({
		isMouseDown: !1,
		midLayer: null,
		topLayer: null,
		btmLayer: null,
		initByParas: function(a, b) {
			var c = cc.Director.getInstance().getWinSize(),
				d = [],
				d = a.awardNumbers.split(",");
			this.midLayer = new MidActionLayer;
			this.midLayer.init();
			this.midLayer.setPosition(cc.p(.5 * c.width - 50, .5 * c.height));
			this.addChild(this.midLayer, 0);
			this.midLayer.initCarSprite(d);
			this.midLayer.runFirstAction();
			this.topLayer = new firstTopDisLayer;
			this.topLayer.init();
			this.topLayer.setPosition(cc.p(.5 * c.width - 75, c.height - 40 - 4));
			this.addChild(this.topLayer, 1);
			this.btmLayer = new firstBtmDisLayer;
			this.btmLayer.init();
			this.btmLayer.setPosition(cc.p(.5 * c.width - 75, 48));
			this.addChild(this.btmLayer, 1);
			this.btmLayer.setCurData(d);
			for (c = 0; c < d.length; ++c) {
				var e = cc.Sprite.create(numberIndexSrc[Number(d[c]) - 1]);
				e.setPosition(cc.p(65 * c + 170, 595));
				this.addChild(e, 4)
			}
			this.topLayer._qishuLable.setString(a.periodNumber);
			this.topLayer._nextQishuLable.setString(b.periodNumber);
			this.topLayer._nextTimeLable.setString(formatData(b.awardTime, b.delayTimeInterval));
			this.btmLayer._curDate.setString(a.awardTime, cc.p(-370, -25), 24);
			this.btmLayer._curperiodNumber.setString(a.periodNumber, cc.p(-174, -25), 24)
		},
		init: function() {
			var a = this;
			return this._super(), this.setTouchEnabled(!0), this.adjustSizeForWindow(), window.addEventListener("resize", function(b) {
				a.adjustSizeForWindow()
			}), !0
		},
		adjustSizeForWindow: function() {
			var a = document.documentElement.clientWidth - document.body.clientWidth;
			document.documentElement.clientWidth < cc.originalCanvasSize.width ? cc.canvas.width = cc.originalCanvasSize.width : cc.canvas.width = document.documentElement.clientWidth - a;
			document.documentElement.clientHeight < cc.originalCanvasSize.height ? cc.canvas.height = cc.originalCanvasSize.height : cc.canvas.height = document.documentElement.clientHeight - a;
			var a = cc.canvas.width / cc.originalCanvasSize.width,
				b = cc.canvas.height / cc.originalCanvasSize.height;
			a > b && (a = b);
			cc.canvas.width = cc.originalCanvasSize.width * a;
			cc.canvas.height = cc.originalCanvasSize.height * a;
			(b = document.getElementById("Cocos2dGameContainer")) && (b.style.width = cc.canvas.width + "px", b.style.height = cc.canvas.height + "px");
			cc.renderContext.translate(0, cc.canvas.height);
			cc.renderContext.scale(a, a);
			cc.Director.getInstance().setContentScaleFactor(a)
		},
		menuCloseCallback: function(a) {
			cc.Director.getInstance().end()
		},
		onTouchesBegan: function(a, b) {
			this.isMouseDown = !0
		},
		onTouchesMoved: function(a, b) {
			this.isMouseDown && a
		},
		onTouchesEnded: function(a, b) {
			this.isMouseDown = !1
		},
		onTouchesCancelled: function(a, b) {
			console.log("onTouchesCancelled")
		}
	}),
	resultScene = cc.Scene.extend({
		onEnter: function() {
			this._super()
		},
		initReseltLayer: function(a, b) {
			var c = new result;
			c.init();
			c.initByParas(a, b);
			this.addChild(c)
		}
	}),
	MidActionLayer = cc.Layer.extend({
		midSprite: null,
		carRankOne: null,
		carRankTwo: null,
		carRankThree: null,
		emitter: null,
		_numArray: null,
		init: function() {
			this._super();
			this._numArray = [];
			cc.Director.getInstance().getWinSize();
			this.midSprite = cc.Sprite.create(g_midImage);
			this.midSprite.setPosition(cc.p(130, 0));
			this.midSprite.setScale(1.5, 1);
			this.addChild(this.midSprite, 0);
			var a = cc.TextureCache.getInstance().addImage(g_caiDai);
			this.emitter = cc.ParticleFlower.create();
			this.emitter.setDrawMode(cc.PARTICLE_TEXTURE_MODE);
			this.emitter.setTexture(a);
			this.emitter.setLife(400);
			this.emitter.setLifeVar(0);
			this.emitter.setGravity(cc.p(0, -30));
			this.emitter.setSpeed(130);
			this.emitter.setSpeedVar(130);
			this.emitter.setPosition(cc.p(0, 220));
			this.addChild(this.emitter, 10)
		},
		runGuangAction: function(a) {
			var b = cc.Sprite.create(g_light);
			b.setPosition(a);
			this.addChild(b, 9);
			a = cc.Blink.create(2, 5);
			var c = cc.ScaleTo.create(1.8, 1.8);
			a = cc.Spawn.create(a, c);
			c = cc.Hide.create();
			a = cc.Sequence.create(a, c);
			a = cc.RepeatForever.create(a);
			b.runAction(a)
		},
		initCarSprite: function(a) {
			cc.Director.getInstance().getWinSize();
			g_carNumber = this._numArray = a;
			var b = Number(a[2]) - 1;
			this.carRankOne = cc.Sprite.create(carRankIndex[b]);
			this.carRankOne.setPosition(cc.p(320, -100));
			this.carRankOne.setScale(.5, .5);
			b = cc.Sprite.create("http://"+document.domain+"/Public/main/img/res/rank3.png");
			b.setPosition(cc.p(120, 350));
			this.carRankOne.addChild(b);
			b = Number(a[1]) - 1;
			this.carRankTwo = cc.Sprite.create(carRankIndex[b]);
			this.carRankTwo.setPosition(cc.p(-360, -120));
			this.carRankTwo.setScale(.5, .5);
			b = cc.Sprite.create("http://"+document.domain+"/Public/main/img/res/rank2.png");
			b.setPosition(cc.p(120, 350));
			this.carRankTwo.addChild(b);
			b = Number(a[0]) - 1;
			this.carRankThree = cc.Sprite.create(carRankIndex[b]);
			this.carRankThree.setPosition(cc.p(0, -120));
			this.carRankThree.setScale(.5, .5);
			a = cc.Sprite.create("http://"+document.domain+"/Public/main/img/res/rank1.png");
			a.setPosition(cc.p(120, 350));
			this.carRankThree.addChild(a)
		},
		runFirstAction: function() {
			this.addChild(this.carRankOne, 1);
			this.carRankOne.runAction(cc.Sequence.create(cc.ScaleTo.create(.3, .8), cc.CallFunc.create(this.stopFirstAction, this)))
		},
		stopFirstAction: function() {
			var a = this.carRankOne.getPosition();
			this.runGuangAction(cc.p(a.x - 65, a.y + 50));
			this.runGuangAction(cc.p(a.x + 65, a.y + 50));
			this.addChild(this.carRankTwo, 1);
			this.carRankTwo.runAction(cc.Sequence.create(cc.ScaleTo.create(.3, 1), cc.CallFunc.create(this.stopSecondAction, this)))
		},
		stopSecondAction: function() {
			var a = this.carRankTwo.getPosition();
			this.runGuangAction(cc.p(a.x - 85, a.y + 65));
			this.runGuangAction(cc.p(a.x + 85, a.y + 65));
			this.addChild(this.carRankThree, 1);
			this.carRankThree.runAction(cc.Sequence.create(cc.ScaleTo.create(.3, 1.2), cc.CallFunc.create(this.stopThirdAction, this)))
		},
		stopThirdAction: function() {
			var a = this.carRankThree.getPosition();
			this.runGuangAction(cc.p(a.x - 98, a.y + 72));
			this.runGuangAction(cc.p(a.x + 98, a.y + 72));
			this.scheduleOnce(this.gotoMainScene, 15)
		},
		gotoMainScene: function() {
			g_isAddMaskSpr = !1;
			var a = new pk10Scene;
			cc.Director.getInstance().replaceScene(a);
			"function" == typeof afterEndRunning && afterEndRunning()
		}
	});
cc.ParticleFlower = cc.ParticleSystemQuad.extend({
	init: function() {
		return this.initWithTotalParticles(300)
	},
	initWithTotalParticles: function(a) {
		return this._super(a) ? (this._duration = cc.PARTICLE_DURATION_INFINITY, this._emitterMode = cc.PARTICLE_MODE_GRAVITY, this.modeA.gravity = cc.p(0, -1), this.modeA.speed = 5, this.modeA.speedVar = 1, this.modeA.radialAccel = 0, this.modeA.radialAccelVar = 1, this.modeA.tangentialAccel = 0, this.modeA.tangentialAccelVar = 1, a = cc.Director.getInstance().getWinSize(), this.setPosition(cc.p(a.width / 2, a.height + 10)), this._posVar = cc.p(400, 0), this._angle = -90, this._angleVar = 5, this._life = 45, this._lifeVar = 0, this._startSize = 30, this._startSizeVar = 5, this._endSize = cc.PARTICLE_START_SIZE_EQUAL_TO_END_SIZE, this._emissionRate = 10, this._startColor.r = .5, this._startColor.g = .5, this._startColor.b = .5, this._startColor.a = 1, this._startColorVar.r = .5, this._startColorVar.g = .5, this._startColorVar.b = .5, this._startColorVar.a = 1, this.setBlendAdditive(!1), !0) : !1
	}
});
cc.ParticleFlower.create = function() {
	var a = new cc.ParticleFlower;
	return a.init() ? a : null
};
var ParticleDemo = cc.LayerGradient.extend({
	_emitter: null,
	_background: null,
	setColor: function() {},
	ctor: function() {
		this._super();
		cc.associateWithNative(this, cc.LayerGradient);
		this._emitter = null;
		this._background = new result;
		this._background.init();
		this.addChild(this._background)
	},
	onEnter: function() {
		this._super()
	},
	setEmitterPosition: function() {
		this._emitter.setPosition(500, 500)
	}
}),
	DemoFlower = ParticleDemo.extend({
		onEnter: function() {
			this._super();
			this._emitter = cc.ParticleFlower.create();
			this._emitter.setLife(5);
			this._emitter.setLifeVar(0);
			this._emitter.setGravity(cc.p(0, -30));
			this._emitter.setSpeed(15);
			this._emitter.setSpeedVar(5);
			this._emitter.setEndSpin(960);
			this._emitter.setEndSpinVar(320);
			var a = this._emitter.getStartColor();
			a.r = .9;
			a.g = .9;
			a.b = .9;
			this._emitter.setStartColor(a);
			a = this._emitter.getStartColorVar();
			a.b = .1;
			this._emitter.setStartColorVar(a);
			this._emitter.setEmissionRate(this._emitter.getTotalParticles() / this._emitter.getLife());
			a = cc.TextureCache.getInstance().addImage("http://"+document.domain+"/Public/main/img/res/ffff.png");
			this._emitter.setTexture(a);
			this._emitter.setDrawMode(1);
			this.addChild(this._emitter);
			this.setEmitterPosition()
		}
	});