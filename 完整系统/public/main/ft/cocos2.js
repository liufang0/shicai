cc.ACTION_TAG_INVALID = -1, cc.Action = cc.Class.extend({
	_originalTarget: null,
	_target: null,
	_tag: cc.ACTION_TAG_INVALID,
	description: function() {
		return "<cc.Action | Tag = " + this._tag + ">"
	},
	copyWithZone: function(e) {
		return this.copy()
	},
	copy: function() {
		return cc.clone(this)
	},
	isDone: function() {
		return !0
	},
	startWithTarget: function(e) {
		this._originalTarget = e, this._target = e
	},
	stop: function() {
		this._target = null
	},
	step: function(e) {
		cc.log("[Action step]. override me")
	},
	update: function(e) {
		cc.log("[Action update]. override me")
	},
	getTarget: function() {
		return this._target
	},
	setTarget: function(e) {
		this._target = e
	},
	getOriginalTarget: function() {
		return this._originalTarget
	},
	setOriginalTarget: function(e) {
		this._originalTarget = e
	},
	getTag: function() {
		return this._tag
	},
	setTag: function(e) {
		this._tag = e
	},
	retain: function() {},
	release: function() {}
}), cc.Action.create = function() {
	return new cc.Action()
}, cc.FiniteTimeAction = cc.Action.extend({
	_duration: 0,
	getDuration: function() {
		return this._duration
	},
	setDuration: function(e) {
		this._duration = e
	},
	reverse: function() {
		return cc.log("cocos2d: FiniteTimeAction#reverse: Implement me"), null
	}
}), cc.Speed = cc.Action.extend({
	_speed: 0,
	_innerAction: null,
	getSpeed: function() {
		return this._speed
	},
	setSpeed: function(e) {
		this._speed = e
	},
	initWithAction: function(e, t) {
		return cc.Assert(e != null, ""), this._innerAction = e, this._speed = t, !0
	},
	startWithTarget: function(e) {
		cc.Action.prototype.startWithTarget.call(this, e), this._innerAction.startWithTarget(e)
	},
	stop: function() {
		this._innerAction.stop(), cc.Action.prototype.stop.call(this)
	},
	step: function(e) {
		this._innerAction.step(e * this._speed)
	},
	isDone: function() {
		return this._innerAction.isDone()
	},
	reverse: function() {
		return cc.Speed.create(this._innerAction.reverse(), this._speed)
	},
	setInnerAction: function(e) {
		this._innerAction != e && (this._innerAction = e)
	},
	getInnerAction: function() {
		return this._innerAction
	}
}), cc.Speed.create = function(e, t) {
	var n = new cc.Speed();
	return n && n.initWithAction(e, t) ? n : null
}, cc.Follow = cc.Action.extend({
	isBoundarySet: function() {
		return this._boundarySet
	},
	setBoudarySet: function(e) {
		this._boundarySet = e
	},
	initWithTarget: function(e, t) {
		cc.Assert(e != null, ""), t = t || cc.RectZero(), this._followedNode = e, this._boundarySet = !cc.Rect.CCRectEqualToRect(t, cc.RectZero()), this._boundaryFullyCovered = !1;
		var n = cc.Director.getInstance().getWinSize();
		return this._fullScreenSize = cc.p(n.width, n.height), this._halfScreenSize = cc.pMult(this._fullScreenSize, .5), this._boundarySet && (this.leftBoundary = -(t.origin.x + t.size.width - this._fullScreenSize.x), this.rightBoundary = -t.origin.x, this.topBoundary = -t.origin.y, this.bottomBoundary = -(t.origin.y + t.size.height - this._fullScreenSize.y), this.rightBoundary < this.leftBoundary && (this.rightBoundary = this.leftBoundary = (this.leftBoundary + this.rightBoundary) / 2), this.topBoundary < this.bottomBoundary && (this.topBoundary = this.bottomBoundary = (this.topBoundary + this.bottomBoundary) / 2), this.topBoundary == this.bottomBoundary && this.leftBoundary == this.rightBoundary && (this._boundaryFullyCovered = !0)), !0
	},
	step: function(e) {
		if (this._boundarySet) {
			if (this._boundaryFullyCovered) return;
			var t = cc.pSub(this._halfScreenSize, this._followedNode.getPosition());
			this._target.setPosition(cc.p(cc.clampf(t.x, this.leftBoundary, this.rightBoundary), cc.clampf(t.y, this.bottomBoundary, this.topBoundary)))
		} else this._target.setPosition(cc.pSub(this._halfScreenSize, this._followedNode.getPosition()))
	},
	isDone: function() {
		return !this._followedNode.isRunning()
	},
	stop: function() {
		this._target = null, cc.Action.prototype.stop.call(this)
	},
	_followedNode: null,
	_boundarySet: !1,
	_boundaryFullyCovered: !1,
	_halfScreenSize: null,
	_fullScreenSize: null,
	leftBoundary: 0,
	rightBoundary: 0,
	topBoundary: 0,
	bottomBoundary: 0
}), cc.Follow.create = function(e, t) {
	t = t || new cc.RectZero();
	var n = new cc.Follow();
	return t != null && n && n.initWithTarget(e, t) ? n : n && n.initWithTarget(e) ? n : null
};
cc.ActionInterval = cc.FiniteTimeAction.extend({
	_elapsed: 0,
	_firstTick: !1,
	getElapsed: function() {
		return this._elapsed
	},
	initWithDuration: function(e) {
		return this._duration = e == 0 ? cc.FLT_EPSILON : e, this._elapsed = 0, this._firstTick = !0, !0
	},
	isDone: function() {
		return this._elapsed >= this._duration
	},
	step: function(e) {
		this._firstTick ? (this._firstTick = !1, this._elapsed = 0) : this._elapsed += e;
		var t = this._elapsed / (this._duration > 1.192092896e-7 ? this._duration : 1.192092896e-7);
		t = 1 > t ? t : 1, this.update(t > 0 ? t : 0)
	},
	startWithTarget: function(e) {
		cc.Action.prototype.startWithTarget.call(this, e), this._elapsed = 0, this._firstTick = !0
	},
	reverse: function() {
		return cc.Assert(!1, "cc.IntervalAction: reverse not implemented."), null
	},
	setAmplitudeRate: function(e) {
		cc.Assert(0, "Actioninterval setAmplitudeRate")
	},
	getAmplitudeRate: function() {
		return cc.Assert(0, "Actioninterval getAmplitudeRate"), 0
	}
}), cc.ActionInterval.create = function(e) {
	var t = new cc.ActionInterval();
	return t.initWithDuration(e), t
}, cc.Sequence = cc.ActionInterval.extend({
	_actions: null,
	_split: null,
	_last: 0,
	ctor: function() {
		this._actions = []
	},
	initOneTwo: function(e, t) {
		cc.Assert(e != null, "Sequence.initOneTwo"), cc.Assert(t != null, "Sequence.initOneTwo");
		var n = e.getDuration(),
			r = t.getDuration(),
			i = e.getDuration() + t.getDuration();
		return this.initWithDuration(i), this._actions[0] = e, this._actions[1] = t, !0
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._split = this._actions[0].getDuration() / this._duration, this._last = -1
	},
	stop: function() {
		this._last != -1 && this._actions[this._last].stop(), cc.Action.prototype.stop.call(this)
	},
	update: function(e) {
		var t, n = 0;
		e < this._split ? t = this._split ? e / this._split : 1 : (n = 1, t = this._split == 1 ? 1 : (e - this._split) / (1 - this._split), this._last == -1 && (this._actions[0].startWithTarget(this._target), this._actions[0].update(1), this._actions[0].stop()), this._last || (this._actions[0].update(1), this._actions[0].stop()));
		if (this._last == n && this._actions[n].isDone()) return;
		this._last != n && this._actions[n].startWithTarget(this._target), this._actions[n].update(t), this._last = n
	},
	reverse: function() {
		return cc.Sequence._actionOneTwo(this._actions[1].reverse(), this._actions[0].reverse())
	},
	copy: function() {
		return cc.Sequence._actionOneTwo(this._actions[0].copy(), this._actions[1].copy())
	}
}), cc.Sequence.create = function(e) {
	var t = e instanceof Array ? e : arguments,
		n = t[0];
	for (var r = 1; r < t.length; r++) t[r] && (n = cc.Sequence._actionOneTwo(n, t[r]));
	return n
}, cc.Sequence._actionOneTwo = function(e, t) {
	var n = new cc.Sequence();
	return n.initOneTwo(e, t), n
}, cc.Repeat = cc.ActionInterval.extend({
	_times: 0,
	_total: 0,
	_nextDt: 0,
	_actionInstant: !1,
	_innerAction: null,
	initWithAction: function(e, t) {
		var n = e.getDuration() * t;
		return this.initWithDuration(n) ? (this._times = t, this._innerAction = e, e instanceof cc.ActionInstant && (this._times -= 1), this._total = 0, !0) : !1
	},
	startWithTarget: function(e) {
		this._total = 0, this._nextDt = this._innerAction.getDuration() / this._duration, cc.ActionInterval.prototype.startWithTarget.call(this, e), this._innerAction.startWithTarget(e)
	},
	stop: function() {
		this._innerAction.stop(), cc.Action.prototype.stop.call(this)
	},
	update: function(e) {
		if (e >= this._nextDt) {
			while (e > this._nextDt && this._total < this._times) this._innerAction.update(1), this._total++, this._innerAction.stop(), this._innerAction.startWithTarget(this._target), this._nextDt += this._innerAction.getDuration() / this._duration;
			e >= 1 && this._total < this._times && this._total++, this._actionInstant && (this._total == this._times ? (this._innerAction.update(1), this._innerAction.stop()) : this._innerAction.update(e - (this._nextDt - this._innerAction.getDuration() / this._duration)))
		} else this._innerAction.update(e * this._times % 1)
	},
	isDone: function() {
		return this._total == this._times
	},
	reverse: function() {
		return cc.Repeat.create(this._innerAction.reverse(), this._times)
	},
	setInnerAction: function(e) {
		this._innerAction != e && (this._innerAction = e)
	},
	getInnerAction: function() {
		return this._innerAction
	}
}), cc.Repeat.create = function(e, t) {
	var n = new cc.Repeat();
	return n.initWithAction(e, t), n
}, cc.RepeatForever = cc.ActionInterval.extend({
	_innerAction: null,
	initWithAction: function(e) {
		return cc.Assert(e != null, ""), this._innerAction = e, !0
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._innerAction.startWithTarget(e)
	},
	step: function(e) {
		this._innerAction.step(e), this._innerAction.isDone() && (this._innerAction.startWithTarget(this._target), this._innerAction.step(this._innerAction.getElapsed() - this._innerAction.getDuration()))
	},
	isDone: function() {
		return !1
	},
	reverse: function() {
		return cc.RepeatForever.create(this._innerAction.reverse())
	},
	setInnerAction: function(e) {
		this._innerAction != e && (this._innerAction = e)
	},
	getInnerAction: function() {
		return this._innerAction
	}
}), cc.RepeatForever.create = function(e) {
	var t = new cc.RepeatForever();
	return t && t.initWithAction(e) ? t : null
}, cc.Spawn = cc.ActionInterval.extend({
	initOneTwo: function(e, t) {
		cc.Assert(e != null, "no action1"), cc.Assert(t != null, "no action2");
		var n = !1,
			r = e.getDuration(),
			i = t.getDuration();
		return this.initWithDuration(Math.max(r, i)) && (this._one = e, this._two = t, r > i ? this._two = cc.Sequence._actionOneTwo(t, cc.DelayTime.create(r - i)) : r < i && (this._one = cc.Sequence._actionOneTwo(e, cc.DelayTime.create(i - r))), n = !0), n
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._one.startWithTarget(e), this._two.startWithTarget(e)
	},
	stop: function() {
		this._one.stop(), this._two.stop(), cc.Action.prototype.stop.call(this)
	},
	update: function(e) {
		this._one && this._one.update(e), this._two && this._two.update(e)
	},
	reverse: function() {
		return cc.Spawn._actionOneTwo(this._one.reverse(), this._two.reverse())
	},
	_one: null,
	_two: null
}), cc.Spawn.create = function(e) {
	var t = e instanceof Array ? e : arguments,
		n = t[0];
	for (var r = 1; r < t.length; r++) t[r] != null && (n = this._actionOneTwo(n, t[r]));
	return n
}, cc.Spawn._actionOneTwo = function(e, t) {
	var n = new cc.Spawn();
	return n.initOneTwo(e, t), n
}, cc.RotateTo = cc.ActionInterval.extend({
	_dstAngle: 0,
	_startAngle: 0,
	_diffAngle: 0,
	initWithDuration: function(e, t) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._dstAngle = t || 0, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._startAngle = e.getRotation(), this._startAngle > 0 ? this._startAngle = this._startAngle % 360 : this._startAngle = this._startAngle % 360, this._diffAngle = this._dstAngle - this._startAngle, this._diffAngle > 180 && (this._diffAngle -= 360), this._diffAngle < -180 && (this._diffAngle += 360)
	},
	reverse: function() {
		cc.Assert(0, "RotateTo reverse not implemented")
	},
	update: function(e) {
		this._target && this._target.setRotation(this._startAngle + this._diffAngle * e)
	}
}), cc.RotateTo.create = function(e, t) {
	var n = new cc.RotateTo();
	return n.initWithDuration(e, t), n
}, cc.RotateBy = cc.ActionInterval.extend({
	_angle: 0,
	_startAngle: 0,
	initWithDuration: function(e, t) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._angle = t, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._startAngle = e.getRotation()
	},
	update: function(e) {
		this._target && this._target.setRotation(this._startAngle + this._angle * e)
	},
	reverse: function() {
		return cc.RotateBy.create(this._duration, -this._angle)
	}
}), cc.RotateBy.create = function(e, t) {
	var n = new cc.RotateBy();
	return n.initWithDuration(e, t), n
}, cc.MoveTo = cc.ActionInterval.extend({
	initWithDuration: function(e, t) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._endPosition = t, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._previousPosition = this._startPosition = e.getPosition(), this._delta = cc.pSub(this._endPosition, this._startPosition)
	},
	update: function(e) {
		if (this._target) {
			var t = this._target.getPosition(),
				n = cc.pSub(t, this._previousPosition);
			this._startPosition = cc.pAdd(this._startPosition, n);
			var r = cc.p(this._startPosition.x + this._delta.x * e, this._startPosition.y + this._delta.y * e);
			this._target.setPosition(r), this._previousPosition = r
		}
	},
	reverse: function() {
		cc.Assert(0, "moveto reverse is not implemented")
	},
	_endPosition: cc.p(0, 0),
	_startPosition: cc.p(0, 0),
	_delta: cc.p(0, 0)
}), cc.MoveTo.create = function(e, t) {
	var n = new cc.MoveTo();
	return n.initWithDuration(e, t), n
}, cc.MoveBy = cc.MoveTo.extend({
	initWithDuration: function(e, t) {
		return cc.MoveTo.prototype.initWithDuration.call(this, e, t) ? (this._delta = t, !0) : !1
	},
	startWithTarget: function(e) {
		var t = this._delta;
		cc.MoveTo.prototype.startWithTarget.call(this, e), this._delta = t
	},
	reverse: function() {
		return cc.MoveBy.create(this._duration, cc.p(-this._delta.x, -this._delta.y))
	}
}), cc.MoveBy.create = function(e, t) {
	var n = new cc.MoveBy();
	return n.initWithDuration(e, t), n
}, cc.SkewTo = cc.ActionInterval.extend({
	initWithDuration: function(e, t, n) {
		var r = !1;
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) && (this._endSkewX = t, this._endSkewY = n, r = !0), r
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._startSkewX = e.getSkewX(), this._startSkewX > 0 ? this._startSkewX = this._startSkewX % 180 : this._startSkewX = this._startSkewX % -180, this._deltaX = this._endSkewX - this._startSkewX, this._deltaX > 180 && (this._deltaX -= 360), this._deltaX < -180 && (this._deltaX += 360), this._startSkewY = e.getSkewY(), this._startSkewY > 0 ? this._startSkewY = this._startSkewY % 360 : this._startSkewY = this._startSkewY % -360, this._deltaY = this._endSkewY - this._startSkewY, this._deltaY > 180 && (this._deltaY -= 360), this._deltaY < -180 && (this._deltaY += 360)
	},
	update: function(e) {
		this._target.setSkewX(this._startSkewX + this._deltaX * e), this._target.setSkewY(this._startSkewY + this._deltaY * e)
	},
	_skewX: 0,
	_skewY: 0,
	_startSkewX: 0,
	_startSkewY: 0,
	_endSkewX: 0,
	_endSkewY: 0,
	_deltaX: 0,
	_deltaY: 0
}), cc.SkewTo.create = function(e, t, n) {
	var r = new cc.SkewTo();
	return r && r.initWithDuration(e, t, n), r
}, cc.SkewBy = cc.SkewTo.extend({
	initWithDuration: function(e, t, n) {
		var r = !1;
		return cc.SkewTo.prototype.initWithDuration.call(this, e, t, n) && (this._skewX = t, this._skewY = n, r = !0), r
	},
	startWithTarget: function(e) {
		cc.SkewTo.prototype.startWithTarget.call(this, e), this._deltaX = this._skewX, this._deltaY = this._skewY, this._endSkewX = this._startSkewX + this._deltaX, this._endSkewY = this._startSkewY + this._deltaY
	},
	reverse: function() {
		return cc.SkewBy.create(this._duration, -this._skewX, -this._skewY)
	}
}), cc.SkewBy.create = function(e, t, n) {
	var r = new cc.SkewBy();
	return r && r.initWithDuration(e, t, n), r
}, cc.JumpBy = cc.ActionInterval.extend({
	initWithDuration: function(e, t, n, r) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._delta = t, this._height = n, this._jumps = r, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._previousPosition = this._startPosition = e.getPosition()
	},
	update: function(e) {
		if (this._target) {
			var t = e * this._jumps % 1,
				n = this._height * 4 * t * (1 - t);
			n += this._delta.y * e;
			var r = this._delta.x * e,
				i = this._target.getPosition(),
				s = cc.pSub(i, this._previousPosition);
			this._startPosition = cc.pAdd(s, this._startPosition);
			var o = cc.pAdd(this._startPosition, cc.p(r, n));
			this._target.setPosition(o), this._previousPosition = o
		}
	},
	reverse: function() {
		return cc.JumpBy.create(this._duration, cc.p(-this._delta.x, -this._delta.y), this._height, this._jumps)
	},
	_startPosition: cc.p(0, 0),
	_delta: cc.p(0, 0),
	_height: 0,
	_jumps: 0
}), cc.JumpBy.create = function(e, t, n, r) {
	var i = new cc.JumpBy();
	return i.initWithDuration(e, t, n, r), i
}, cc.JumpTo = cc.JumpBy.extend({
	startWithTarget: function(e) {
		cc.JumpBy.prototype.startWithTarget.call(this, e), this._delta = cc.p(this._delta.x - this._startPosition.x, this._delta.y - this._startPosition.y)
	}
}), cc.JumpTo.create = function(e, t, n, r) {
	var i = new cc.JumpTo();
	return i.initWithDuration(e, t, n, r), i
}, cc.bezierat = function(e, t, n, r, i) {
	return Math.pow(1 - i, 3) * e + 3 * i * Math.pow(1 - i, 2) * t + 3 * Math.pow(i, 2) * (1 - i) * n + Math.pow(i, 3) * r
}, cc.BezierBy = cc.ActionInterval.extend({
	initWithDuration: function(e, t) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._config = t, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._previousPosition = this._startPosition = e.getPosition()
	},
	update: function(e) {
		if (this._target) {
			var t = 0,
				n = this._config[0].x,
				r = this._config[1].x,
				i = this._config[2].x,
				s = 0,
				o = this._config[0].y,
				u = this._config[1].y,
				a = this._config[2].y,
				f = cc.bezierat(t, n, r, i, e),
				l = cc.bezierat(s, o, u, a, e),
				c = this._target.getPosition(),
				h = cc.pSub(c, this._previousPosition);
			this._startPosition = cc.pAdd(this._startPosition, h);
			var p = cc.pAdd(this._startPosition, cc.p(f, l));
			this._target.setPosition(p), this._previousPosition = p
		}
	},
	reverse: function() {
		var e = [cc.pAdd(this._config[1], cc.pNeg(this._config[2])), cc.pAdd(this._config[0], cc.pNeg(this._config[2])), cc.pNeg(this._config[2])];
		return cc.BezierBy.create(this._duration, e)
	},
	ctor: function() {
		this._config = [], this._startPosition = cc.p(0, 0)
	}
}), cc.BezierBy.create = function(e, t) {
	var n = new cc.BezierBy();
	return n.initWithDuration(e, t), n
}, cc.BezierTo = cc.BezierBy.extend({
	_toConfig: null,
	initWithDuration: function(e, t) {
		return cc.BezierBy.prototype.initWithDuration.call(this, e, t) ? (this._toConfig = [], this._toConfig[0] = cc.p(t[0].x, t[0].y), this._toConfig[1] = cc.p(t[1].x, t[1].y), this._toConfig[2] = cc.p(t[2].x, t[2].y), !0) : !1
	},
	startWithTarget: function(e) {
		cc.BezierBy.prototype.startWithTarget.call(this, e), this._config[0] = cc.pSub(this._toConfig[0], this._startPosition), this._config[1] = cc.pSub(this._toConfig[1], this._startPosition), this._config[2] = cc.pSub(this._toConfig[2], this._startPosition)
	}
}), cc.BezierTo.create = function(e, t) {
	var n = new cc.BezierTo();
	return n.initWithDuration(e, t), n
}, cc.ScaleTo = cc.ActionInterval.extend({
	initWithDuration: function(e, t, n) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._endScaleX = t, this._endScaleY = n != null ? n : t, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._startScaleX = e.getScaleX(), this._startScaleY = e.getScaleY(), this._deltaX = this._endScaleX - this._startScaleX, this._deltaY = this._endScaleY - this._startScaleY
	},
	update: function(e) {
		this._target && this._target.setScale(this._startScaleX + this._deltaX * e, this._startScaleY + this._deltaY * e)
	},
	_scaleX: 1,
	_scaleY: 1,
	_startScaleX: 1,
	_startScaleY: 1,
	_endScaleX: 0,
	_endScaleY: 0,
	_deltaX: 0,
	_deltaY: 0
}), cc.ScaleTo.create = function(e, t, n) {
	var r = new cc.ScaleTo();
	return n ? r.initWithDuration(e, t, n) : r.initWithDuration(e, t), r
}, cc.ScaleBy = cc.ScaleTo.extend({
	startWithTarget: function(e) {
		cc.ScaleTo.prototype.startWithTarget.call(this, e), this._deltaX = this._startScaleX * this._endScaleX - this._startScaleX, this._deltaY = this._startScaleY * this._endScaleY - this._startScaleY
	},
	reverse: function() {
		return cc.ScaleBy.create(this._duration, 1 / this._endScaleX, 1 / this._endScaleY)
	}
}), cc.ScaleBy.create = function(e, t, n) {
	var r = new cc.ScaleBy();
	return arguments.length == 3 ? r.initWithDuration(e, t, n) : r.initWithDuration(e, t), r
}, cc.Blink = cc.ActionInterval.extend({
	initWithDuration: function(e, t) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._times = t, !0) : !1
	},
	update: function(e) {
		if (this._target && !this.isDone()) {
			var t = 1 / this._times,
				n = e % t;
			this._target.setVisible(n > t / 2 ? !0 : !1)
		}
	},
	startWithTarget: function(e) {
		this._super(e), this._originalState = e.isVisible()
	},
	stop: function() {
		this._target.setVisible(this._originalState), this._super()
	},
	reverse: function() {
		return cc.Blink.create(this._duration, this._times)
	},
	_times: 0,
	_originalState: !1
}), cc.Blink.create = function(e, t) {
	var n = new cc.Blink();
	return n.initWithDuration(e, t), n
}, cc.FadeIn = cc.ActionInterval.extend({
	update: function(e) {
		this._target.setOpacity(255 * e)
	},
	reverse: function() {
		return cc.FadeOut.create(this._duration)
	}
}), cc.FadeIn.create = function(e) {
	var t = new cc.FadeIn();
	return t.initWithDuration(e), t
}, cc.FadeOut = cc.ActionInterval.extend({
	update: function(e) {
		this._target.setOpacity(255 * (1 - e))
	},
	reverse: function() {
		return cc.FadeIn.create(this._duration)
	}
}), cc.FadeOut.create = function(e) {
	var t = new cc.FadeOut();
	return t.initWithDuration(e), t
}, cc.FadeTo = cc.ActionInterval.extend({
	initWithDuration: function(e, t) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._toOpacity = t, !0) : !1
	},
	update: function(e) {
		this._target.setOpacity(this._fromOpacity + (this._toOpacity - this._fromOpacity) * e)
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._fromOpacity = e.getOpacity()
	},
	_toOpacity: "",
	_fromOpacity: ""
}), cc.FadeTo.create = function(e, t) {
	var n = new cc.FadeTo();
	return n.initWithDuration(e, t), n
}, cc.TintTo = cc.ActionInterval.extend({
	initWithDuration: function(e, t, n, r) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._to = cc.c3b(t, n, r), !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._from = this._target.getColor()
	},
	update: function(e) {
		this._target.setColor(cc.c3b(this._from.r + (this._to.r - this._from.r) * e, this._from.g + (this._to.g - this._from.g) * e, this._from.b + (this._to.b - this._from.b) * e))
	},
	_to: new cc.Color3B(),
	_from: new cc.Color3B()
}), cc.TintTo.create = function(e, t, n, r) {
	var i = new cc.TintTo();
	return i.initWithDuration(e, t, n, r), i
}, cc.TintBy = cc.ActionInterval.extend({
	initWithDuration: function(e, t, n, r) {
		return cc.ActionInterval.prototype.initWithDuration.call(this, e) ? (this._deltaR = t, this._deltaG = n, this._deltaB = r, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e);
		if (e.RGBAProtocol) {
			var t = e.getColor();
			this._fromR = t.r, this._fromG = t.g, this._fromB = t.b
		}
	},
	update: function(e) {
		this._target.RGBAProtocol && this._target.setColor(cc.c3b(this._fromR + this._deltaR * e, this._fromG + this._deltaG * e, this._fromB + this._deltaB * e))
	},
	reverse: function() {
		return cc.TintBy.create(this._duration, -this._deltaR, -this._deltaG, -this._deltaB)
	},
	_deltaR: 0,
	_deltaG: 0,
	_deltaB: 0,
	_fromR: 0,
	_fromG: 0,
	_fromB: 0
}), cc.TintBy.create = function(e, t, n, r) {
	var i = new cc.TintBy();
	return i.initWithDuration(e, t, n, r), i
}, cc.DelayTime = cc.ActionInterval.extend({
	update: function(e) {},
	reverse: function() {
		return cc.DelayTime.create(this._duration)
	}
}), cc.DelayTime.create = function(e) {
	var t = new cc.DelayTime();
	return t.initWithDuration(e), t
}, cc.ReverseTime = cc.ActionInterval.extend({
	initWithAction: function(e) {
		return cc.Assert(e != null, ""), cc.Assert(e != this._other, ""), cc.ActionInterval.prototype.initWithDuration.call(this, e.getDuration()) ? (this._other = e, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._other.startWithTarget(e)
	},
	update: function(e) {
		this._other && this._other.update(1 - e)
	},
	reverse: function() {
		return this._other.copy()
	},
	stop: function() {
		this._other.stop(), cc.Action.prototype.stop.call(this)
	},
	_other: null
}), cc.ReverseTime.create = function(e) {
	var t = new cc.ReverseTime();
	return t.initWithAction(e), t
}, cc.Animate = cc.ActionInterval.extend({
	_animation: null,
	_nextFrame: 0,
	_origFrame: null,
	_executedLoops: 0,
	_splitTimes: null,
	getAnimation: function() {
		return this._animation
	},
	setAnimation: function(e) {
		this._animation = e
	},
	initWithAnimation: function(e) {
		cc.Assert(e != null, "Animate: argument Animation must be non-NULL");
		var t = e.getDuration();
		if (this.initWithDuration(t * e.getLoops())) {
			this._nextFrame = 0, this.setAnimation(e), this._origFrame = null, this._executedLoops = 0, this._splitTimes = [];
			var n = 0,
				r = t / e.getTotalDelayUnits(),
				i = e.getFrames();
			cc.ArrayVerifyType(i, cc.AnimationFrame);
			for (var s = 0; s < i.length; s++) {
				var o = i[s],
					u = n * r / t;
				n += o.getDelayUnits(), this._splitTimes.push(u)
			}
			return !0
		}
		return !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._animation.getRestoreOriginalFrame() && (this._origFrame = e.displayFrame()), this._nextFrame = 0, this._executedLoops = 0
	},
	update: function(e) {
		if (e < 1) {
			e *= this._animation.getLoops();
			var t = 0 | e;
			t > this._executedLoops && (this._nextFrame = 0, this._executedLoops++), e %= 1
		}
		var n = this._animation.getFrames(),
			r = n.length;
		for (var i = this._nextFrame; i < r; i++) if (this._splitTimes[i] <= e) {
			this._target.setDisplayFrame(n[i].getSpriteFrame()), this._nextFrame = i + 1;
			break
		}
	},
	reverse: function() {
		var e = this._animation.getFrames(),
			t = [];
		cc.ArrayVerifyType(e, cc.AnimationFrame);
		if (e.length > 0) for (var n = e.length - 1; n >= 0; n--) {
			var r = e[n];
			if (!r) break;
			t.push(r.copy())
		}
		var i = cc.Animation.createWithAnimationFrames(t, this._animation.getDelayPerUnit(), this._animation.getLoops());
		return i.setRestoreOriginalFrame(this._animation.getRestoreOriginalFrame()), cc.Animate.create(i)
	},
	stop: function() {
		this._animation.getRestoreOriginalFrame() && this._target && this._target.setDisplayFrame(this._origFrame), cc.Action.prototype.stop.call(this)
	}
}), cc.Animate.create = function(e) {
	var t = new cc.Animate();
	return t.initWithAnimation(e), t
}, cc.TargetedAction = cc.ActionInterval.extend({
	_action: null,
	_forcedTarget: null,
	initWithTarget: function(e, t) {
		return this.initWithDuration(t.getDuration()) ? (this._forcedTarget = e, this._action = t, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, this._forcedTarget), this._action.startWithTarget(this._forcedTarget)
	},
	stop: function() {
		this._action.stop()
	},
	update: function(e) {
		this._action.update(e)
	},
	getForcedTarget: function() {
		return this._forcedTarget
	},
	setForcedTarget: function(e) {
		this._forcedTarget != e && (this._forcedTarget = e)
	}
}), cc.TargetedAction.create = function(e, t) {
	var n = new cc.TargetedAction();
	return n.initWithTarget(e, t), n
};
cc.ActionInstant = cc.FiniteTimeAction.extend({
	isDone: function() {
		return !0
	},
	step: function(e) {
		this.update(1)
	},
	update: function(e) {}
}), cc.Show = cc.ActionInstant.extend({
	update: function(e) {
		this._target.setVisible(!0)
	},
	reverse: function() {
		return cc.Hide.create.call(this)
	}
}), cc.Show.create = function() {
	return new cc.Show()
}, cc.Hide = cc.ActionInstant.extend({
	update: function(e) {
		this._target.setVisible(!1)
	},
	reverse: function() {
		return cc.Show.create.call(this)
	}
}), cc.Hide.create = function() {
	return new cc.Hide()
}, cc.ToggleVisibility = cc.ActionInstant.extend({
	update: function(e) {
		this._target.setVisible(!this._target.isVisible())
	},
	reverse: function() {
		return new cc.ToggleVisibility()
	}
}), cc.ToggleVisibility.create = function() {
	return new cc.ToggleVisibility()
}, cc.FlipX = cc.ActionInstant.extend({
	initWithFlipX: function(e) {
		return this._flipX = e, !0
	},
	update: function(e) {
		this._target.setFlipX(this._flipX)
	},
	reverse: function() {
		return cc.FlipX.create(!this._flipX)
	},
	_flipX: !1
}), cc.FlipX.create = function(e) {
	var t = new cc.FlipX();
	return t.initWithFlipX(e) ? t : null
}, cc.FlipY = cc.ActionInstant.extend({
	initWithFlipY: function(e) {
		return this._flipY = e, !0
	},
	update: function(e) {
		this._target.setFlipY(this._flipY)
	},
	reverse: function() {
		return cc.FlipY.create(!this._flipY)
	},
	_flipY: !1
}), cc.FlipY.create = function(e) {
	var t = new cc.FlipY();
	return t.initWithFlipY(e) ? t : null
}, cc.Place = cc.ActionInstant.extend({
	initWithPosition: function(e) {
		return this._position = e, !0
	},
	update: function(e) {
		this._target.setPosition(this._position)
	}
}), cc.Place.create = function(e) {
	var t = new cc.Place();
	return t.initWithPosition(e), t
}, cc.CallFunc = cc.ActionInstant.extend({
	initWithTarget: function(e, t, n) {
		return this._data = n, this._callFunc = e, this._selectorTarget = t, !0
	},
	execute: function() {
		this._callFunc != null && this._callFunc.call(this._selectorTarget, this._target, this._data)
	},
	update: function(e) {
		this.execute()
	},
	getTargetCallback: function() {
		return this._selectorTarget
	},
	setTargetCallback: function(e) {
		e != this._selectorTarget && (this._selectorTarget && (this._selectorTarget = null), this._selectorTarget = e)
	},
	copy: function() {
		var e = new cc.CallFunc();
		return e.initWithTarget(this._callFunc, this._selectorTarget, this._data), e
	},
	_selectorTarget: null,
	_callFunc: null
}), cc.CallFunc.create = function(e, t, n) {
	var r = new cc.CallFunc();
	return r && r.initWithTarget(e, t, n) ? (r._callFunc = e, r) : null
};
cc.HashElement = cc.Class.extend({
	actions: null,
	target: null,
	actionIndex: 0,
	currentAction: null,
	currentActionSalvaged: !1,
	paused: !1,
	hh: null,
	ctor: function() {
		this.actions = []
	}
}), cc.ActionManager = cc.Class.extend({
	_targets: null,
	_currentTarget: null,
	_currentTargetSalvaged: !1,
	_searchElementByTarget: function(e, t) {
		for (var n = 0; n < e.length; n++) if (t == e[n].target) return e[n];
		return null
	},
	ctor: function() {
		this._targets = []
	},
	addAction: function(e, t, n) {
		cc.Assert(e != null, "no action"), cc.Assert(t != null, "");
		var r = this._searchElementByTarget(this._targets, t);
		r || (r = new cc.HashElement(), r.paused = n, r.target = t, this._targets.push(r)), this._actionAllocWithHashElement(r), r.actions.push(e), e.startWithTarget(t)
	},
	removeAllActions: function() {
		for (var e = 0; e < this._targets.length; e++) {
			var t = this._targets[e];
			t && this.removeAllActionsFromTarget(t.target)
		}
	},
	removeAllActionsFromTarget: function(e) {
		if (e == null) return;
		var t = this._searchElementByTarget(this._targets, e);
		t && (t.currentAction in t.actions && !t.currentActionSalvaged && (t.currentActionSalvaged = !0), t.actions = [], this._currentTarget == t ? this._currentTargetSalvaged = !0 : this._deleteHashElement(t))
	},
	removeAction: function(e) {
		if (e == null) return;
		var t = e.getOriginalTarget(),
			n = this._searchElementByTarget(this._targets, t);
		if (n) {
			for (var r = 0; r < n.actions.length; r++) if (n.actions[r] == e) {
				n.actions.splice(r, 1);
				break
			}
		} else cc.log("cocos2d: removeAction: Target not found")
	},
	removeActionByTag: function(e, t) {
		cc.Assert(e != cc.ACTION_TAG_INVALID, ""), cc.Assert(t != null, "");
		var n = this._searchElementByTarget(this._targets, t);
		if (n) {
			var r = n.actions.length;
			for (var i = 0; i < r; ++i) {
				var s = n.actions[i];
				if (s && s.getTag() == e && s.getOriginalTarget() == t) {
					this._removeActionAtIndex(i, n);
					break
				}
			}
		}
	},
	getActionByTag: function(e, t) {
		cc.Assert(e != cc.ACTION_TAG_INVALID, "");
		var n = this._searchElementByTarget(this._targets, t);
		if (n && n.actions != null) for (var r = 0; r < n.actions.length; ++r) {
			var i = n.actions[r];
			if (i && i.getTag() == e) return i
		}
		return null
	},
	numberOfRunningActionsInTarget: function(e) {
		var t = this._searchElementByTarget(this._targets, e);
		return t ? t.actions ? t.actions.length : 0 : 0
	},
	pauseTarget: function(e) {
		var t = this._searchElementByTarget(this._targets, e);
		t && (t.paused = !0)
	},
	resumeTarget: function(e) {
		var t = this._searchElementByTarget(this._targets, e);
		t && (t.paused = !1)
	},
	pauseAllRunningActions: function() {
		var e = [];
		for (var t = 0; t < this._targets.length; t++) {
			var n = this._targets[t];
			n && !n.paused && (n.paused = !0, e.push(n.target))
		}
		return e
	},
	resumeTargets: function(e) {
		if (!e) return;
		for (var t = 0; t < e.length; t++) e[t] && this.resumeTarget(e[t])
	},
	purgeSharedManager: function() {
		cc.Director.getInstance().getScheduler().unscheduleUpdateForTarget(this)
	},
	_removeActionAtIndex: function(e, t) {
		var n = t.actions[e];
		n == t.currentAction && !t.currentActionSalvaged && (t.currentActionSalvaged = !0), cc.ArrayRemoveObjectAtIndex(t.actions, e), t.actionIndex >= e && t.actionIndex--, t.actions.length == 0 && (this._currentTarget == t ? this._currentTargetSalvaged = !0 : this._deleteHashElement(t))
	},
	_deleteHashElement: function(e) {
		cc.ArrayRemoveObject(this._targets, e), e && (e.actions = null, e.target = null)
	},
	_actionAllocWithHashElement: function(e) {
		e.actions == null && (e.actions = [])
	},
	update: function(e) {
		for (var t = 0; t < this._targets.length; t++) {
			this._currentTarget = this._targets[t], this._currentTargetSalvaged = !1;
			if (!this._currentTarget.paused) for (this._currentTarget.actionIndex = 0; this._currentTarget.actionIndex < this._currentTarget.actions.length; this._currentTarget.actionIndex++) {
				this._currentTarget.currentAction = this._currentTarget.actions[this._currentTarget.actionIndex];
				if (!this._currentTarget.currentAction) continue;
				this._currentTarget.currentActionSalvaged = !1, this._currentTarget.currentAction.step(e);
				if (this._currentTarget.currentActionSalvaged) this._currentTarget.currentAction = null;
				else if (this._currentTarget.currentAction.isDone()) {
					this._currentTarget.currentAction.stop();
					var n = this._currentTarget.currentAction;
					this._currentTarget.currentAction = null, this.removeAction(n)
				}
				this._currentTarget.currentAction = null
			}
			this._currentTargetSalvaged && this._currentTarget.actions.length == 0 && this._deleteHashElement(this._currentTarget)
		}
	}
});
cc.ActionEase = cc.ActionInterval.extend({
	initWithAction: function(e) {
		return cc.Assert(e != null, ""), this.initWithDuration(e.getDuration()) ? (this._other = e, !0) : !1
	},
	startWithTarget: function(e) {
		cc.ActionInterval.prototype.startWithTarget.call(this, e), this._other.startWithTarget(this._target)
	},
	stop: function() {
		this._other.stop(), this._super()
	},
	update: function(e) {
		this._other.update(e)
	},
	reverse: function() {
		return cc.ActionEase.create(this._other.reverse())
	},
	_other: null
}), cc.ActionEase.create = function(e) {
	var t = new cc.ActionEase();
	return t && t.initWithAction(e), t
}, cc.EaseRateAction = cc.ActionEase.extend({
	setRate: function(e) {
		this._rate = e
	},
	getRate: function() {
		return this._rate
	},
	initWithAction: function(e, t) {
		return this._super(e) ? (this._rate = t, !0) : !1
	},
	reverse: function() {
		return cc.EaseRateAction.create(this._other.reverse(), 1 / this._rate)
	},
	_rate: null
}), cc.EaseRateAction.create = function(e, t) {
	var n = new cc.EaseRateAction();
	return n && n.initWithAction(e, t), n
}, cc.EaseIn = cc.EaseRateAction.extend({
	update: function(e) {
		this._other.update(Math.pow(e, this._rate))
	},
	reverse: function() {
		return cc.EaseIn.create(this._other.reverse(), 1 / this._rate)
	}
}), cc.EaseIn.create = function(e, t) {
	var n = new cc.EaseIn();
	return n && n.initWithAction(e, t), n
}, cc.EaseOut = cc.EaseRateAction.extend({
	update: function(e) {
		this._other.update(Math.pow(e, 1 / this._rate))
	},
	reverse: function() {
		return cc.EaseOut.create(this._other.reverse(), 1 / this._rate)
	}
}), cc.EaseOut.create = function(e, t) {
	var n = new cc.EaseOut();
	return n && n.initWithAction(e, t), n
}, cc.EaseInOut = cc.EaseRateAction.extend({
	update: function(e) {
		e *= 2, e < 1 ? this._other.update(.5 * Math.pow(e, this._rate)) : this._other.update(1 - .5 * Math.pow(2 - e, this._rate))
	},
	reverse: function() {
		return cc.EaseInOut.create(this._other.reverse(), this._rate)
	}
}), cc.EaseInOut.create = function(e, t) {
	var n = new cc.EaseInOut();
	return n && n.initWithAction(e, t), n
}, cc.EaseExponentialIn = cc.ActionEase.extend({
	update: function(e) {
		this._other.update(e == 0 ? 0 : Math.pow(2, 10 * (e - 1)) - .001)
	},
	reverse: function() {
		return cc.EaseExponentialOut.create(this._other.reverse())
	}
}), cc.EaseExponentialIn.create = function(e) {
	var t = new cc.EaseExponentialIn();
	return t && t.initWithAction(e), t
}, cc.EaseExponentialOut = cc.ActionEase.extend({
	update: function(e) {
		this._other.update(e == 1 ? 1 : -Math.pow(2, -10 * e) + 1)
	},
	reverse: function() {
		return cc.EaseExponentialIn.create(this._other.reverse())
	}
}), cc.EaseExponentialOut.create = function(e) {
	var t = new cc.EaseExponentialOut();
	return t && t.initWithAction(e), t
}, cc.EaseExponentialInOut = cc.ActionEase.extend({
	update: function(e) {
		e /= .5, e < 1 ? e = .5 * Math.pow(2, 10 * (e - 1)) : e = .5 * (-Math.pow(2, -10 * (e - 1)) + 2), this._other.update(e)
	},
	reverse: function() {
		return cc.EaseExponentialInOut.create(this._other.reverse())
	}
}), cc.EaseExponentialInOut.create = function(e) {
	var t = new cc.EaseExponentialInOut();
	return t && t.initWithAction(e), t
}, cc.EaseSineIn = cc.ActionEase.extend({
	update: function(e) {
		this._other.update(-1 * Math.cos(e * Math.PI / 2) + 1)
	},
	reverse: function() {
		return cc.EaseSineOut.create(this._other.reverse())
	}
}), cc.EaseSineIn.create = function(e) {
	var t = new cc.EaseSineIn();
	return t && t.initWithAction(e), t
}, cc.EaseSineOut = cc.ActionEase.extend({
	update: function(e) {
		this._other.update(Math.sin(e * Math.PI / 2))
	},
	reverse: function() {
		return cc.EaseSineIn.create(this._other.reverse())
	}
}), cc.EaseSineOut.create = function(e) {
	var t = new cc.EaseSineOut();
	return t && t.initWithAction(e), t
}, cc.EaseSineInOut = cc.ActionEase.extend({
	update: function(e) {
		this._other.update(-.5 * (Math.cos(Math.PI * e) - 1))
	},
	reverse: function() {
		return cc.EaseSineInOut.create(this._other.reverse())
	}
}), cc.EaseSineInOut.create = function(e) {
	var t = new cc.EaseSineInOut();
	return t && t.initWithAction(e), t
}, cc.EaseElastic = cc.ActionEase.extend({
	getPeriod: function() {
		return this._period
	},
	setPeriod: function(e) {
		this._period = e
	},
	initWithAction: function(e, t) {
		return this._super(e), this._period = t == null ? 3 : t, !0
	},
	reverse: function() {
		return cc.Assert(0, "Override me"), null
	},
	_period: null
}), cc.EaseElastic.create = function(e, t) {
	var n = new cc.EaseElastic();
	return n && (t == null ? n.initWithAction(e) : n.initWithAction(e, t)), n
}, cc.EaseElasticIn = cc.EaseElastic.extend({
	update: function(e) {
		var t = 0;
		if (e == 0 || e == 1) t = e;
		else {
			var n = this._period / 4;
			e -= 1, t = -Math.pow(2, 10 * e) * Math.sin((e - n) * Math.PI * 2 / this._period)
		}
		this._other.update(t)
	},
	reverse: function() {
		return cc.EaseElasticOut.create(this._other.reverse(), this._period)
	}
}), cc.EaseElasticIn.create = function(e, t) {
	var n = new cc.EaseElasticIn();
	return n && (t == null ? n.initWithAction(e) : n.initWithAction(e, t)), n
}, cc.EaseElasticOut = cc.EaseElastic.extend({
	update: function(e) {
		var t = 0;
		if (e == 0 || e == 1) t = e;
		else {
			var n = this._period / 4;
			t = Math.pow(2, -10 * e) * Math.sin((e - n) * Math.PI * 2 / this._period) + 1
		}
		this._other.update(t)
	},
	reverse: function() {
		return cc.EaseElasticIn.create(this._other.reverse(), this._period)
	}
}), cc.EaseElasticOut.create = function(e, t) {
	var n = new cc.EaseElasticOut();
	return n && (t == null ? n.initWithAction(e) : n.initWithAction(e, t)), n
}, cc.EaseElasticInOut = cc.EaseElastic.extend({
	update: function(e) {
		var t = 0;
		if (e === 0 || e === 1) t = e;
		else {
			e *= 2, this._period || (this._period = .3 * 1.5);
			var n = this._period / 4;
			e -= 1, e < 0 ? t = -.5 * Math.pow(2, 10 * e) * Math.sin((e - n) * Math.PI * 2 / this._period) : t = Math.pow(2, -10 * e) * Math.sin((e - n) * Math.PI * 2 / this._period) * .5 + 1
		}
		this._other.update(t)
	},
	reverse: function() {
		return cc.EaseElasticInOut.create(this._other.reverse(), this._period)
	}
}), cc.EaseElasticInOut.create = function(e, t) {
	var n = new cc.EaseElasticInOut();
	return n && (t == null ? n.initWithAction(e) : n.initWithAction(e, t)), n
}, cc.EaseBounce = cc.ActionEase.extend({
	bounceTime: function(e) {
		return e < 1 / 2.75 ? 7.5625 * e * e : e < 2 / 2.75 ? (e -= 1.5 / 2.75, 7.5625 * e * e + .75) : e < 2.5 / 2.75 ? (e -= 2.25 / 2.75, 7.5625 * e * e + .9375) : (e -= 2.625 / 2.75, 7.5625 * e * e + .984375)
	},
	reverse: function() {
		return cc.EaseBounce.create(this._other.reverse())
	}
}), cc.EaseBounce.create = function(e) {
	var t = new cc.EaseBounce();
	return t && t.initWithAction(e), t
}, cc.EaseBounceIn = cc.EaseBounce.extend({
	update: function(e) {
		var t = 1 - this.bounceTime(1 - e);
		this._other.update(t)
	},
	reverse: function() {
		return cc.EaseBounceOut.create(this._other.reverse())
	}
}), cc.EaseBounceIn.create = function(e) {
	var t = new cc.EaseBounceIn();
	return t && t.initWithAction(e), t
}, cc.EaseBounceOut = cc.EaseBounce.extend({
	update: function(e) {
		var t = this.bounceTime(e);
		this._other.update(t)
	},
	reverse: function() {
		return cc.EaseBounceIn.create(this._other.reverse())
	}
}), cc.EaseBounceOut.create = function(e) {
	var t = new cc.EaseBounceOut();
	return t && t.initWithAction(e), t
}, cc.EaseBounceInOut = cc.EaseBounce.extend({
	update: function(e) {
		var t = 0;
		e < .5 ? (e *= 2, t = (1 - this.bounceTime(1 - e)) * .5) : t = this.bounceTime(e * 2 - 1) * .5 + .5, this._other.update(t)
	},
	reverse: function() {
		return cc.EaseBounceInOut.create(this._other.reverse())
	}
}), cc.EaseBounceInOut.create = function(e) {
	var t = new cc.EaseBounceInOut();
	return t && t.initWithAction(e), t
}, cc.EaseBackIn = cc.ActionEase.extend({
	update: function(e) {
		var t = 1.70158;
		this._other.update(e * e * ((t + 1) * e - t))
	},
	reverse: function() {
		return cc.EaseBackOut.create(this._other.reverse())
	}
}), cc.EaseBackIn.create = function(e) {
	var t = new cc.EaseBackIn();
	return t && t.initWithAction(e), t
}, cc.EaseBackOut = cc.ActionEase.extend({
	update: function(e) {
		var t = 1.70158;
		e -= 1, this._other.update(e * e * ((t + 1) * e + t) + 1)
	},
	reverse: function() {
		return cc.EaseBackIn.create(this._other.reverse())
	}
}), cc.EaseBackOut.create = function(e) {
	var t = new cc.EaseBackOut();
	return t && t.initWithAction(e), t
}, cc.EaseBackInOut = cc.ActionEase.extend({
	update: function(e) {
		var t = 2.5949095;
		e *= 2, e < 1 ? this._other.update(e * e * ((t + 1) * e - t) / 2) : (e -= 2, this._other.update(e * e * ((t + 1) * e + t) / 2 + 1))
	},
	reverse: function() {
		return cc.EaseBackInOut.create(this._other.reverse())
	}
}), cc.EaseBackInOut.create = function(e) {
	var t = new cc.EaseBackInOut();
	return t && t.initWithAction(e), t
};
cc.Scene = cc.Node.extend({
	ctor: function() {
		this._super()
	},
	init: function() {
		return this._super(), this._ignoreAnchorPointForPosition = !0, this.setAnchorPoint(cc.p(.5, .5)), this.setContentSize(cc.Director.getInstance().getWinSize()), !0
	}
}), cc.Scene.create = function() {
	var e = new cc.Scene();
	return e.init(), e
};
cc.TOUCH_ALL_AT_ONCE = 0, cc.TOUCH_ONE_BY_ONE = 1, cc.Layer = cc.Node.extend({
	_isTouchEnabled: !1,
	_isAccelerometerEnabled: !1,
	_isKeyboardEnabled: !1,
	_touchPriority: 0,
	_touchMode: cc.TOUCH_ALL_AT_ONCE,
	_isMouseEnabled: !1,
	_mousePriority: 0,
	ctor: function() {
		this._super()
	},
	_initLayer: function() {
		this.setAnchorPoint(cc.p(.5, .5)), this._ignoreAnchorPointForPosition = !0;
		var e = cc.Director.getInstance();
		this.setContentSize(e.getWinSize()), this._isTouchEnabled = !1, this._isAccelerometerEnabled = !1, this._isMouseEnabled = !1, this._touchMode = cc.TOUCH_ALL_AT_ONCE, this._touchPriority = 0
	},
	init: function() {
		return this._super(), this._initLayer(), !0
	},
	registerWithTouchDispatcher: function() {
		this._touchMode === cc.TOUCH_ALL_AT_ONCE ? cc.Director.getInstance().getTouchDispatcher().addStandardDelegate(this, this._touchPriority) : cc.Director.getInstance().getTouchDispatcher().addTargetedDelegate(this, this._touchPriority, !0)
	},
	isMouseEnabled: function() {
		return this._isMouseEnabled
	},
	setMouseEnabled: function(e) {
		this._isMouseEnabled != e && (this._isMouseEnabled = e, this._running && (e ? cc.Director.getInstance().getMouseDispatcher().addMouseDelegate(this, this._mousePriority) : cc.Director.getInstance().getMouseDispatcher().removeMouseDelegate(this)))
	},
	setMousePriority: function(e) {
		this._mousePriority != e && (this._mousePriority = e, this._isMouseEnabled && (this.setMouseEnabled(!1), this.setMouseEnabled(!0)))
	},
	getMousePriority: function() {
		return this._mousePriority
	},
	isTouchEnabled: function() {
		return this._isTouchEnabled
	},
	setTouchEnabled: function(e) {
		this._isTouchEnabled != e && (this._isTouchEnabled = e, this._running && (e ? this.registerWithTouchDispatcher() : cc.Director.getInstance().getTouchDispatcher().removeDelegate(this)))
	},
	getTouchPriority: function() {
		return this._touchPriority
	},
	setTouchPriority: function(e) {
		this._touchPriority != e && (this._touchPriority = e, this._isTouchEnabled && (this.setTouchEnabled(!1), this.setTouchEnabled(!0)))
	},
	getTouchMode: function() {
		return this._touchMode
	},
	setTouchMode: function(e) {
		this._touchMode != e && (this._touchMode = e, this._isTouchEnabled && (this.setTouchEnabled(!1), this.setTouchEnabled(!0)))
	},
	isAccelerometerEnabled: function() {
		return this._isAccelerometerEnabled
	},
	setAccelerometerEnabled: function(e) {
		if (e != this._isAccelerometerEnabled) {
			this._isAccelerometerEnabled = e;
			if (this._running) {
				var t = cc.Director.getInstance();
				e ? t.getAccelerometer().setDelegate(this) : t.getAccelerometer().setDelegate(null)
			}
		}
	},
	isKeyboardEnabled: function() {
		return this._isKeyboardEnabled
	},
	setKeyboardEnabled: function(e) {
		if (e != this._isKeyboardEnabled) {
			this._isKeyboardEnabled = e;
			if (this._running) {
				var t = cc.Director.getInstance();
				e ? t.getKeyboardDispatcher().addDelegate(this) : t.getKeyboardDispatcher().removeDelegate(this)
			}
		}
	},
	onEnter: function() {
		var e = cc.Director.getInstance();
		this._isTouchEnabled && this.registerWithTouchDispatcher(), this._super(), this._isKeyboardEnabled && e.getKeyboardDispatcher().addDelegate(this), this._isMouseEnabled && e.getMouseDispatcher().addMouseDelegate(this, this._mousePriority)
	},
	onExit: function() {
		var e = cc.Director.getInstance();
		this._isTouchEnabled && e.getTouchDispatcher().removeDelegate(this), this._isKeyboardEnabled && e.getKeyboardDispatcher().removeDelegate(this), this._isMouseEnabled && e.getMouseDispatcher().removeMouseDelegate(this), this._super()
	},
	onEnterTransitionDidFinish: function() {
		this._super()
	},
	onTouchBegan: function(e, t) {
		return cc.Assert(!1, "Layer#onTouchBegan override me"), !0
	},
	onTouchMoved: function(e, t) {},
	onTouchEnded: function(e, t) {},
	onTouchCancelled: function(e, t) {},
	onTouchesBegan: function(e, t) {},
	onTouchesMoved: function(e, t) {},
	onTouchesEnded: function(e, t) {},
	onTouchesCancelled: function(e, t) {},
	didAccelerate: function(e) {},
	onMouseDown: function(e) {
		return !1
	},
	onMouseDragged: function(e) {
		return !1
	},
	onMouseMoved: function(e) {
		return !1
	},
	onMouseUp: function(e) {
		return !1
	},
	onRightMouseDown: function(e) {
		return !1
	},
	onRightMouseDragged: function(e) {
		return !1
	},
	onRightMouseUp: function(e) {
		return !1
	},
	onOtherMouseDown: function(e) {
		return !1
	},
	onOtherMouseDragged: function(e) {
		return !1
	},
	onOtherMouseUp: function(e) {
		return !1
	},
	onScrollWheel: function(e) {
		return !1
	},
	onMouseEntered: function(e) {
		return !1
	},
	onMouseExited: function(e) {
		return !1
	}
}), cc.Layer.create = function() {
	var e = new cc.Layer();
	return e && e.init() ? e : null
}, cc.LayerColor = cc.Layer.extend({
	RGBAProtocol: !0,
	_squareVertices: [],
	_squareColors: [],
	_opacity: 0,
	_color: new cc.Color3B(255, 255, 255),
	_blendFunc: new cc.BlendFunc(cc.BLEND_SRC, cc.BLEND_DST),
	_layerColorStr: null,
	ctor: function() {
		this._squareVertices = [new cc.Vertex2F(0, 0), new cc.Vertex2F(0, 0), new cc.Vertex2F(0, 0), new cc.Vertex2F(0, 0)], this._squareColors = [new cc.Color4F(0, 0, 0, 1), new cc.Color4F(0, 0, 0, 1), new cc.Color4F(0, 0, 0, 1), new cc.Color4F(0, 0, 0, 1)], this._color = new cc.Color4B(0, 0, 0, 0), this._opacity = 255, this._super(), this._layerColorStr = this._getLayerColorString()
	},
	_getLayerColorString: function() {
		return "rgba(" + (0 | this._color.r) + "," + (0 | this._color.g) + "," + (0 | this._color.b) + "," + (this.getOpacity() / 255).toFixed(5) + ")"
	},
	getOpacity: function() {
		return this._opacity
	},
	setOpacity: function(e) {
		this._opacity = e, this._updateColor(), this.setNodeDirty()
	},
	getColor: function() {
		return this._color
	},
	setColor: function(e) {
		this._color = e, this._updateColor(), this.setNodeDirty()
	},
	getBlendFunc: function() {
		return this._blendFunc
	},
	_isLighterMode: !1,
	setBlendFunc: function(e, t) {
		arguments.length == 1 ? this._blendFunc = e : this._blendFunc = {
			src: e,
			dst: t
		}, this._isLighterMode = this._blendFunc && this._blendFunc.src == 1 && this._blendFunc.dst == 771
	},
	init: function(e, t, n) {
		this._initLayer();
		var r = cc.Director.getInstance().getWinSize();
		e = e || new cc.Color4B(0, 0, 0, 255), t = t || r.width, n = n || r.height, this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, this._color = new cc.Color3B(e.r, e.g, e.b), this._opacity = e.a;
		for (var i = 0; i < this._squareVertices.length; i++) this._squareVertices[i].x = 0, this._squareVertices[i].y = 0;
		return this.setContentSize(cc.size(t, n)), this._updateColor(), !0
	},
	setContentSize: function(e) {
		this._squareVertices[1].x = e.width, this._squareVertices[2].y = e.height, this._squareVertices[3].x = e.width, this._squareVertices[3].y = e.height, this._super(e)
	},
	changeWidthAndHeight: function(e, t) {
		this.setContentSize(cc.size(e, t))
	},
	changeWidth: function(e) {
		this.setContentSize(cc.size(e, this._contentSize.height))
	},
	changeHeight: function(e) {
		this.setContentSize(cc.size(this._contentSize.width, e))
	},
	_updateColor: function() {
		for (var e = 0; e < 4; e++) this._squareColors[e].r = this._color.r / 255, this._squareColors[e].g = this._color.g / 255, this._squareColors[e].b = this._color.b / 255, this._squareColors[e].a = this._opacity / 255
	},
	setOpacityModifyRGB: function(e) {},
	isOpacityModifyRGB: function() {
		return !1
	},
	draw: function(e) {
		var t = e || cc.renderContext,
			n = this.getContentSize().width,
			r = this.getContentSize().height,
			i = this.getAnchorPointInPoints();
		t.fillStyle = "rgba(" + (0 | this._color.r) + "," + (0 | this._color.g) + "," + (0 | this._color.b) + "," + this.getOpacity() / 255 + ")", t.fillRect(-i.x, i.y, n, -r), cc.INCREMENT_GL_DRAWS(1)
	},
	_drawForWebGL: function(e) {}
}), cc.LayerColor.create = function(e, t, n) {
	var r = new cc.LayerColor();
	switch (arguments.length) {
	case 0:
		r.init();
		break;
	case 1:
		r.init(e);
		break;
	case 3:
		r.init(e, t, n);
		break;
	default:
		r.init()
	}
	return r
}, cc.LayerGradient = cc.LayerColor.extend({
	_startColor: null,
	_endColor: null,
	_startOpacity: null,
	_endOpacity: null,
	_alongVector: null,
	_compressedInterpolation: !1,
	_gradientStartPoint: null,
	_gradientEndPoint: null,
	ctor: function() {
		this._super(), this._color = new cc.Color3B(0, 0, 0), this._startColor = new cc.Color3B(0, 0, 0), this._endColor = new cc.Color3B(0, 0, 0), this._alongVector = cc.p(0, -1), this._startOpacity = 255, this._endOpacity = 255, this._gradientStartPoint = cc.p(0, 0), this._gradientEndPoint = cc.p(0, 0)
	},
	getStartColor: function() {
		return this._color
	},
	setStartColor: function(e) {
		this.setColor(e)
	},
	setEndColor: function(e) {
		this._endColor = e, this._updateColor()
	},
	getEndColor: function() {
		return this._endColor
	},
	setStartOpacity: function(e) {
		this._startOpacity = e, this._updateColor()
	},
	getStartOpacity: function() {
		return this._startOpacity
	},
	setEndOpacity: function(e) {
		this._endOpacity = e, this._updateColor()
	},
	getEndOpacity: function() {
		return this._endOpacity
	},
	setVector: function(e) {
		this._alongVector = e, this._updateColor()
	},
	getVector: function() {
		return this._alongVector
	},
	isCompressedInterpolation: function() {
		return this._compressedInterpolation
	},
	setCompressedInterpolation: function(e) {
		this._compressedInterpolation = e, this._updateColor()
	},
	init: function(e, t, n) {
		var r = arguments.length;
		return r == 0 ? this._super() : (r == 2 && (n = cc.p(0, -1)), this._startColor.r = e.r, this._startColor.g = e.g, this._startColor.b = e.b, this._startOpacity = e.a, this._endColor.r = t.r, this._endColor.g = t.g, this._endColor.b = t.b, this._endOpacity = t.a, this._alongVector = n, this._compressedInterpolation = !0, this._super(cc.c4b(e.r, e.g, e.b, 255)), !0)
	},
	_updateColor: function() {
		if (cc.renderContextType === cc.CANVAS) {
			var e = this.getContentSize().width / 2,
				t = this.getContentSize().height / 2,
				n = this.getAnchorPointInPoints(),
				r = e - n.x,
				i = t - n.y;
			this._gradientStartPoint = cc.p(e * -this._alongVector.x + r, t * this._alongVector.y - i), this._gradientEndPoint = cc.p(e * this._alongVector.x + r, t * -this._alongVector.y - i)
		} else {
			this._super();
			var s = cc.pLength(this._alongVector);
			if (s == 0) return;
			var o = Math.sqrt(2),
				u = cc.p(this._alongVector.x / s, this._alongVector.y / s);
			if (this._compressedInterpolation) {
				var a = 1 / (Math.abs(u.x) + Math.abs(u.y));
				u = cc.pMult(u, a * o)
			}
			var f = this._opacity / 255,
				l = new cc.Color4F(this._color.r / 255, this._color.g / 255, this._color.b / 255, this._startOpacity * f / 255),
				c = new cc.Color4F(this._endColor.r / 255, this._endColor.g / 255, this._endColor.b / 255, this._endOpacity * f / 255);
			this._squareColors[0].r = c.r + (l.r - c.r) * ((o + u.x + u.y) / (2 * o)), this._squareColors[0].g = c.g + (l.g - c.g) * ((o + u.x + u.y) / (2 * o)), this._squareColors[0].b = c.b + (l.b - c.b) * ((o + u.x + u.y) / (2 * o)), this._squareColors[0].a = c.a + (l.a - c.a) * ((o + u.x + u.y) / (2 * o)), this._squareColors[1].r = c.r + (l.r - c.r) * ((o - u.x + u.y) / (2 * o)), this._squareColors[1].g = c.g + (l.g - c.g) * ((o - u.x + u.y) / (2 * o)), this._squareColors[1].b = c.b + (l.b - c.b) * ((o - u.x + u.y) / (2 * o)), this._squareColors[1].a = c.a + (l.a - c.a) * ((o - u.x + u.y) / (2 * o)), this._squareColors[2].r = c.r + (l.r - c.r) * ((o + u.x - u.y) / (2 * o)), this._squareColors[2].g = c.g + (l.g - c.g) * ((o + u.x - u.y) / (2 * o)), this._squareColors[2].b = c.b + (l.b - c.b) * ((o + u.x - u.y) / (2 * o)), this._squareColors[2].a = c.a + (l.a - c.a) * ((o + u.x - u.y) / (2 * o)), this._squareColors[3].r = c.r + (l.r - c.r) * ((o - u.x - u.y) / (2 * o)), this._squareColors[3].g = c.g + (l.g - c.g) * ((o - u.x - u.y) / (2 * o)), this._squareColors[3].b = c.b + (l.b - c.b) * ((o - u.x - u.y) / (2 * o)), this._squareColors[3].a = c.a + (l.a - c.a) * ((o - u.x - u.y) / (2 * o))
		}
	},
	draw: function(e) {
		var t = e || cc.renderContext;
		if (cc.renderContextType == cc.CANVAS) {
			this._isLighterMode && (t.globalCompositeOperation = "lighter"), t.save();
			var n = this.getContentSize().width,
				r = this.getContentSize().height,
				i = this.getAnchorPointInPoints(),
				s = t.createLinearGradient(this._gradientStartPoint.x, this._gradientStartPoint.y, this._gradientEndPoint.x, this._gradientEndPoint.y);
			s.addColorStop(0, "rgba(" + Math.round(this._color.r) + "," + Math.round(this._color.g) + "," + Math.round(this._color.b) + "," + (this._startOpacity / 255).toFixed(4) + ")"), s.addColorStop(1, "rgba(" + Math.round(this._endColor.r) + "," + Math.round(this._endColor.g) + "," + Math.round(this._endColor.b) + "," + (this._endOpacity / 255).toFixed(4) + ")"), t.fillStyle = s, t.fillRect(-i.x, i.y, n, -r), this._rotation != 0 && t.rotate(this._rotationRadians), t.restore()
		}
	}
}), cc.LayerGradient.create = function(e, t, n) {
	var r = new cc.LayerGradient();
	switch (arguments.length) {
	case 2:
		if (r && r.init(e, t)) return r;
		break;
	case 3:
		if (r && r.init(e, t, n)) return r;
		break;
	case 0:
		if (r && r.init()) return r;
		break;
	default:
		throw "Arguments error "
	}
	return null
}, cc.LayerMultiplex = cc.Layer.extend({
	_enabledLayer: 0,
	_layers: null,
	ctor: function() {
		this._super()
	},
	initWithLayer: function(e) {
		return this._layers = [], this._layers.push(e), this._enabledLayer = 0, this.addChild(e), !0
	},
	initWithLayers: function(e) {
		return this._layers = e, this._enabledLayer = 0, this.addChild(this._layers[this._enabledLayer]), !0
	},
	switchTo: function(e) {
		cc.Assert(e < this._layers.length, "Invalid index in MultiplexLayer switchTo message"), this.removeChild(this._layers[this._enabledLayer], !0), this._enabledLayer = e, this.addChild(this._layers[e])
	},
	switchToAndReleaseMe: function(e) {
		cc.Assert(e < this._layers.count(), "Invalid index in MultiplexLayer switchTo message"), this.removeChild(this._layers[this._enabledLayer], !0), this._layers[this._enabledLayer] = null, this._enabledLayer = e, this.addChild(this._layers[e])
	},
	addLayer: function(e) {
		cc.Assert(this._layers, "cc.Layer addLayer"), this._layers.push(e)
	}
}), cc.LayerMultiplex.create = function() {
	var e = new cc.LayerMultiplex();
	return e.initWithLayers(arguments) ? e : null
}, cc.LazyLayer = cc.Node.extend({
	_layerCanvas: null,
	_layerContext: null,
	_isNeedUpdate: !1,
	_canvasZOrder: -10,
	_layerId: "",
	ctor: function() {
		this._super(), this.setAnchorPoint(cc.p(0, 0)), this._setupHtml()
	},
	setLayerZOrder: function(e) {
		if (e >= 0) throw "LazyLayer zOrder must Less than Zero.Because LazyLayer is a background Layer!";
		this._canvasZOrder = e, this._layerCanvas.style.zIndex = this._canvasZOrder
	},
	getLayerZOrder: function() {
		return this._canvasZOrder
	},
	_setupHtml: function() {
		this._layerCanvas = document.createElement("canvas"), this._layerCanvas.width = cc.canvas.width, this._layerCanvas.height = cc.canvas.height, this._layerId = "lazyCanvas" + Date.now(), this._layerCanvas.id = this._layerId, this._layerCanvas.style.zIndex = this._canvasZOrder, this._layerCanvas.style.position = "absolute", this._layerCanvas.style.top = "0", this._layerCanvas.style.left = "0", this._layerContext = this._layerCanvas.getContext("2d"), this._layerContext.fillStyle = "rgba(0,0,0,1)", this._layerContext.translate(0, this._layerCanvas.height), cc.container.appendChild(this._layerCanvas);
		var e = this;
		window.addEventListener("resize", function(t) {
			e.adjustSizeForCanvas()
		})
	},
	adjustSizeForCanvas: function() {
		this._isNeedUpdate = !0, this._layerCanvas.width = cc.canvas.width, this._layerCanvas.height = cc.canvas.height;
		var e = cc.canvas.width / cc.originalCanvasSize.width,
			t = cc.canvas.height / cc.originalCanvasSize.height;
		e > t && (e = t), this._layerContext.translate(0, this._layerCanvas.height), this._layerContext.scale(e, e)
	},
	getLayerCanvas: function() {
		return this._layerCanvas
	},
	addChild: function(e, t, n) {
		this._isNeedUpdate = !0, this._super(e, t, n)
	},
	removeChild: function(e, t) {
		this._isNeedUpdate = !0, this._super(e, t)
	},
	visit: function() {
		if (!this._visible) return;
		if (!this._isNeedUpdate) return;
		this._isNeedUpdate = !1;
		var e = this._layerContext;
		e.save(), e.clearRect(0, 0, this._layerCanvas.width, -this._layerCanvas.height);
		if (this._children && this._children.length > 0) {
			this.sortAllChildren();
			for (var t = 0; t < this._children.length; t++) this._children[t].visit(e)
		}
		e.restore()
	},
	onExit: function() {
		this._super(), this._layerCanvas.parentNode && this._layerCanvas.parentNode.removeChild(this._layerCanvas)
	},
	_setNodeDirtyForCache: function() {
		this._cacheDirty = !0, this._isNeedUpdate = !0
	}
});
cc.SCENE_FADE = 4208917214, cc.TransitionEaseScene = cc.Class.extend({
	easeActionWithAction: function() {}
}), cc.TRANSITION_ORIENTATION_LEFT_OVER = 0, cc.TRANSITION_ORIENTATION_RIGHT_OVER = 1, cc.TRANSITION_ORIENTATION_UP_OVER = 0, cc.TRANSITION_ORIENTATION_DOWN_OVER = 1, cc.TransitionScene = cc.Scene.extend({
	_inScene: null,
	_outScene: null,
	_duration: null,
	_isInSceneOnTop: !1,
	_isSendCleanupToScene: !1,
	_setNewScene: function(e) {
		this.unschedule(this._setNewScene);
		var t = cc.Director.getInstance();
		this._isSendCleanupToScene = t.isSendCleanupToScene(), t.replaceScene(this._inScene), t.getTouchDispatcher().setDispatchEvents(!0), this._outScene.setVisible(!0)
	},
	_sceneOrder: function() {
		this._isInSceneOnTop = !0
	},
	draw: function() {
		this._super(), this._isInSceneOnTop ? (this._outScene.visit(), this._inScene.visit()) : (this._inScene.visit(), this._outScene.visit())
	},
	onEnter: function() {
		this._super(), this._inScene.onEnter()
	},
	onExit: function() {
		this._super(), this._outScene.onExit(), this._inScene.onEnterTransitionDidFinish()
	},
	cleanup: function() {
		this._super(), this._isSendCleanupToScene && this._outScene.cleanup()
	},
	initWithDuration: function(e, t) {
		return cc.Assert(t != null, "CCTransitionScene.initWithDuration() Argument scene must be non-nil"), this.init() ? (this._duration = e, this.setAnchorPoint(cc.p(0, 0)), this.setPosition(cc.p(0, 0)), this._inScene = t, this._outScene = cc.Director.getInstance().getRunningScene(), this._outScene || (this._outScene = cc.Scene.create(), this._outScene.init()), cc.Assert(this._inScene != this._outScene, "CCTransitionScene.initWithDuration() Incoming scene must be different from the outgoing scene"), cc.Director.getInstance().getTouchDispatcher().setDispatchEvents(!1), this._sceneOrder(), !0) : !1
	},
	finish: function() {
		this._inScene.setVisible(!0), this._inScene.setPosition(cc.p(0, 0)), this._inScene.setScale(1), this._inScene.setRotation(0), this._inScene.getCamera().restore(), this._outScene.setVisible(!1), this._outScene.setPosition(cc.p(0, 0)), this._outScene.setScale(1), this._outScene.setRotation(0), this._outScene.getCamera().restore(), this.schedule(this._setNewScene, 0)
	},
	hideOutShowIn: function() {
		this._inScene.setVisible(!0), this._outScene.setVisible(!1)
	}
}), cc.TransitionScene.create = function(e, t) {
	var n = new cc.TransitionScene();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionSceneOriented = cc.TransitionScene.extend({
	_orientation: 0,
	initWithDuration: function(e, t, n) {
		return this._super(e, t) && (this._orientation = n), !0
	}
}), cc.TransitionSceneOriented.create = function(e, t, n) {
	var r = new cc.TransitionSceneOriented();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionRotoZoom = cc.TransitionScene.extend({
	ctor: function() {},
	onEnter: function() {
		this._super(), this._inScene.setScale(.001), this._outScene.setScale(1), this._inScene.setAnchorPoint(cc.p(.5, .5)), this._outScene.setAnchorPoint(cc.p(.5, .5));
		var e = cc.Sequence.create(cc.Spawn.create(cc.ScaleBy.create(this._duration / 2, .001), cc.RotateBy.create(this._duration / 2, 720)), cc.DelayTime.create(this._duration / 2));
		this._outScene.runAction(e), this._inScene.runAction(cc.Sequence.create(e.reverse(), cc.CallFunc.create(this.finish, this)))
	}
}), cc.TransitionRotoZoom.create = function(e, t) {
	var n = new cc.TransitionRotoZoom();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionJumpZoom = cc.TransitionScene.extend({
	onEnter: function() {
		this._super();
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setScale(.5), this._inScene.setPosition(cc.p(e.width, 0)), this._inScene.setAnchorPoint(cc.p(.5, .5)), this._outScene.setAnchorPoint(cc.p(.5, .5));
		var t = cc.JumpBy.create(this._duration / 4, cc.p(-e.width, 0), e.width / 4, 2),
			n = cc.ScaleTo.create(this._duration / 4, 1),
			r = cc.ScaleTo.create(this._duration / 4, .5),
			i = cc.Sequence.create(r, t),
			s = cc.Sequence.create(t, n),
			o = cc.DelayTime.create(this._duration / 2);
		this._outScene.runAction(i), this._inScene.runAction(cc.Sequence.create(o, s, cc.CallFunc.create(this.finish, this)))
	}
}), cc.TransitionJumpZoom.create = function(e, t) {
	var n = new cc.TransitionJumpZoom();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionMoveInL = cc.TransitionScene.extend({
	onEnter: function() {
		this._super(), this.initScenes();
		var e = this.action();
		this._inScene.runAction(cc.Sequence.create(this.easeActionWithAction(e), cc.CallFunc.create(this.finish, this), null))
	},
	initScenes: function() {
		this._inScene.setPosition(cc.p(-cc.Director.getInstance().getWinSize().width, 0))
	},
	action: function() {
		return cc.MoveTo.create(this._duration, cc.p(0, 0))
	},
	easeActionWithAction: function(e) {
		return cc.EaseOut.create(e, 2)
	}
}), cc.TransitionMoveInL.create = function(e, t) {
	var n = new cc.TransitionMoveInL();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionMoveInR = cc.TransitionMoveInL.extend({
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(e.width, 0))
	}
}), cc.TransitionMoveInR.create = function(e, t) {
	var n = new cc.TransitionMoveInR();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionMoveInT = cc.TransitionMoveInL.extend({
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(e.height, 0))
	}
}), cc.TransitionMoveInT.create = function(e, t) {
	var n = new cc.TransitionMoveInT();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionMoveInB = cc.TransitionMoveInL.extend({
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(0, -e.height))
	}
}), cc.TransitionMoveInB.create = function(e, t) {
	var n = new cc.TransitionMoveInB();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.ADJUST_FACTOR = .5, cc.TransitionSlideInL = cc.TransitionScene.extend({
	_sceneOrder: function() {
		this._isInSceneOnTop = !1
	},
	ctor: function() {},
	onEnter: function() {
		this._super(), this.initScenes();
		var e = this.action(),
			t = this.action(),
			n = this.easeActionWithAction(e),
			r = cc.Sequence.create(this.easeActionWithAction(t), cc.CallFunc.create(this.finish, this), null);
		this._inScene.runAction(n), this._outScene.runAction(r)
	},
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(-(e.width - cc.ADJUST_FACTOR), 0))
	},
	action: function() {
		var e = cc.Director.getInstance().getWinSize();
		return cc.MoveBy.create(this._duration, cc.p(e.width - cc.ADJUST_FACTOR, 0))
	},
	easeActionWithAction: function(e) {
		return cc.EaseOut.create(e, 2)
	}
}), cc.TransitionSlideInL.create = function(e, t) {
	var n = new cc.TransitionSlideInL();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionSlideInR = cc.TransitionSlideInL.extend({
	_sceneOrder: function() {
		this._isInSceneOnTop = !0
	},
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(e.width - cc.ADJUST_FACTOR, 0))
	},
	action: function() {
		var e = cc.Director.getInstance().getWinSize();
		return cc.MoveBy.create(this._duration, cc.p(-(e.width - cc.ADJUST_FACTOR), 0))
	}
}), cc.TransitionSlideInR.create = function(e, t) {
	var n = new cc.TransitionSlideInR();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionSlideInB = cc.TransitionSlideInL.extend({
	_sceneOrder: function() {
		this._isInSceneOnTop = !1
	},
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(0, e.height - cc.ADJUST_FACTOR))
	},
	action: function() {
		var e = cc.Director.getInstance().getWinSize();
		return cc.MoveBy.create(this._duration, cc.p(0, -(e.height - cc.ADJUST_FACTOR)))
	}
}), cc.TransitionSlideInB.create = function(e, t) {
	var n = new cc.TransitionSlideInB();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionSlideInT = cc.TransitionSlideInL.extend({
	_sceneOrder: function() {
		this._isInSceneOnTop = !0
	},
	initScenes: function() {
		var e = cc.Director.getInstance().getWinSize();
		this._inScene.setPosition(cc.p(0, -(e.height - cc.ADJUST_FACTOR)))
	},
	action: function() {
		var e = cc.Director.getInstance().getWinSize();
		return cc.MoveBy.create(this._duration, cc.p(0, e.height - cc.ADJUST_FACTOR))
	}
}), cc.TransitionSlideInT.create = function(e, t) {
	var n = new cc.TransitionSlideInT();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionShrinkGrow = cc.TransitionScene.extend({
	onEnter: function() {
		this._super(), this._inScene.setScale(.001), this._outScene.setScale(1), this._inScene.setAnchorPoint(cc.p(2 / 3, .5)), this._outScene.setAnchorPoint(cc.p(1 / 3, .5));
		var e = cc.ScaleTo.create(this._duration, .01),
			t = cc.ScaleTo.create(this._duration, 1);
		this._inScene.runAction(this.easeActionWithAction(t)), this._outScene.runAction(cc.Sequence.create(this.easeActionWithAction(e), cc.CallFunc.create(this.finish, this)))
	},
	easeActionWithAction: function(e) {
		return cc.EaseOut.create(e, 2)
	}
}), cc.TransitionShrinkGrow.create = function(e, t) {
	var n = new cc.TransitionShrinkGrow();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionFlipX = cc.TransitionSceneOriented.extend({
	onEnter: function() {
		this._super();
		var e, t;
		this._inScene.setVisible(!1);
		var n, r, i, s;
		this._orientation == cc.TRANSITION_ORIENTATION_RIGHT_OVER ? (n = 90, r = 270, i = 90, s = 0) : (n = -90, r = 90, i = -90, s = 0), e = cc.Sequence.create(cc.DelayTime.create(this._duration / 2), cc.Show.create(), cc.OrbitCamera.create(this._duration / 2, 1, 0, r, n, 0, 0), cc.CallFunc.create(this.finish, this)), t = cc.Sequence.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, s, i, 0, 0), cc.Hide.create(), cc.DelayTime.create(this._duration / 2)), this._inScene.runAction(e), this._outScene.runAction(t)
	}
}), cc.TransitionFlipX.create = function(e, t, n) {
	n == null && (n = cc.TRANSITION_ORIENTATION_RIGHT_OVER);
	var r = new cc.TransitionFlipX();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionFlipY = cc.TransitionSceneOriented.extend({
	onEnter: function() {
		this._super();
		var e, t;
		this._inScene.setVisible(!1);
		var n, r, i, s;
		this._orientation == cc.TRANSITION_ORIENTATION_UP_OVER ? (n = 90, r = 270, i = 90, s = 0) : (n = -90, r = 90, i = -90, s = 0), e = cc.Sequence.create(cc.DelayTime.create(this._duration / 2), cc.Show.create(), cc.OrbitCamera.create(this._duration / 2, 1, 0, r, n, 90, 0), cc.CallFunc.create(this.finish, this)), t = cc.Sequence.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, s, i, 90, 0), cc.Hide.create(), cc.DelayTime.create(this._duration / 2)), this._inScene.runAction(e), this._outScene.runAction(t)
	}
}), cc.TransitionFlipY.create = function(e, t, n) {
	n == null && (n = cc.TRANSITION_ORIENTATION_UP_OVER);
	var r = new cc.TransitionFlipY();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionFlipAngular = cc.TransitionSceneOriented.extend({
	onEnter: function() {
		this._super();
		var e, t;
		this._inScene.setVisible(!1);
		var n, r, i, s;
		this._orientation == cc.TRANSITION_ORIENTATION_RIGHT_OVER ? (n = 90, r = 270, i = 90, s = 0) : (n = -90, r = 90, i = -90, s = 0), e = cc.Sequence.create(cc.DelayTime.create(this._duration / 2), cc.Show.create(), cc.OrbitCamera.create(this._duration / 2, 1, 0, r, n, -45, 0), cc.CallFunc.create(this.finish, this)), t = cc.Sequence.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, s, i, 45, 0), cc.Hide.create(), cc.DelayTime.create(this._duration / 2)), this._inScene.runAction(e), this._outScene.runAction(t)
	}
}), cc.TransitionFlipAngular.create = function(e, t, n) {
	n == null && (n = cc.TRANSITION_ORIENTATION_RIGHT_OVER);
	var r = new cc.TransitionFlipAngular();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionZoomFlipX = cc.TransitionSceneOriented.extend({
	onEnter: function() {
		this._super();
		var e, t;
		this._inScene.setVisible(!1);
		var n, r, i, s;
		this._orientation == cc.TRANSITION_ORIENTATION_RIGHT_OVER ? (n = 90, r = 270, i = 90, s = 0) : (n = -90, r = 90, i = -90, s = 0), e = cc.Sequence.create(cc.DelayTime.create(this._duration / 2), cc.Spawn.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, r, n, 0, 0), cc.ScaleTo.create(this._duration / 2, 1), cc.Show.create()), cc.CallFunc.create(this.finish, this)), t = cc.Sequence.create(cc.Spawn.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, s, i, 0, 0), cc.ScaleTo.create(this._duration / 2, .5)), cc.Hide.create(), cc.DelayTime.create(this._duration / 2)), this._inScene.setScale(.5), this._inScene.runAction(e), this._outScene.runAction(t)
	}
}), cc.TransitionZoomFlipX.create = function(e, t, n) {
	n == null && (n = cc.TRANSITION_ORIENTATION_RIGHT_OVER);
	var r = new cc.TransitionZoomFlipX();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionZoomFlipY = cc.TransitionSceneOriented.extend({
	onEnter: function() {
		this._super();
		var e, t;
		this._inScene.setVisible(!1);
		var n, r, i, s;
		this._orientation == cc.TRANSITION_ORIENTATION_UP_OVER ? (n = 90, r = 270, i = 90, s = 0) : (n = -90, r = 90, i = -90, s = 0), e = cc.Sequence.create(cc.DelayTime.create(this._duration / 2), cc.Spawn.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, r, n, 90, 0), cc.ScaleTo.create(this._duration / 2, 1), cc.Show.create()), cc.CallFunc.create(this.finish, this)), t = cc.Sequence.create(cc.Spawn.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, s, i, 90, 0), cc.ScaleTo.create(this._duration / 2, .5)), cc.Hide.create(), cc.DelayTime.create(this._duration / 2)), this._inScene.setScale(.5), this._inScene.runAction(e), this._outScene.runAction(t)
	}
}), cc.TransitionZoomFlipY.create = function(e, t, n) {
	n == null && (n = cc.TRANSITION_ORIENTATION_UP_OVER);
	var r = new cc.TransitionZoomFlipY();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionZoomFlipAngular = cc.TransitionSceneOriented.extend({
	onEnter: function() {
		this._super();
		var e, t;
		this._inScene.setVisible(!1);
		var n, r, i, s;
		this._orientation == cc.TRANSITION_ORIENTATION_RIGHT_OVER ? (n = 90, r = 270, i = 90, s = 0) : (n = -90, r = 90, i = -90, s = 0), e = cc.Sequence.create(cc.DelayTime.create(this._duration / 2), cc.Spawn.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, r, n, -45, 0), cc.ScaleTo.create(this._duration / 2, 1), cc.Show.create()), cc.Show.create(), cc.CallFunc.create(this.finish, this)), t = cc.Sequence.create(cc.Spawn.create(cc.OrbitCamera.create(this._duration / 2, 1, 0, s, i, 45, 0), cc.ScaleTo.create(this._duration / 2, .5)), cc.Hide.create(), cc.DelayTime.create(this._duration / 2)), this._inScene.setScale(.5), this._inScene.runAction(e), this._outScene.runAction(t)
	}
}), cc.TransitionZoomFlipAngular.create = function(e, t, n) {
	n == null && (n = cc.TRANSITION_ORIENTATION_RIGHT_OVER);
	var r = new cc.TransitionZoomFlipAngular();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionFade = cc.TransitionScene.extend({
	_color: new cc.Color3B(),
	ctor: function() {},
	onEnter: function() {
		this._super();
		var e = cc.LayerColor.create(this._color);
		this._inScene.setVisible(!1), this.addChild(e, 2, cc.SCENE_FADE);
		var t = this.getChildByTag(cc.SCENE_FADE),
			n = cc.Sequence.create(cc.FadeIn.create(this._duration / 2), cc.CallFunc.create(this.hideOutShowIn, this), cc.FadeOut.create(this._duration / 2), cc.CallFunc.create(this.finish, this));
		t.runAction(n)
	},
	onExit: function() {
		this._super(), this.removeChildByTag(cc.SCENE_FADE, !1)
	},
	initWithDuration: function(e, t, n) {
		if (n == "undefined" || n == null) n = cc.black();
		return this._super(e, t) && (this._color.r = n.r, this._color.g = n.g, this._color.b = n.b, this._color.a = 0), !0
	}
}), cc.TransitionFade.create = function(e, t, n) {
	var r = new cc.TransitionFade();
	return r.initWithDuration(e, t, n), r
}, cc.TransitionCrossFade = cc.TransitionScene.extend({
	onEnter: function() {
		this._super();
		var e = new cc.Color4B(0, 0, 0, 0),
			t = cc.Director.getInstance().getWinSize(),
			n = cc.LayerColor.create(e),
			r = cc.RenderTexture.create(t.width, t.height);
		if (null == r) return;
		r.getSprite().setAnchorPoint(cc.p(.5, .5)), r.setPosition(cc.p(t.width / 2, t.height / 2)), r.setAnchorPoint(cc.p(.5, .5)), r.begin(), this._inScene.visit(), r.end();
		var i = cc.RenderTexture.create(t.width, t.height);
		i.getSprite().setAnchorPoint(cc.p(.5, .5)), i.setPosition(cc.p(t.width / 2, t.height / 2)), i.setAnchorPoint(cc.p(.5, .5)), i.begin(), this._outScene.visit(), i.end(), r.getSprite().setBlendFunc(gl.ONE, gl.ONE), i.getSprite().setBlendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA), n.addChild(r), n.addChild(i), r.getSprite().setOpacity(255), i.getSprite().setOpacity(255);
		var s = cc.Sequence.create(cc.FadeTo.create(this._duration, 0), cc.CallFunc.create(this.hideOutShowIn, this), cc.CallFunc.create(this.finish, this));
		i.getSprite().runAction(s), this.addChild(n, 2, cc.SCENE_FADE)
	},
	onExit: function() {
		this.removeChildByTag(cc.SCENE_FADE, !1), this._super()
	},
	draw: function() {}
}), cc.TransitionCrossFade.create = function(e, t) {
	var n = new cc.TransitionCrossFade();
	return n.initWithDuration(e, t), n
}, cc.TransitionTurnOffTiles = cc.TransitionScene.extend({
	_sceneOrder: function() {
		this._isInSceneOnTop = !1
	},
	onEnter: function() {
		this._super();
		var e = cc.Director.getInstance().getWinSize(),
			t = e.width / e.height,
			n = 12 * t,
			r = 12,
			i = cc.TurnOffTiles.create(cc.g(n, r), this._duration),
			s = this.easeActionWithAction(i);
		this._outScene.runAction(cc.Sequence.create(s, cc.CallFunc.create(this.finish, this), cc.StopGrid.create()))
	},
	easeActionWithAction: function(e) {
		return e
	}
}), cc.TransitionTurnOffTiles.create = function(e, t) {
	var n = new cc.TransitionTurnOffTiles();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionSplitCols = cc.TransitionScene.extend({
	onEnter: function() {
		this._super(), this._inScene.setVisible(!1);
		var e = this.action(),
			t = cc.Sequence.create(e, cc.CallFunc.create(this.hideOutShowIn, this), e.reverse());
		this.runAction(cc.Sequence.create(this.easeActionWithAction(t), cc.CallFunc.create(this.finish, this), cc.StopGrid.create()))
	},
	easeActionWithAction: function(e) {
		return cc.EaseInOut.create(e, 3)
	},
	action: function() {
		return cc.SplitCols.create(3, this._duration / 2)
	}
}), cc.TransitionSplitCols.create = function(e, t) {
	var n = new cc.TransitionSplitCols();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionSplitRows = cc.TransitionSplitCols.extend({
	action: function() {
		return cc.SplitRows.actionWithRows(3, this._duration / 2)
	}
}), cc.TransitionSplitRows.create = function(e, t) {
	var n = new cc.TransitionSplitRows();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionFadeTR = cc.TransitionScene.extend({
	_sceneOrder: function() {
		this._isInSceneOnTop = !1
	},
	onEnter: function() {
		this._super();
		var e = cc.Director.getInstance().getWinSize(),
			t = e.width / e.height,
			n = 12 * t,
			r = 12,
			i = this.actionWithSize(cc.g(n, r));
		this._outScene.runAction(cc.Sequence.create(this.easeActionWithAction(i), cc.CallFunc.create(this.finish, this), cc.StopGrid.create()))
	},
	easeActionWithAction: function(e) {
		return e
	},
	actionWithSize: function(e) {
		return cc.FadeOutTRTiles.create(e, this._duration)
	}
}), cc.TransitionFadeTR.create = function(e, t) {
	var n = new cc.TransitionFadeTR();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionFadeBL = cc.TransitionFadeTR.extend({
	actionWithSize: function(e) {
		return cc.FadeOutBLTiles.create(e, this._duration)
	}
}), cc.TransitionFadeBL.create = function(e, t) {
	var n = new cc.TransitionFadeBL();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionFadeUp = cc.TransitionFadeTR.extend({
	actionWithSize: function(e) {
		return cc.FadeOutUpTiles.create(e, this._duration)
	}
}), cc.TransitionFadeUp.create = function(e, t) {
	var n = new cc.TransitionFadeUp();
	return n != null && n.initWithDuration(e, t) ? n : null
}, cc.TransitionFadeDown = cc.TransitionFadeTR.extend({
	actionWithSize: function(e) {
		return cc.FadeOutDownTiles.create(e, this._duration)
	}
}), cc.TransitionFadeDown.create = function(e, t) {
	var n = new cc.TransitionFadeDown();
	return n != null && n.initWithDuration(e, t) ? n : null
};
cc.SPRITE_INDEX_NOT_INITIALIZED = -1, cc.generateTextureCacheForColor = function(e) {
	var t = e.width,
		n = e.height,
		r = [],
		i = document.createElement("canvas");
	i.width = t, i.height = n;
	var s = i.getContext("2d");
	s.drawImage(e, 0, 0);
	var o = document.createElement("canvas");
	o.width = t, o.height = n;
	var u = o.getContext("2d"),
		a = s.getImageData(0, 0, t, n).data;
	for (var f = 0; f < 3; f++) {
		var l = document.createElement("canvas");
		l.width = t, l.height = n;
		var c = l.getContext("2d");
		u.drawImage(e, 0, 0);
		var h = u.getImageData(0, 0, t, n),
			p = h.data;
		for (var d = 0; d < a.length; d += 4) p[d] = f === 0 ? a[d] : 0, p[d + 1] = f === 1 ? a[d + 1] : 0, p[d + 2] = f === 2 ? a[d + 2] : 0, p[d + 3] = a[d + 3];
		c.putImageData(h, 0, 0), r.push(l)
	}
	return r
}, cc.generateTintImage2 = function(e, t, n) {
	n || (n = cc.rect(0, 0, e.width, e.height));
	var r;
	t instanceof cc.Color4F ? r = cc.c4b(t.r * 255, t.g * 255, t.b * 255, t.a * 255) : r = cc.c4b(t.r, t.g, t.b, 50);
	var i = document.createElement("canvas"),
		s = i.getContext("2d");
	return i.width != n.size.width && (i.width = n.size.width), i.height != n.size.height && (i.height = n.size.height), s.save(), s.drawImage(e, n.origin.x, n.origin.y, n.size.width, n.size.height, 0, 0, n.size.width, n.size.height), s.globalCompositeOperation = "source-in", s.globalAlpha = r.a / 255, s.fillStyle = "rgb(" + r.r + "," + r.g + "," + r.b + ")", s.fillRect(0, 0, n.size.width, n.size.height), s.restore(), i
}, cc.generateTintImage = function(e, t, n, r, i) {
	r || (r = cc.rect(0, 0, e.width, e.height));
	var s;
	n instanceof cc.Color4F ? s = cc.c3b(n.r * 255, n.g * 255, n.b * 255) : s = n;
	var o = i || document.createElement("canvas");
	o.width = r.size.width, o.height = r.size.height;
	var u = o.getContext("2d");
	return u.globalCompositeOperation = "lighter", s.r > 0 && (u.globalAlpha = s.r / 255, u.drawImage(t[0], r.origin.x, r.origin.y, r.size.width, r.size.height, 0, 0, r.size.width, r.size.height)), s.g > 0 && (u.globalAlpha = s.g / 255, u.drawImage(t[1], r.origin.x, r.origin.y, r.size.width, r.size.height, 0, 0, r.size.width, r.size.height)), s.b > 0 && (u.globalAlpha = s.b / 255, u.drawImage(t[2], r.origin.x, r.origin.y, r.size.width, r.size.height, 0, 0, r.size.width, r.size.height)), o
}, cc.cutRotateImageToCanvas = function(e, t) {
	if (!e) return null;
	if (!t) return e;
	var n = document.createElement("canvas");
	n.width = t.size.width, n.height = t.size.height;
	var r = n.getContext("2d");
	return r.translate(n.width / 2, n.height / 2), r.rotate(-1.5707963267948966), r.drawImage(e, t.origin.x, t.origin.y, t.size.height, t.size.width, -t.size.height / 2, -t.size.width / 2, t.size.height, t.size.width), n
}, cc.TransformValues = function(e, t, n, r, i, s) {
	this.pos = e, this.scale = t, this.rotation = n, this.skew = r, this.ap = i, this.visible = s
}, cc.RENDER_IN_SUBPIXEL = function(e) {
	return cc.SPRITEBATCHNODE_RENDER_SUBPIXEL ? e : parseInt(e)
}, cc.Sprite = cc.Node.extend({
	RGBAProtocol: !0,
	_textureAtlas: null,
	_atlasIndex: 0,
	_batchNode: null,
	_dirty: null,
	_recursiveDirty: null,
	_hasChildren: null,
	_shouldBeHidden: !1,
	_transformToBatch: null,
	_blendFunc: {
		src: cc.BLEND_SRC,
		dst: cc.BLEND_DST
	},
	_texture: null,
	_originalTexture: null,
	_color: null,
	_colorized: !1,
	_rect: cc.rect(0, 0, 0, 0),
	_rectRotated: null,
	_offsetPosition: cc.p(0, 0),
	_unflippedOffsetPositionFromCenter: cc.PointZero(),
	_quad: cc.V3F_C4B_T2F_QuadZero(),
	colorUnmodified: null,
	_opacityModifyRGB: null,
	_flipX: null,
	_flipY: null,
	_opacity: 255,
	ctor: function(e) {
		this._super(), this._shouldBeHidden = !1, this._offsetPosition = cc.p(0, 0), this._unflippedOffsetPositionFromCenter = cc.p(0, 0), this._color = cc.white();
		if (e) if (typeof e == "string") {
			var t = cc.SpriteFrameCache.getInstance().getSpriteFrame(e);
			this.initWithSpriteFrame(t)
		} else if (typeof e == "object") if (e instanceof cc.SpriteFrame) this.initWithSpriteFrame(e);
		else if (e instanceof cc.SpriteBatchNode) {
			if (arguments.length > 1) {
				var n = arguments[1];
				n instanceof cc.Rect && this.initWithBatchNode(e, n)
			}
		} else e instanceof HTMLImageElement || e instanceof HTMLCanvasElement ? this.initWithTexture(e) : e instanceof cc.Texture2D && this.initWithTexture(e)
	},
	isDirty: function() {
		return this._dirty
	},
	setDirty: function(e) {
		this._dirty = e
	},
	getQuad: function() {
		return this._quad
	},
	isTextureRectRotated: function() {
		return this._rectRotated
	},
	getAtlasIndex: function() {
		return this._atlasIndex
	},
	setAtlasIndex: function(e) {
		this._atlasIndex = e
	},
	getTextureRect: function() {
		return cc.rect(this._rect.origin.x, this._rect.origin.y, this._rect.size.width, this._rect.size.height)
	},
	getTextureAtlas: function(e) {
		return this._textureAtlas
	},
	setTextureAtlas: function(e) {
		this._textureAtlas = e
	},
	getSpriteBatchNode: function() {
		return this._batchNode
	},
	setSpriteBatchNode: function(e) {
		this._batchNode = e
	},
	getOffsetPosition: function() {
		return cc.p(this._offsetPosition.x, this._offsetPosition.y)
	},
	getBlendFunc: function() {
		return this._blendFunc
	},
	_isLighterMode: !1,
	setBlendFunc: function(e, t) {
		arguments.length == 1 ? this._blendFunc = e : this._blendFunc = {
			src: e,
			dst: t
		}, this._isLighterMode = this._blendFunc && this._blendFunc.src == gl.SRC_ALPHA && this._blendFunc.dst == gl.ONE
	},
	init: function() {
		this._super(), this._dirty = this._recursiveDirty = !1, this._opacityModifyRGB = !0, this._opacity = 255, this._color = cc.white(), this._colorUnmodified = cc.white(), this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, this.setTexture(null), this._flipX = this._flipY = !1, this.setAnchorPoint(cc.p(.5, .5)), this._offsetPosition = cc.PointZero(), this._hasChildren = !1;
		var e = new cc.Color4B(255, 255, 255, 255);
		return this._quad.bl.colors = e, this._quad.br.colors = e, this._quad.tl.colors = e, this._quad.tr.colors = e, this.setTextureRect(cc.RectZero(), !1, cc.SizeZero()), !0
	},
	initWithTexture: function(e, t, n) {
		var r = arguments.length;
		if (r == 0) throw "Sprite.initWithTexture(): Argument must be non-nil ";
		n = n || !1, this._batchNode = null, this._recursiveDirty = !1, this.setDirty(!1), this._opacityModifyRGB = !0, this._opacity = 255, this._color = cc.white(), this._colorUnmodified = cc.white(), this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, this._flipX = this._flipY = !1, this.setAnchorPoint(cc.p(.5, .5)), this._offsetPosition = cc.p(0, 0), this._hasChildren = !1;
		var i = new cc.Color4B(255, 255, 255, 255);
		this._quad.bl.colors = i, this._quad.br.colors = i, this._quad.tl.colors = i, this._quad.tr.colors = i;
		if (!t) {
			t = cc.rect(0, 0, 0, 0);
			if (e instanceof cc.Texture2D) t.size = e.getContentSize();
			else if (e instanceof HTMLImageElement || e instanceof HTMLCanvasElement) t.size = cc.size(e.width, e.height)
		}
		return cc.renderContextType == cc.CANVAS && (this._originalTexture = e), this.setTexture(e), this.setTextureRect(t, n, t.size), this.setBatchNode(null), !0
	},
	initWithFile: function(e, t) {
		cc.Assert(e != null, "Sprite#initWithFile():Invalid filename for sprite");
		var n = this,
			r = cc.TextureCache.getInstance().textureForKey(cc.FileUtils.getInstance().fullPathForFilename(e));
		if (!r) {
			this._visible = !1;
			var i = new Image();
			i.crossOrigin = "anonymous";
			return i.addEventListener("load", function() {
				t || (t = cc.rect(0, 0, i.width, i.height)), n.initWithTexture(i, t), cc.TextureCache.getInstance().cacheImage(e, i), n._visible = !0
			}), i.addEventListener("error", function() {
				cc.log("load failure:" + e)
			}), i.src = e, !0
		}
		if (r) {
			if (!t) {
				t = cc.rect(0, 0, 0, 0);
				if (r instanceof cc.Texture2D) t.size = r.getContentSize();
				else if (r instanceof HTMLImageElement || r instanceof HTMLCanvasElement) t.size = cc.size(r.width, r.height)
			}
			return this.initWithTexture(r, t)
		}
		return !1
	},
	initWithSpriteFrame: function(e) {
		cc.Assert(e != null, "");
		var t = this.initWithTexture(e.getTexture(), e.getRect());
		return this.setDisplayFrame(e), t
	},
	initWithSpriteFrameName: function(e) {
		cc.Assert(e != null, "");
		var t = cc.SpriteFrameCache.getInstance().getSpriteFrame(e);
		return this.initWithSpriteFrame(t)
	},
	useBatchNode: function(e) {
		this._textureAtlas = e.getTextureAtlas(), this._batchNode = e
	},
	setTextureRect: function(e, t, n) {
		this._rectRotated = t || !1, n = n || e.size, this.setContentSize(n), this.setVertexRect(e), this._setTextureCoords(e);
		var r = this._unflippedOffsetPositionFromCenter;
		this._offsetPosition.x = r.x + (this._contentSize.width - this._rect.size.width) / 2, this._offsetPosition.y = r.y + (this._contentSize.height - this._rect.size.height) / 2;
		if (this._batchNode) this._dirty = !0;
		else {
			var i = 0 + this._offsetPosition.x,
				s = 0 + this._offsetPosition.y,
				o = i + this._rect.size.width,
				u = s + this._rect.size.height;
			this._quad.bl.vertices = cc.vertex3(i, s, 0), this._quad.br.vertices = cc.vertex3(o, s, 0), this._quad.tl.vertices = cc.vertex3(i, u, 0), this._quad.tr.vertices = cc.vertex3(o, u, 0)
		}
	},
	setVertexRect: function(e) {
		this._rect = e
	},
	_setTextureCoords: function(e) {
		if (cc.renderContextType == cc.WEBGL) {
			e = cc.RECT_POINTS_TO_PIXELS(e);
			var t = this._batchNode ? this._textureAtlas.getTexture() : this._texture;
			if (!t) return;
			var n = t.getPixelsWide(),
				r = t.getPixelsHigh(),
				i, s, o, u;
			this._rectRotated ? (cc.FIX_ARTIFACTS_BY_STRECHING_TEXEL ? (i = (2 * e.origin.x + 1) / (2 * n), s = i + (e.size.height * 2 - 2) / (2 * n), o = (2 * e.origin.y + 1) / (2 * r), u = o + (e.size.width * 2 - 2) / (2 * r)) : (i = e.origin.x / n, s = (e.origin.x + e.size.height) / n, o = e.origin.y / r, u = (e.origin.y + e.size.width) / r), this._flipX && cc.SWAP(o, u), this._flipY && cc.SWAP(i, s), this._quad.bl.texCoords.u = i, this._quad.bl.texCoords.v = o, this._quad.br.texCoords.u = i, this._quad.br.texCoords.v = u, this._quad.tl.texCoords.u = s, this._quad.tl.texCoords.v = o, this._quad.tr.texCoords.u = s, this._quad.tr.texCoords.v = u) : (cc.FIX_ARTIFACTS_BY_STRECHING_TEXEL ? (i = (2 * e.origin.x + 1) / (2 * n), s = i + (e.size.width * 2 - 2) / (2 * n), o = (2 * e.origin.y + 1) / (2 * r), u = o + (e.size.height * 2 - 2) / (2 * r)) : (i = e.origin.x / n, s = (e.origin.x + e.size.width) / n, o = e.origin.y / r, u = (e.origin.y + e.size.height) / r), this._flipX && cc.SWAP(i, s), this._flipY && cc.SWAP(o, u), this._quad.bl.texCoords.u = i, this._quad.bl.texCoords.v = u, this._quad.br.texCoords.u = s, this._quad.br.texCoords.v = u, this._quad.tl.texCoords.u = i, this._quad.tl.texCoords.v = o, this._quad.tr.texCoords.u = s, this._quad.tr.texCoords.v = o)
		}
	},
	updateTransform: function() {
		cc.Assert(this._batchNode, "updateTransform is only valid when cc.Sprite is being rendered using an cc.SpriteBatchNode");
		if (this.isDirty()) {
			if (!this._visible || this._parent && this._parent != this._batchNode && this._parent._shouldBeHidden) this._quad.br.vertices = this._quad.tl.vertices = this._quad.tr.vertices = this._quad.bl.vertices = cc.vertex3(0, 0, 0), this._shouldBeHidden = !0;
			else {
				this._shouldBeHidden = !1, !this._parent || this._parent == this._batchNode ? this._transformToBatch = this.nodeToParentTransform() : (cc.Assert(this._parent instanceof cc.Sprite, "Logic error in CCSprite. Parent must be a CCSprite"), this._transformToBatch = cc.AffineTransformConcat(this.nodeToParentTransform(), this._parent._transformToBatch));
				var e = this._rect.size,
					t = this._offsetPosition.x,
					n = this._offsetPosition.y,
					r = t + e.width,
					i = n + e.height,
					s = this._transformToBatch.tx,
					o = this._transformToBatch.ty,
					u = this._transformToBatch.a,
					a = this._transformToBatch.b,
					f = this._transformToBatch.d,
					l = -this._transformToBatch.c,
					c = t * u - n * l + s,
					h = t * a + n * f + o,
					p = r * u - n * l + s,
					d = r * a + n * f + o,
					v = r * u - i * l + s,
					m = r * a + i * f + o,
					g = t * u - i * l + s,
					y = t * a + i * f + o;
				this._quad.bl.vertices = cc.vertex3(cc.RENDER_IN_SUBPIXEL(c), cc.RENDER_IN_SUBPIXEL(h), this._vertexZ), this._quad.br.vertices = cc.vertex3(cc.RENDER_IN_SUBPIXEL(p), cc.RENDER_IN_SUBPIXEL(d), this._vertexZ), this._quad.tl.vertices = cc.vertex3(cc.RENDER_IN_SUBPIXEL(g), cc.RENDER_IN_SUBPIXEL(y), this._vertexZ), this._quad.tr.vertices = cc.vertex3(cc.RENDER_IN_SUBPIXEL(v), cc.RENDER_IN_SUBPIXEL(m), this._vertexZ)
			}
			this._textureAtlas.updateQuad(this._quad, this._atlasIndex), this._recursiveDirty = !1, this.setDirty(!1)
		}
		this._hasChildren && this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.updateTransform);
		if (cc.SPRITE_DEBUG_DRAW) {
			var b = [cc.p(this._quad.bl.vertices.x, this._quad.bl.vertices.y), cc.p(this._quad.br.vertices.x, this._quad.br.vertices.y), cc.p(this._quad.tr.vertices.x, this._quad.tr.vertices.y), cc.p(this._quad.tl.vertices.x, this._quad.tl.vertices.y)];
			cc.drawingUtil.drawPoly(b, 4, !0)
		}
	},
	_getTransformValues: function(e) {
		return e.pos = this._position, e.scale.x = this._scaleX, e.scale.y = this._scaleY, e.rotation = this._rotation, e.skew.x = this._skewX, e.skew.y = this._skewY, e.ap = this._anchorPointInPoints, e.visible = this._visible, e
	},
	draw: function(e) {
		var t = e || cc.renderContext;
		this._isLighterMode && (t.globalCompositeOperation = "lighter"), t.globalAlpha = this._opacity / 255;
		var n = 0,
			r = 0;
		this._flipX && (n = 0 | this._contentSize.width / 2 - this._anchorPointInPoints.x, t.translate(n, 0), t.scale(-1, 1)), this._flipY && (r = -(0 | this._contentSize.height / 2 - this._anchorPointInPoints.y), t.translate(0, r), t.scale(1, -1));
		var i = 0 | -this._anchorPointInPoints.x - n + this._offsetPosition.x,
			s = 0 | -this._anchorPointInPoints.y + r + this._offsetPosition.y;
		this._texture ? this._texture instanceof HTMLImageElement ? this._contentSize.width == 0 && this._contentSize.height == 0 ? (this.setContentSize(cc.size(this._texture.width, this._texture.height)), this._rect.size.width = this._texture.width, this._rect.size.height = this._texture.height, t.drawImage(this._texture, i, -(s + this._texture.height))) : t.drawImage(this._texture, this._rect.origin.x, this._rect.origin.y, this._rect.size.width, this._rect.size.height, i, -(s + this._rect.size.height), this._rect.size.width, this._rect.size.height) : this._contentSize.width == 0 && this._contentSize.height == 0 ? (this.setContentSize(cc.size(this._texture.width, this._texture.height)), this._rect.size.width = this._texture.width, this._rect.size.height = this._texture.height, t.drawImage(this._texture, i, -(s + this._texture.height))) : this._colorized ? t.drawImage(this._texture, 0, 0, this._rect.size.width, this._rect.size.height, i, -(s + this._rect.size.height), this._rect.size.width, this._rect.size.height) : t.drawImage(this._texture, this._rect.origin.x, this._rect.origin.y, this._rect.size.width, this._rect.size.height, i, -(s + this._rect.size.height), this._rect.size.width, this._rect.size.height) : (t.fillStyle = "rgba(" + this._color.r + "," + this._color.g + "," + this._color.b + ",1)", t.fillRect(i, s, this._contentSize.width, this._contentSize.height));
		if (cc.SPRITE_DEBUG_DRAW == 1) {
			t.strokeStyle = "rgba(0,255,0,1)";
			var o = [cc.p(i, s), cc.p(i + this._rect.size.width, s), cc.p(i + this._rect.size.width, s + this._rect.size.height), cc.p(i, s + this._rect.size.height)];
			cc.drawingUtil.drawPoly(o, 4, !0)
		} else if (cc.SPRITE_DEBUG_DRAW == 2) {
			t.strokeStyle = "rgba(0,255,0,1)";
			var u = this._rect.size,
				a = this.getOffsetPosition(),
				f = [cc.p(a.x, a.y), cc.p(a.x + u.width, a.y), cc.p(a.x + u.width, a.y + u.height), cc.p(a.x, a.y + u.height)];
			cc.drawingUtil.drawPoly(f, 4, !0)
		}
		cc.g_NumberOfDraws++
	},
	_drawForWebGL: function(e) {
		var t = e;
		cc.Assert(!this._batchNode, "If cc.Sprite is being rendered by cc.SpriteBatchNode, cc.Sprite#draw SHOULD NOT be called"), cc.NODE_DRAW_SETUP(this), this._texture;
		var n = this._quad;
		if (cc.SPRITE_DEBUG_DRAW == 1) {
			var r = [cc.p(this._quad.tl.vertices.x, this._quad.tl.vertices.y), cc.p(this._quad.bl.vertices.x, this._quad.bl.vertices.y), cc.p(this._quad.br.vertices.x, this._quad.br.vertices.y), cc.p(this._quad.tr.vertices.x, this._quad.tr.vertices.y)];
			cc.drawingUtil.drawPoly(r, 4, !0)
		} else if (cc.SPRITE_DEBUG_DRAW == 2) {
			var i = this.getTextureRect().size,
				s = this.getOffsetPosition(),
				o = [cc.p(s.x, s.y), cc.p(s.x + i.width, s.y), cc.p(s.x + i.width, s.y + i.height), cc.p(s.x, s.y + i.height)];
			cc.drawingUtil.drawPoly(o, 4, !0)
		}
		cc.g_NumberOfDraws++
	},
	addChild: function(e, t, n) {
		var r = arguments.length;
		switch (r) {
		case 1:
			this._super(e);
			break;
		case 2:
			this._super(e, t);
			break;
		case 3:
			cc.Assert(e != null, "Argument must be non-NULL"), cc.renderContextType == cc.WEBGL && this._batchNode && (cc.Assert(e instanceof cc.Sprite, "cc.Sprite only supports cc.Sprites as children when using cc.SpriteBatchNode"), cc.Assert(e.getTexture().getName() == this._textureAtlas.getTexture().getName(), ""), this._batchNode.appendChild(e), this._reorderChildDirty || this._setReorderChildDirtyRecursively()), this._super(e, t, n), this._hasChildren = !0;
			break;
		default:
			throw "Sprite.addChild():Argument must be non-nil "
		}
	},
	sortAllChildren: function() {
		if (this._reorderChildDirty) {
			var e, t = null;
			for (var n = 1; n < this._children.length; n++) {
				t = this._children[n], e = n - 1;
				while (e >= 0 && (t.getZOrder() < this._children[e].getZOrder() || t.getZOrder() == this._children[e].getZOrder() && t.getOrderOfArrival() < this._children[e].getOrderOfArrival())) this._children[e + 1] = this._children[e], e -= 1;
				this._children[e + 1] = t
			}
			this._batchNode && this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.sortAllChildren), this._reorderChildDirty = !1
		}
	},
	reorderChild: function(e, t) {
		cc.Assert(e != null, "child is null"), cc.Assert(this._children.indexOf(e) > -1, "this child is not in children list");
		if (t == e.getZOrder()) return;
		this._batchNode && this._reorderChildDirty && (this._setReorderChildDirtyRecursively(), this._batchNode.reorderBatch(!0)), this._super(e, t)
	},
	removeChild: function(e, t) {
		this._batchNode && this._batchNode.removeSpriteFromAtlas(e), this._super(e, t)
	},
	removeAllChildren: function(e) {
		if (this._batchNode && this._children != null) for (var t = 0; t < this._children.length; t++) this._children[t] instanceof cc.Sprite && this._batchNode.removeSpriteFromAtlas(this._children[t]);
		this._super(e), this._hasChildren = !1
	},
	setDirtyRecursively: function(e) {
		this._recursiveDirty = e, this.setDirty(e);
		if (this._children != null) for (var t = 0; t < this._children.length; t++) this._children[t] instanceof cc.Sprite && this._children[t].setDirtyRecursively(!0)
	},
	SET_DIRTY_RECURSIVELY: function() {
		this._batchNode && !this._recursiveDirty && (this._recursiveDirty = !0, this._dirty = !0, this._hasChildren && this.setDirtyRecursively(!0))
	},
	setPosition: function(e) {
		arguments.length >= 2 ? cc.Node.prototype.setPosition.call(this, e, arguments[1]) : cc.Node.prototype.setPosition.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setRotation: function(e) {
		cc.Node.prototype.setRotation.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setSkewX: function(e) {
		cc.Node.prototype.setSkewX.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setSkewY: function(e) {
		cc.Node.prototype.setSkewY.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setScaleX: function(e) {
		cc.Node.prototype.setScaleX.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setScaleY: function(e) {
		cc.Node.prototype.setScaleY.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setScale: function(e, t) {
		cc.Node.prototype.setScale.call(this, e, t), this.SET_DIRTY_RECURSIVELY()
	},
	setVertexZ: function(e) {
		cc.Node.prototype.setVertexZ.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setAnchorPoint: function(e) {
		cc.Node.prototype.setAnchorPoint.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	setVisible: function(e) {
		cc.Node.prototype.setVisible.call(this, e), this.SET_DIRTY_RECURSIVELY()
	},
	ignoreAnchorPointForPosition: function(e) {
		cc.Assert(!this._batchNode, "ignoreAnchorPointForPosition is invalid in cc.Sprite"), this._super(e)
	},
	setFlipX: function(e) {
		this._flipX != e && (this._flipX = e, this.setTextureRect(this._rect, this._rectRotated, this._contentSize), this.setNodeDirty())
	},
	setFlipY: function(e) {
		this._flipY != e && (this._flipY = e, this.setNodeDirty())
	},
	isFlippedX: function() {
		return this._flipX
	},
	isFlippedY: function() {
		return this._flipY
	},
	updateColor: function() {
		var e = new cc.Color4B(this._color.r, this._color.g, this._color.b, this._opacity);
		this._quad.bl.colors = e, this._quad.br.colors = e, this._quad.tl.colors = e, this._quad.tr.colors = e, this._batchNode && (this._atlasIndex != cc.SPRITE_INDEX_NOT_INITIALIZED ? this._textureAtlas.updateQuad(this._quad, this._atlasIndex) : this._dirty = !0)
	},
	getOpacity: function() {
		return this._opacity
	},
	setOpacity: function(e) {
		this._opacity = e, this.setNodeDirty();
		return
	},
	getColor: function() {
		return this._opacityModifyRGB ? new cc.Color3B(this._colorUnmodified) : new cc.Color3B(this._color)
	},
	setColor: function(e) {
		if (this._color.r == e.r && this._color.g == e.g && this._color.b == e.b) return;
		this._color = this._colorUnmodified = new cc.Color3B(e.r, e.g, e.b), this._changeTextureColor(), this.updateColor(), this.setNodeDirty()
	},
	_changeTextureColor: function() {
		if (this.getTexture() && cc.renderContextType === cc.CANVAS) {
			var e = cc.TextureCache.getInstance().getTextureColors(this._originalTexture);
			if (e) {
				this._colorized = !0;
				if (this._texture instanceof HTMLCanvasElement && !this._rectRotated) cc.generateTintImage(this.getTexture(), e, this._color, this.getTextureRect(), this._texture);
				else {
					var t = cc.generateTintImage(this.getTexture(), e, this._color, this.getTextureRect());
					this.setTexture(t)
				}
			}
		}
	},
	setOpacityModifyRGB: function(e) {
		var t = this._color;
		this._opacityModifyRGB = e, this._color = t
	},
	isOpacityModifyRGB: function() {
		return this._opacityModifyRGB
	},
	setDisplayFrame: function(e) {
		this.setNodeDirty(), this._unflippedOffsetPositionFromCenter = e.getOffset();
		var t = e.getTexture();
		t != this._texture && this.setTexture(t), this._rectRotated = e.isRotated(), this._rectRotated && (this._originalTexture = t), this.setTextureRect(e.getRect(), this._rectRotated, e.getOriginalSize()), (this._color.r !== 255 || this._color.g !== 255 || this._color.b !== 255) && this._changeTextureColor()
	},
	setDisplayFrameWithAnimationName: function(e, t) {
		cc.Assert(e, "cc.Sprite#setDisplayFrameWithAnimationName. animationName must not be null");
		var n = cc.AnimationCache.getInstance().getAnimation(e);
		cc.Assert(n, "cc.Sprite#setDisplayFrameWithAnimationName: Frame not found");
		var r = n.getFrames()[t];
		cc.Assert(r, "cc.Sprite#setDisplayFrame. Invalid frame"), this.setDisplayFrame(r.getSpriteFrame())
	},
	isFrameDisplayed: function(e) {
		return cc.renderContextType == cc.CANVAS ? e.getTexture() != this._texture ? !1 : cc.Rect.CCRectEqualToRect(e.getRect(), this._rect) : cc.Rect.CCRectEqualToRect(e.getRect(), this._rect) && e.getTexture().getName() == this._texture.getName() && cc.Point.CCPointEqualToPoint(e.getOffset(), this._unflippedOffsetPositionFromCenter)
	},
	displayFrame: function() {
		return cc.renderContextType == cc.CANVAS ? cc.SpriteFrame._frameWithTextureForCanvas(this._texture, cc.RECT_POINTS_TO_PIXELS(this._rect), this._rectRotated, this._unflippedOffsetPositionFromCenter, cc.SIZE_POINTS_TO_PIXELS(this._contentSize)) : cc.SpriteFrame.createWithTexture(this._texture, cc.RECT_POINTS_TO_PIXELS(this._rect), this._rectRotated, this._unflippedOffsetPositionFromCenter, cc.SIZE_POINTS_TO_PIXELS(this._contentSize))
	},
	getBatchNode: function() {
		return this._batchNode
	},
	setBatchNode: function(e) {
		this._batchNode = e;
		if (!this._batchNode) {
			this._atlasIndex = cc.SPRITE_INDEX_NOT_INITIALIZED, this.setTextureAtlas(null), this._recursiveDirty = !1, this.setDirty(!1);
			var t = this._offsetPosition.x,
				n = this._offsetPosition.y,
				r = t + this._rect.size.width,
				i = n + this._rect.size.height;
			this._quad.bl.vertices = cc.vertex3(t, n, 0), this._quad.br.vertices = cc.vertex3(r, n, 0), this._quad.tl.vertices = cc.vertex3(t, i, 0), this._quad.tr.vertices = cc.vertex3(r, i, 0)
		} else this._transformToBatch = cc.AffineTransformIdentity(), this.setTextureAtlas(this._batchNode.getTextureAtlas())
	},
	_updateBlendFunc: function() {
		cc.renderContextType == cc.WEBGL && (cc.Assert(!this._batchNode, "cc.Sprite: _updateBlendFunc doesn't work when the sprite is rendered using a cc.SpriteSheet"), !this._texture || !this._texture.hasPremultipliedAlpha() ? (this._blendFunc.src = gl.SRC_ALPHA, this._blendFunc.dst = gl.ONE_MINUS_SRC_ALPHA, this.setOpacityModifyRGB(!1)) : (this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, this.setOpacityModifyRGB(!0)))
	},
	_setReorderChildDirtyRecursively: function() {
		if (!this._reorderChildDirty) {
			this._reorderChildDirty = !0;
			var e = this._parent;
			while (e && e != this._batchNode) e._setReorderChildDirtyRecursively(), e = e.getParent()
		}
	},
	setTexture: function(e) {
		cc.Assert(!e || e instanceof cc.Texture2D || e instanceof HTMLImageElement || e instanceof HTMLCanvasElement, "setTexture expects a CCTexture2D. Invalid argument");
		if (cc.renderContextType != cc.CANVAS) cc.Assert(!this._batchNode, "cc.Sprite: Batched sprites should use the same texture as the batchnode"), !this._batchNode && this._texture != e && (this._texture = e, this._updateBlendFunc());
		else if (this._texture != e) if (e instanceof HTMLImageElement) {
			if (!this._rect || cc.rectEqualToRect(this._rect, cc.RectZero())) this._rect = cc.rect(0, 0, e.width, e.height);
			this._texture = e, this._originalTexture = e
		} else this._texture = e, this._updateBlendFunc()
	},
	getTexture: function() {
		return this._texture
	}
}), cc.Sprite.createWithTexture = function(e, t, n) {
	var r = arguments.length,
		i = new cc.Sprite();
	switch (r) {
	case 1:
		if (i && i.initWithTexture(e)) return i;
		return null;
	case 2:
		if (i && i.initWithTexture(e, t)) return i;
		return null;
	case 3:
		return cc.Assert(0, ""), null;
	default:
		throw "Sprite.createWithTexture(): Argument must be non-nil "
	}
}, cc.Sprite.create = function(e, t) {
	var n = arguments.length,
		r = new cc.Sprite();
	return n === 0 ? r.init() ? r : null : n < 2 ? r && r.initWithFile(e) ? r : null : r && r.initWithFile(e, t) ? r : null
}, cc.Sprite.createWithSpriteFrameName = function(e) {
	var t = null;
	if (typeof e != "string") return cc.log("Invalid argument. Expecting string."), null;
	t = cc.SpriteFrameCache.getInstance().getSpriteFrame(e);
	if (!t) return cc.log("Invalid spriteFrameName: " + e), null;
	var n = new cc.Sprite();
	return n && n.initWithSpriteFrame(t) ? n : null
}, cc.Sprite.createWithSpriteFrame = function(e) {
	var t = new cc.Sprite();
	return t && t.initWithSpriteFrame(e) ? t : null
};
cc.AnimationFrame = cc.Class.extend({
	_spriteFrame: null,
	_delayPerUnit: 0,
	_userInfo: null,
	ctor: function() {
		this._delayPerUnit = 0
	},
	copyWithZone: function(e) {
		return cc.clone(this)
	},
	copy: function(e) {
		var t = new cc.AnimationFrame();
		return t.initWithSpriteFrame(this._spriteFrame, this._delayPerUnit, this._userInfo), t
	},
	initWithSpriteFrame: function(e, t, n) {
		return this.setSpriteFrame(e), this.setDelayUnits(t), this.setUserInfo(n), !0
	},
	getSpriteFrame: function() {
		return this._spriteFrame
	},
	setSpriteFrame: function(e) {
		this._spriteFrame = e
	},
	getDelayUnits: function() {
		return this._delayPerUnit
	},
	setDelayUnits: function(e) {
		this._delayPerUnit = e
	},
	getUserInfo: function() {
		return this._userInfo
	},
	setUserInfo: function(e) {
		this._userInfo = e
	}
}), cc.Animation = cc.Class.extend({
	_frames: null,
	_loops: 0,
	_restoreOriginalFrame: !1,
	_duration: 0,
	_delayPerUnit: 0,
	_totalDelayUnits: 0,
	ctor: function() {
		this._frames = []
	},
	getFrames: function() {
		return this._frames
	},
	setFrames: function(e) {
		this._frames = e
	},
	addSpriteFrame: function(e) {
		var t = new cc.AnimationFrame();
		t.initWithSpriteFrame(e, 1, null), this._frames.push(t), this._totalDelayUnits++
	},
	addSpriteFrameWithFile: function(e) {
		var t = cc.TextureCache.getInstance().addImage(e),
			n = cc.RectZero();
		t instanceof HTMLImageElement || t instanceof HTMLCanvasElement ? n.size = cc.size(t.width, t.height) : n.size = t.getContentSize();
		var r = cc.SpriteFrame.createWithTexture(t, n);
		this.addSpriteFrame(r)
	},
	addSpriteFrameWithTexture: function(e, t) {
		var n = cc.SpriteFrame.createWithTexture(e, t);
		this.addSpriteFrame(n)
	},
	initWithAnimationFrames: function(e, t, n) {
		cc.ArrayVerifyType(e, cc.AnimationFrame), this._delayPerUnit = t, this._loops = n, this.setFrames([]);
		for (var r = 0; r < e.length; r++) {
			var i = e[r];
			this._frames.push(i), this._totalDelayUnits += i.getDelayUnits()
		}
		return !0
	},
	copyWithZone: function(e) {
		var t = new cc.Animation();
		return t.initWithAnimationFrames(this._frames, this._delayPerUnit, this._loops), t.setRestoreOriginalFrame(this._restoreOriginalFrame), t
	},
	copy: function(e) {
		return this.copyWithZone(null)
	},
	getLoops: function() {
		return this._loops
	},
	setLoops: function(e) {
		this._loops = e
	},
	setRestoreOriginalFrame: function(e) {
		this._restoreOriginalFrame = e
	},
	getRestoreOriginalFrame: function() {
		return this._restoreOriginalFrame
	},
	getDuration: function() {
		return this._totalDelayUnits * this._delayPerUnit
	},
	getDelayPerUnit: function() {
		return this._delayPerUnit
	},
	setDelayPerUnit: function(e) {
		this._delayPerUnit = e
	},
	getTotalDelayUnits: function() {
		return this._totalDelayUnits
	},
	initWithSpriteFrames: function(e, t) {
		cc.ArrayVerifyType(e, cc.SpriteFrame), this._loops = 1, t = t || 0, this._delayPerUnit = t;
		var n = [];
		this.setFrames(n);
		if (e) for (var r = 0; r < e.length; r++) {
			var i = e[r],
				s = new cc.AnimationFrame();
			s.initWithSpriteFrame(i, 1, null), this._frames.push(s), this._totalDelayUnits++
		}
		return !0
	}
}), cc.Animation.create = function(e, t, n) {
	var r = arguments.length,
		i = new cc.Animation();
	return r == 0 ? i.initWithSpriteFrames(null, 0) : r == 2 ? (t = t || 0, i.initWithSpriteFrames(e, t)) : r == 3 && i.initWithAnimationFrames(e, t, n), i
}, cc.Animation.createWithAnimationFrames = function(e, t, n) {
	var r = new cc.Animation();
	return r.initWithAnimationFrames(e, t, n), r
};
cc.AnimationCache = cc.Class.extend({
	addAnimation: function(e, t) {
		this._animations[t] = e
	},
	removeAnimation: function(e) {
		if (!e) return;
		this._animations.hasOwnProperty(e) && delete this._animations[e]
	},
	getAnimation: function(e) {
		return this._animations.hasOwnProperty(e) ? this._animations[e] : null
	},
	addAnimationsWithDictionary: function(e) {
		var t = e.animations;
		if (!t) {
			cc.log("cocos2d: cc.AnimationCache: No animations were found in provided dictionary.");
			return
		}
		var n = 1,
			r = e.properties;
		if (r) {
			n = r["format"] != null ? parseInt(r.format) : n;
			var i = r.spritesheets;
			for (var s = 0; s < i.length; s++) cc.SpriteFrameCache.getInstance().addSpriteFrames(i[s])
		}
		switch (n) {
		case 1:
			this._parseVersion1(t);
			break;
		case 2:
			this._parseVersion2(t);
			break;
		default:
			cc.Assert(!1, "Invalid animation format")
		}
	},
	addAnimations: function(e) {
		cc.Assert(e, "Invalid texture file name");
		var t = cc.FileUtils.getInstance().fullPathFromRelativePath(e),
			n = cc.FileUtils.getInstance().dictionaryWithContentsOfFileThreadSafe(t);
		cc.Assert(n, "cc.AnimationCache: File could not be found"), this.addAnimationsWithDictionary(n)
	},
	_parseVersion1: function(e) {
		var t = cc.SpriteFrameCache.getInstance();
		for (var n in e) {
			var r = e[n],
				i = r.frames,
				s = parseFloat(r.delay) || 0,
				o = null;
			if (!i) {
				cc.log("cocos2d: cc.AnimationCache: Animation '" + n + "' found in dictionary without any frames - cannot add to animation cache.");
				continue
			}
			var u = [];
			for (var a = 0; a < i.length; a++) {
				var f = t.getSpriteFrame(i[a]);
				if (!f) {
					cc.log("cocos2d: cc.AnimationCache: Animation '" + n + "' refers to frame '" + i[a] + "' which is not currently in the cc.SpriteFrameCache. This frame will not be added to the animation.");
					continue
				}
				var l = new cc.AnimationFrame();
				l.initWithSpriteFrame(f, 1, null), u.push(l)
			}
			if (u.length === 0) {
				cc.log("cocos2d: cc.AnimationCache: None of the frames for animation '" + n + "' were found in the cc.SpriteFrameCache. Animation is not being added to the Animation Cache.");
				continue
			}
			u.length != i.length && cc.log("cocos2d: cc.AnimationCache: An animation in your dictionary refers to a frame which is not in the cc.SpriteFrameCache. Some or all of the frames for the animation '" + n + "' may be missing."), o = cc.Animation.createWithAnimationFrames(u, s, 1), cc.AnimationCache.getInstance().addAnimation(o, n)
		}
	},
	_parseVersion2: function(e) {
		var t = cc.SpriteFrameCache.getInstance();
		for (var n in e) {
			var r = e[n],
				i = parseInt(r.loops),
				s = i == null ? 1 : i,
				o = r.restoreOriginalFrame && r["restoreOriginalFrame"] == 1 ? !0 : !1,
				u = r.frames;
			if (!u) {
				cc.log("cocos2d: CCAnimationCache: Animation '" + n + "' found in dictionary without any frames - cannot add to animation cache.");
				continue
			}
			var a = [];
			for (var f = 0; f < u.length; f++) {
				var l = u[f],
					c = l.spriteframe,
					h = t.getSpriteFrame(c);
				if (!h) {
					cc.log("cocos2d: cc.AnimationCache: Animation '" + n + "' refers to frame '" + c + "' which is not currently in the cc.SpriteFrameCache. This frame will not be added to the animation.");
					continue
				}
				var p = parseFloat(l.delayUnits) || 0,
					d = l.notification,
					v = new cc.AnimationFrame();
				v.initWithSpriteFrame(h, p, d), a.push(v)
			}
			var m = parseFloat(r.delayPerUnit) || 0,
				g = new cc.Animation();
			g.initWithAnimationFrames(a, m, s), g.setRestoreOriginalFrame(o), cc.AnimationCache.getInstance().addAnimation(g, n)
		}
	},
	init: function() {
		return this._animations = {}, !0
	},
	_animations: null
}), cc.AnimationCache.purgeSharedAnimationCache = function() {
	cc.s_sharedAnimationCache && (cc.s_sharedAnimationCache._animations = null, cc.s_sharedAnimationCache = null)
}, cc.AnimationCache.getInstance = function() {
	return cc.s_sharedAnimationCache === null && (cc.s_sharedAnimationCache = new cc.AnimationCache(), cc.s_sharedAnimationCache.init()), cc.s_sharedAnimationCache
}, cc.s_sharedAnimationCache = null;
cc.SpriteFrame = cc.Class.extend({
	_offset: null,
	_originalSize: null,
	_rectInPixels: null,
	_rotated: null,
	_rect: null,
	_offsetInPixels: null,
	_originalSizeInPixels: null,
	_texture: null,
	_textureFilename: "",
	ctor: function() {
		this._offset = cc.p(0, 0), this._offsetInPixels = cc.p(0, 0), this._originalSize = cc.size(0, 0), this._rectInPixels = cc.rect(0, 0, 0, 0), this._rect = cc.rect(0, 0, 0, 0), this._originalSizeInPixels = cc.size(0, 0), this._textureFilename = ""
	},
	getRectInPixels: function() {
		return this._rectInPixels
	},
	setRectInPixels: function(e) {
		this._rectInPixels = e, this._rect = cc.RECT_PIXELS_TO_POINTS(e)
	},
	isRotated: function() {
		return this._rotated
	},
	setRotated: function(e) {
		this._rotated = e
	},
	getRect: function() {
		return this._rect
	},
	setRect: function(e) {
		this._rect = e, this._rectInPixels = cc.RECT_POINTS_TO_PIXELS(this._rect)
	},
	getOffsetInPixels: function() {
		return cc.p(this._offsetInPixels.x, this._offsetInPixels.y)
	},
	setOffsetInPixels: function(e) {
		this._offsetInPixels = e, this._offset = cc.POINT_PIXELS_TO_POINTS(this._offsetInPixels)
	},
	getOriginalSizeInPixels: function() {
		return this._originalSizeInPixels
	},
	setOriginalSizeInPixels: function(e) {
		this._originalSizeInPixels = e
	},
	getOriginalSize: function() {
		return cc.size(this._originalSize.width, this._originalSize.height)
	},
	setOriginalSize: function(e) {
		this._originalSize = e
	},
	getTexture: function() {
		return this._texture ? this._texture : this._textureFilename !== "" ? cc.TextureCache.getInstance().addImage(this._textureFilename) : null
	},
	setTexture: function(e) {
		this._texture != e && (this._texture = e)
	},
	getOffset: function() {
		return cc.p(this._offset.x, this._offset.y)
	},
	setOffset: function(e) {
		this._offset = e
	},
	copyWithZone: function() {
		var e = new cc.SpriteFrame();
		return e.initWithTextureFilename(this._textureFilename, this._rectInPixels, this._rotated, this._offsetInPixels, this._originalSizeInPixels), e.setTexture(this._texture), e
	},
	initWithTexture: function(e, t, n, r, i) {
		var s = arguments.length;
		switch (s) {
		case 2:
			var o = cc.RECT_POINTS_TO_PIXELS(t);
			return this.initWithTexture(e, o, !1, cc.PointZero(), o.size);
		case 5:
			return this._texture = e, this._rectInPixels = t, this._rect = cc.RECT_PIXELS_TO_POINTS(t), this._offsetInPixels = r, this._offset = cc.POINT_PIXELS_TO_POINTS(this._offsetInPixels), this._originalSizeInPixels = i, this._originalSize = cc.SIZE_PIXELS_TO_POINTS(this._originalSizeInPixels), this._rotated = n, !0;
		default:
			throw "Argument must be non-nil "
		}
	},
	initWithTextureFilename: function(e, t, n, r, i) {
		var s = cc.RECT_POINTS_TO_PIXELS(t);
		return r = r || cc.size(0, 0), i = i || s.size, this._texture = null, this._textureFilename = e, this._rectInPixels = s, this._rect = cc.RECT_PIXELS_TO_POINTS(s), this._rotated = n || !1, this._offsetInPixels = r, this._offset = cc.POINT_PIXELS_TO_POINTS(r), this._originalSizeInPixels = i, this._originalSize = cc.SIZE_PIXELS_TO_POINTS(i), !0
	}
}), cc.SpriteFrame.create = function(e, t, n, r, i) {
	var s = new cc.SpriteFrame();
	switch (arguments.length) {
	case 2:
		s.initWithTextureFilename(e, t);
		break;
	case 5:
		s.initWithTextureFilename(e, t, n, r, i);
		break;
	default:
		throw "Argument must be non-nil "
	}
	return s
}, cc.SpriteFrame.createWithTexture = function(e, t, n, r, i) {
	var s = arguments.length,
		o = new cc.SpriteFrame();
	switch (s) {
	case 2:
		o.initWithTexture(e, t);
		break;
	case 5:
		o.initWithTexture(e, t, n, r, i);
		break;
	default:
		throw "Argument must be non-nil "
	}
	return o
}, cc.SpriteFrame._frameWithTextureForCanvas = function(e, t, n, r, i) {
	var s = new cc.SpriteFrame();
	return s._texture = e, s._rectInPixels = t, s._rect = cc.RECT_PIXELS_TO_POINTS(t), s._offsetInPixels = r, s._offset = cc.POINT_PIXELS_TO_POINTS(s._offsetInPixels), s._originalSizeInPixels = i, s._originalSize = cc.SIZE_PIXELS_TO_POINTS(s._originalSizeInPixels), s._rotated = n, s
};
cc.SpriteFrameCache = cc.Class.extend({
	_spriteFrames: null,
	_spriteFramesAliases: null,
	_loadedFileNames: null,
	ctor: function() {
		this._spriteFrames = {}, this._spriteFramesAliases = {}, this._loadedFileNames = []
	},
	_addSpriteFramesWithDictionary: function(e, t) {
		var n = e.metadata,
			r = e.frames,
			i = 0;
		n != null && (i = parseInt(this._valueForKey("format", n))), cc.Assert(i >= 0 && i <= 3, "format is not supported for cc.SpriteFrameCache addSpriteFramesWithDictionary:textureFilename:");
		for (var s in r) {
			var o = r[s];
			if (o) {
				var u = this._spriteFrames[s];
				if (u) continue;
				if (i == 0) {
					var a = parseFloat(this._valueForKey("x", o)),
						f = parseFloat(this._valueForKey("y", o)),
						l = parseFloat(this._valueForKey("width", o)),
						c = parseFloat(this._valueForKey("height", o)),
						h = parseFloat(this._valueForKey("offsetX", o)),
						p = parseFloat(this._valueForKey("offsetY", o)),
						d = parseInt(this._valueForKey("originalWidth", o)),
						v = parseInt(this._valueForKey("originalHeight", o));
					(!d || !v) && cc.log("cocos2d: WARNING: originalWidth/Height not found on the cc.SpriteFrame. AnchorPoint won't work as expected. Regenrate the .plist"), d = Math.abs(d), v = Math.abs(v), u = new cc.SpriteFrame(), u.initWithTexture(t, cc.rect(a, f, l, c), !1, cc.p(h, p), cc.size(d, v))
				} else if (i == 1 || i == 2) {
					var m = cc.RectFromString(this._valueForKey("frame", o)),
						g = !1;
					i == 2 && (g = this._valueForKey("rotated", o) == "true");
					var y = cc.PointFromString(this._valueForKey("offset", o)),
						b = cc.SizeFromString(this._valueForKey("sourceSize", o));
					u = new cc.SpriteFrame(), u.initWithTexture(t, m, g, y, b)
				} else if (i == 3) {
					var w, E, S, x, T;
					w = cc.SizeFromString(this._valueForKey("spriteSize", o)), E = cc.PointFromString(this._valueForKey("spriteOffset", o)), S = cc.SizeFromString(this._valueForKey("spriteSourceSize", o)), x = cc.RectFromString(this._valueForKey("textureRect", o)), T = this._valueForKey("textureRotated", o) == "true";
					var N = o.aliases,
						C = s.toString();
					for (var k in N) this._spriteFramesAliases.hasOwnProperty(N[k]) && cc.log("cocos2d: WARNING: an alias with name " + k + " already exists"), this._spriteFramesAliases[N[k]] = C;
					u = new cc.SpriteFrame(), o.hasOwnProperty("spriteSize") ? u.initWithTexture(t, cc.rect(x.origin.x, x.origin.y, w.width, w.height), T, E, S) : u.initWithTexture(t, w, T, E, S)
				}
				if (u.isRotated()) {
					var L = cc.cutRotateImageToCanvas(u.getTexture(), u.getRect()),
						A = u.getRect();
					u.setRect(cc.rect(0, 0, A.size.width, A.size.height)), u.setTexture(L)
				}
				this._spriteFrames[s] = u
			}
		}
	},
	addSpriteFramesWithJson: function(e) {
		var t = e,
			n = "",
			r = t.metadata;
		r && (n = this._valueForKey("textureFileName", r), n = n.toString());
		var i = cc.TextureCache.getInstance().addImage(n);
		i ? this._addSpriteFramesWithDictionary(t, i) : cc.log("cocos2d: cc.SpriteFrameCache: Couldn't load texture")
	},
	addSpriteFrames: function(e, t) {
		var n = cc.FileUtils.getInstance().dictionaryWithContentsOfFileThreadSafe(e);
		switch (arguments.length) {
		case 1:
			cc.Assert(e, "plist filename should not be NULL");
			if (!cc.ArrayContainsObject(this._loadedFileNames, e)) {
				var r = "",
					i = n.metadata;
				i && (r = this._valueForKey("textureFileName", i).toString());
				if (r != "") {
					var s = e.lastIndexOf("/"),
						o;
					o = s ? e.substring(0, s + 1) : "", r = o + r
				} else {
					r = e;
					var u = r.lastIndexOf(".", r.length);
					r = r.substr(0, u), r += ".png"
				}
				var a = cc.TextureCache.getInstance().addImage(r);
				a ? this._addSpriteFramesWithDictionary(n, a) : cc.log("cocos2d: cc.SpriteFrameCache: Couldn't load texture")
			}
			break;
		case 2:
			if (t instanceof cc.Texture2D || t instanceof HTMLImageElement || t instanceof HTMLCanvasElement) this._addSpriteFramesWithDictionary(n, t);
			else {
				var f = t;
				cc.Assert(f, "texture name should not be null");
				var l = cc.TextureCache.getInstance().addImage(f);
				l ? this._addSpriteFramesWithDictionary(n, l) : cc.log("cocos2d: cc.SpriteFrameCache: couldn't load texture file. File not found " + f)
			}
			break;
		default:
			throw "Argument must be non-nil "
		}
	},
	addSpriteFrame: function(e, t) {
		this._spriteFrames[t] = e
	},
	removeSpriteFrames: function() {
		this._spriteFrames = [], this._spriteFramesAliases = [], this._loadedFileNames = {}
	},
	removeSpriteFrameByName: function(e) {
		if (!e) return;
		this._spriteFramesAliases.hasOwnProperty(e) && delete this._spriteFramesAliases[e], this._spriteFrames.hasOwnProperty(e) && delete this._spriteFrames[e], this._loadedFileNames = {}
	},
	removeSpriteFramesFromFile: function(e) {
		var t = cc.FileUtils.getInstance().fullPathFromRelativePath(e),
			n = cc.FileUtils.getInstance().dictionaryWithContentsOfFileThreadSafe(t);
		this._removeSpriteFramesFromDictionary(n), cc.ArrayContainsObject(this._loadedFileNames, e) && cc.ArrayRemoveObject(e)
	},
	_removeSpriteFramesFromDictionary: function(e) {
		var t = e.frames;
		for (var n in t) this._spriteFrames.hasOwnProperty(n) && delete this._spriteFrames[n]
	},
	removeSpriteFramesFromTexture: function(e) {
		for (var t in this._spriteFrames) {
			var n = this._spriteFrames[t];
			n && n.getTexture() == e && delete this._spriteFrames[t]
		}
	},
	getSpriteFrame: function(e) {
		var t;
		this._spriteFrames.hasOwnProperty(e) && (t = this._spriteFrames[e]);
		if (!t) {
			var n;
			this._spriteFramesAliases.hasOwnProperty(e) && (n = this._spriteFramesAliases[e]), n && (this._spriteFrames.hasOwnProperty(n.toString()) && (t = this._spriteFrames[n.toString()]), t || cc.log("cocos2d: cc.SpriteFrameCahce: Frame " + e + " not found"))
		}
		return t
	},
	_valueForKey: function(e, t) {
		return t && t.hasOwnProperty(e) ? t[e].toString() : ""
	}
}), cc.s_sharedSpriteFrameCache = null, cc.SpriteFrameCache.getInstance = function() {
	return cc.s_sharedSpriteFrameCache || (cc.s_sharedSpriteFrameCache = new cc.SpriteFrameCache()), cc.s_sharedSpriteFrameCache
}, cc.SpriteFrameCache.purgeSharedSpriteFrameCache = function() {
	cc.s_sharedSpriteFrameCache = null
};
cc.DEFAULT_SPRITE_BATCH_CAPACITY = 29, cc.SpriteBatchNode = cc.Node.extend({
	_textureAtlas: new cc.TextureAtlas(),
	_blendFunc: new cc.BlendFunc(0, 0),
	_descendants: [],
	_renderTexture: null,
	_isUseCache: !1,
	_originalTexture: null,
	ctor: function(e) {
		this._super(), e && this.init(e, cc.DEFAULT_SPRITE_BATCH_CAPACITY), this._renderTexture = cc.RenderTexture.create(cc.canvas.width, cc.canvas.height), this.setContentSize(cc.size(cc.canvas.width, cc.canvas.height))
	},
	setContentSize: function(e) {
		if (!e) return;
		this._super(e), this._renderTexture.setContentSize(e)
	},
	_updateBlendFunc: function() {
		this._textureAtlas.getTexture().hasPremultipliedAlpha() || (this._blendFunc.src = gl.SRC_ALPHA, this._blendFunc.dst = gl.ONE_MINUS_SRC_ALPHA)
	},
	_updateAtlasIndex: function(e, t) {
		var n = 0,
			r = e.getChildren();
		r && (n = r.length);
		var i = 0;
		if (n == 0) i = e.getAtlasIndex(), e.setAtlasIndex(t), e.setOrderOfArrival(0), i != t && this._swap(i, t), t++;
		else {
			var s = !0;
			r[0].getZOrder() >= 0 && (i = e.getAtlasIndex(), e.setAtlasIndex(t), e.setOrderOfArrival(0), i != t && this._swap(i, t), t++, s = !1);
			for (var o = 0; o < r.length; o++) {
				var u = r[o];
				s && u.getZOrder() >= 0 && (i = e.getAtlasIndex(), e.setAtlasIndex(t), e.setOrderOfArrival(0), i != t && this._swap(i, t), t++, s = !1), this._updateAtlasIndex(u, t)
			}
			s && (i = e.getAtlasIndex(), e.setAtlasIndex(t), e.setOrderOfArrival(0), i != t && this._swap(i, t), t++)
		}
		return t
	},
	_swap: function(e, t) {
		if (this._descendants.length >= 2 && t < this._descendants.length) {
			e == -1 && (e = this._descendants.length - 1);
			var n = this._textureAtlas.getQuads(),
				r = this._descendants[e],
				i = n[e];
			this._descendants[t].setAtlasIndex(e), this._descendants[e] = this._descendants[t], n[e] = n[t], this._descendants[t] = r, n[t] = i
		}
	},
	addQuadFromSprite: function(e, t) {
		cc.Assert(e != null, "SpriteBatchNode.addQuadFromSprite():Argument must be non-nil"), cc.Assert(e instanceof cc.Sprite, "cc.SpriteBatchNode only supports cc.Sprites as children"), e.setBatchNode(this), e.setAtlasIndex(t), this._textureAtlas.insertQuad(e.getQuad(), t), e.setDirty(!0), e.updateTransform(), cc.renderContextType == cc.CANVAS && (this._children = cc.ArrayAppendObjectToIndex(this._children, e, t))
	},
	addSpriteWithoutQuad: function(e, t, n) {
		cc.Assert(e != null, "SpriteBatchNode.addQuadFromSprite():Argument must be non-nil"), cc.Assert(e instanceof cc.Sprite, "cc.SpriteBatchNode only supports cc.Sprites as children"), e.setAtlasIndex(t);
		var r = 0;
		if (this._descendants && this._descendants.length > 0) {
			var i = null;
			for (var s = 0; s < this._descendants.length; s++) i = this._descendants[s], i && i.getAtlasIndex() >= t && ++r
		}
		return this._descendants = cc.ArrayAppendObjectToIndex(this._descendants, e, r), this.addChild(e, t, n), this.reorderBatch(!1), this
	},
	getTextureAtlas: function() {
		return this._textureAtlas
	},
	setTextureAtlas: function(e) {
		e != this._textureAtlas && (this._textureAtlas = e)
	},
	getDescendants: function() {
		return this._descendants
	},
	initWithTexture: function(e, t) {
		return this._children = [], this._descendants = [], this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, this._textureAtlas = new cc.TextureAtlas(), t = t || cc.DEFAULT_SPRITE_BATCH_CAPACITY, this._textureAtlas.initWithTexture(e, t), cc.renderContextType == cc.CANVAS && (this._originalTexture = e), cc.renderContextType == cc.WEBGL && this._updateBlendFunc(), !0
	},
	setNodeDirty: function() {
		this._setNodeDirtyForCache(), this._transformDirty = this._inverseDirty = !0, cc.NODE_TRANSFORM_USING_AFFINE_MATRIX && (this._transformGLDirty = !0)
	},
	_setNodeDirtyForCache: function() {
		this._cacheDirty = !0
	},
	init: function(e, t) {
		var n = cc.TextureCache.getInstance().textureForKey(e);
		return n || (n = cc.TextureCache.getInstance().addImage(e)), this.initWithTexture(n, t)
	},
	increaseAtlasCapacity: function() {
		var e = (this._textureAtlas.getCapacity() + 1) * 4 / 3;
		cc.log("cocos2d: CCSpriteBatchNode: resizing TextureAtlas capacity from " + this._textureAtlas.getCapacity() + " to [" + e + "]."), this._textureAtlas.resizeCapacity(e) || (cc.log("cocos2d: WARNING: Not enough memory to resize the atlas"), cc.Assert(!1, "Not enough memory to resize the atla"))
	},
	removeChildAtIndex: function(e, t) {
		this.removeChild(this._children[e], t)
	},
	insertChild: function(e, t) {
		e.setBatchNode(this), e.setAtlasIndex(t), e.setDirty(!0), this._textureAtlas.getTotalQuads() == this._textureAtlas.getCapacity() && this.increaseAtlasCapacity(), this._textureAtlas.insertQuad(e.getQuad(), t), this._descendants = cc.ArrayAppendObjectToIndex(this._descendants, e, t);
		var n = t + 1;
		if (this._descendants && this._descendants.length > 0) for (; n < this._descendants.length; n++) this._descendants[n].setAtlasIndex(this._descendants[n].getAtlasIndex() + 1);
		var r = e.getChildren();
		if (r && r.length > 0) for (n = 0; n < r.length; n++) if (r[n]) {
			var i = this.atlasIndexForChild(r[n], r[n].getZOrder());
			this.insertChild(r[n], i)
		}
	},
	appendChild: function(e) {
		this._reorderChildDirty = !0, e.setBatchNode(this), e.setDirty(!0), this._textureAtlas.getTotalQuads() == this._textureAtlas.getCapacity() && this.increaseAtlasCapacity(), cc.ArrayAppendObject(this._descendants, e);
		var t = this._descendants.length - 1;
		e.setAtlasIndex(t), this._textureAtlas.insertQuad(e.getQuad(), t);
		var n = e.getChildren();
		for (var r = 0; r < n.length; r++) this.appendChild(n[r])
	},
	removeSpriteFromAtlas: function(e) {
		this._textureAtlas.removeQuadAtIndex(e.getAtlasIndex()), e.setBatchNode(null);
		var t = cc.ArrayGetIndexOfObject(this._descendants, e);
		if (t != -1) {
			cc.ArrayRemoveObjectAtIndex(this._descendants, t);
			var n = this._descendants.length;
			for (; t < n; ++t) {
				var r = this._descendants[t];
				r.setAtlasIndex(r.getAtlasIndex() - 1)
			}
		}
		var i = e.getChildren();
		if (i && i.length > 0) for (var s = 0; s < i.length; s++) i[s] && this.removeSpriteFromAtlas(i[s])
	},
	rebuildIndexInOrder: function(e, t) {
		var n = e.getChildren();
		if (n && n.length > 0) for (var r = 0; r < n.length; r++) {
			var i = n[r];
			i && i.getZOrder() < 0 && (t = this.rebuildIndexInOrder(i, t))
		}
		e.isEqual(this) || (e.setAtlasIndex(t), t++);
		if (n && n.length > 0) for (r = 0; r < n.length; r++) i = n[r], i && i.getZOrder() >= 0 && (t = this.rebuildIndexInOrder(i, t));
		return t
	},
	highestAtlasIndexInChild: function(e) {
		var t = e.getChildren();
		return !t || t.length == 0 ? e.getAtlasIndex() : this.highestAtlasIndexInChild(t.pop())
	},
	lowestAtlasIndexInChild: function(e) {
		var t = e.getChildren();
		return !t || t.length == 0 ? e.getAtlasIndex() : this.lowestAtlasIndexInChild(t.pop())
	},
	atlasIndexForChild: function(e, t) {
		var n = e.getParent().getChildren(),
			r = cc.ArrayGetIndexOfObject(n, e),
			i = e.getParent() == this,
			s = null;
		r > 0 && r < cc.UINT_MAX && (s = n[r - 1]);
		if (i) return r == 0 ? 0 : this.highestAtlasIndexInChild(s) + 1;
		if (r == 0) {
			var o = e.getParent();
			return t < 0 ? o.getAtlasIndex() : o.getAtlasIndex() + 1
		}
		if (s.getZOrder() < 0 && t < 0 || s.getZOrder() >= 0 && t >= 0) return this.highestAtlasIndexInChild(s) + 1;
		var o = e.getParent();
		return o.getAtlasIndex() + 1
	},
	reorderBatch: function(e) {
		this._reorderChildDirty = e
	},
	getTexture: function() {
		return this._textureAtlas.getTexture()
	},
	setTexture: function(e) {
		this._textureAtlas.setTexture(e);
		for (var t = 0; t < this._children.length; t++) this._children[t].setTexture(e)
	},
	setBlendFunc: function(e, t) {
		arguments.length == 1 ? this._blendFunc = e : this._blendFunc = {
			src: e,
			dst: t
		}
	},
	getBlendFunc: function() {
		return this._blendFunc
	},
	visit: function(e) {
		if (cc.renderContextType == cc.CANVAS) {
			var t = e || cc.renderContext;
			if (!this._visible) return;
			t.save(), this.transform(e);
			var n;
			if (this._isUseCache) {
				if (this._cacheDirty) {
					this._renderTexture.clear(), this._renderTexture.context.save(), this._renderTexture.context.translate(this._anchorPointInPoints.x, -this._anchorPointInPoints.y);
					if (this._children) {
						this.sortAllChildren();
						for (n = 0; n < this._children.length; n++) this._children[n] && this._children[n].visit(this._renderTexture.context)
					}
					this._renderTexture.context.restore(), this._cacheDirty = !1
				}
				this.draw(e)
			} else if (this._children) {
				this.sortAllChildren();
				for (n = 0; n < this._children.length; n++) this._children[n] && this._children[n].visit(t)
			}
			t.restore()
		} else {
			if (!this._visible) return;
			this._grid && this._grid.isActive() && (this._grid.beforeDraw(), this.transformAncestors()), this.sortAllChildren(), this.transform(), this.draw(), this._grid && this._grid.isActive() && this._grid.afterDraw(this), this.setOrderOfArrival(0)
		}
	},
	addChild: function(e, t, n) {
		switch (arguments.length) {
		case 1:
			this._super(e);
			break;
		case 2:
			this._super(e, t);
			break;
		case 3:
			cc.Assert(e != null, "SpriteBatchNode.addChild():child should not be null"), cc.Assert(e instanceof cc.Sprite, "cc.SpriteBatchNode only supports cc.Sprites as children"), cc.renderContextType != cc.CANVAS && cc.Assert(e.getTexture().getName() == this._textureAtlas.getTexture().getName(), "SpriteBatchNode.addChild():check cc.Sprite is using the same texture id"), this._super(e, t, n), this.appendChild(e);
			break;
		case 4:
			arguments[3] && this._super(e, t, n);
			break;
		default:
			throw "Argument must be non-nil "
		}
		this.setNodeDirty()
	},
	reorderChild: function(e, t) {
		cc.Assert(e != null, "SpriteBatchNode.addChild():the child should not be null"), cc.Assert(this._children.indexOf(e) > -1, "SpriteBatchNode.addChild():Child doesn't belong to Sprite");
		if (t == e.getZOrder()) return;
		this._super(e, t), this.setNodeDirty()
	},
	removeChild: function(e, t) {
		if (e == null) return;
		cc.Assert(this._children.indexOf(e) > -1, "SpriteBatchNode.addChild():sprite batch node should contain the child"), this.removeSpriteFromAtlas(e), this._super(e, t)
	},
	removeAllChildren: function(e) {
		var t;
		if (this._descendants && this._descendants.length > 0) for (t = 0; t < this._descendants.length; t++) this._descendants[t] && this._descendants[t].setBatchNode(null);
		this._super(e), this._descendants = [], this._textureAtlas.removeAllQuads()
	},
	sortAllChildren: function() {
		if (this._reorderChildDirty) {
			var e = 0,
				t = 0,
				n = this._children.length;
			for (e = 1; e < n; e++) {
				var r = this._children[e];
				t = e - 1;
				while (t >= 0 && (r.getZOrder() < this._children[t].getZOrder() || r.getZOrder() == this._children[t].getZOrder() && r.getOrderOfArrival() < this._children[t].getOrderOfArrival())) this._children[t + 1] = this._children[t], t--;
				this._children[t + 1] = r
			}
			if (this._children.length > 0) {
				this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.sortAllChildren);
				var i = 0;
				for (e = 0; e < this._children.length; e++) i = this._updateAtlasIndex(this._children[e], i)
			}
			this._reorderChildDirty = !1
		}
	},
	draw: function(e) {
		this._super();
		if (cc.renderContextType == cc.CANVAS) {
			var t = e || cc.renderContext,
				n = cc.p(0 | -this._anchorPointInPoints.x, 0 | -this._anchorPointInPoints.y);
			this._renderTexture && t.drawImage(this._renderTexture.getCanvas(), n.x, -(n.y + this._renderTexture.getCanvas().height))
		} else {
			if (this._textureAtlas.getTotalQuads() == 0) return;
			this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.updateTransform), this._textureAtlas.drawQuads()
		}
	}
}), cc.SpriteBatchNode.create = function(e, t) {
	t || (t = cc.DEFAULT_SPRITE_BATCH_CAPACITY);
	var n = new cc.SpriteBatchNode();
	return n.init(e, t), n
}, cc.SpriteBatchNode.createWithTexture = function(e, t) {
	t || (t = cc.DEFAULT_SPRITE_BATCH_CAPACITY);
	var n = new cc.SpriteBatchNode();
	return n.initWithTexture(e, t), n
};
cc.LabelTTF = cc.Sprite.extend({
	_dimensions: cc.SizeZero(),
	_hAlignment: cc.TEXT_ALIGNMENT_CENTER,
	_vAlignment: cc.VERTICAL_TEXT_ALIGNMENT_TOP,
	_fontName: "Arial",
	_fontSize: 0,
	_string: "",
	_fontStyleStr: null,
	_colorStyleStr: null,
	ctor: function() {
		this._super(), this._opacityModifyRGB = !1, this._fontStyleStr = "", this._colorStyleStr = "", this._opacity = 255, this._color = cc.white(), this._setColorStyleStr()
	},
	init: function(e) {
		return e ? this._super() : this.initWithString([" ", this._fontName, this._fontSize])
	},
	description: function() {
		return "<cc.LabelTTF | FontName =" + this._fontName + " FontSize = " + this._fontSize.toFixed(1) + ">"
	},
	setColor: function(e) {
		if (this._color.r == e.r && this._color.g == e.g && this._color.b == e.b) return;
		this._color = this._colorUnmodified = new cc.Color3B(e.r, e.g, e.b), this._setColorStyleStr(), this.setNodeDirty()
	},
	setOpacity: function(e) {
		if (this._opacity === e) return;
		this._opacity = e, this._setColorStyleStr(), this.setNodeDirty()
	},
	_setColorStyleStr: function() {
		this._colorStyleStr = "rgba(" + this._color.r + "," + this._color.g + "," + this._color.b + ", " + this._opacity / 255 + ")"
	},
	setString: function(e) {
		this._string != e && (this._string = e + "", this._string.length > 0 && this._updateTTF())
	},
	getString: function() {
		return this._string
	},
	getHorizontalAlignment: function() {
		return this._hAlignment
	},
	setHorizontalAlignment: function(e) {
		e != this._hAlignment && (this._hAlignment = e, this._string.length > 0 && this._updateTTF())
	},
	getVerticalAlignment: function() {
		return this._vAlignment
	},
	setVerticalAlignment: function(e) {
		e != this._vAlignment && (this._vAlignment = e, this._string.length > 0 && this._updateTTF())
	},
	getDimensions: function() {
		return this._dimensions
	},
	setDimensions: function(e) {
		if (e.width != this._dimensions.width || e.height != this._dimensions.height) this._dimensions = e, this._string.length > 0 && this._updateTTF()
	},
	getFontSize: function() {
		return this._fontSize
	},
	setFontSize: function(e) {
		this._fontSize != e && (this._fontSize = e, this._string.length > 0 && this._updateTTF())
	},
	getFontName: function() {
		return this._fontName
	},
	setFontName: function(e) {
		this._fontName != e && (this._fontName = new String(e), this._string.length > 0 && this._updateTTF())
	},
	initWithString: function(e) {
		var t = new String(e[0]),
			n, r, i, s, o;
		return cc.Assert(t != null, "cc.LabelTTF.initWithString() label is null"), e.length == 6 ? (n = e[1], r = e[2], i = e[3], s = e[4], o = e[5]) : e.length == 5 ? (n = e[1], r = e[2], i = e[3], s = e[4], o = cc.VERTICAL_TEXT_ALIGNMENT_TOP) : (n = e[1], r = e[2], i = cc.size(0, e[2]), s = cc.TEXT_ALIGNMENT_LEFT, o = cc.VERTICAL_TEXT_ALIGNMENT_BOTTOM), this.init(!0) ? (this._dimensions = cc.size(i.width, i.height), this._fontName = n, this._hAlignment = s, this._vAlignment = o, this._fontSize = r * cc.CONTENT_SCALE_FACTOR(), this.setString(t), this._fontStyleStr = this._fontSize + "px '" + this._fontName + "'", this._updateTTF(), !0) : !1
	},
	draw: function(e) {
		if (cc.renderContextType == cc.CANVAS) {
			var t = e || cc.renderContext;
			this._flipX && t.scale(-1, 1), this._flipY && t.scale(1, -1), t.fillStyle = this._colorStyleStr, t.font != this._fontStyleStr && (t.font = this._fontStyleStr);
			if ((this._contentSize.width > this._dimensions.width || this._string.indexOf("\n") > -1) && this._dimensions.width !== 0) t.textBaseline = cc.LabelTTF._textBaseline[this._vAlignment], t.textAlign = cc.LabelTTF._textAlign[this._hAlignment], this._wrapText(t, this._string, -this._dimensions.width * this._anchorPoint.x, this._dimensions.height * this._anchorPoint.y, this._dimensions.width, this._dimensions.height, this._fontSize * 1.2);
			else if (this._dimensions.width == 0) {
				t.textBaseline = "bottom", t.textAlign = "left";
				if (!this._string.indexOf) var n = 0;
				this._string.indexOf("\n") > -1 ? this._multiLineText(t) : t.fillText(this._string, -this._contentSize.width * this._anchorPoint.x, this._contentSize.height * this._anchorPoint.y)
			} else {
				t.textBaseline = cc.LabelTTF._textBaseline[this._vAlignment], t.textAlign = cc.LabelTTF._textAlign[this._hAlignment];
				var r = 0,
					i = 0;
				this._hAlignment == cc.TEXT_ALIGNMENT_RIGHT && (r = this._dimensions.width), this._hAlignment == cc.TEXT_ALIGNMENT_CENTER && (r = this._dimensions.width / 2), this._vAlignment == cc.VERTICAL_TEXT_ALIGNMENT_TOP && (i = -this._dimensions.height), this._vAlignment == cc.VERTICAL_TEXT_ALIGNMENT_CENTER && (i = -this._dimensions.height / 2), t.fillText(this._string, -this._dimensions.width * this._anchorPoint.x + r, this._dimensions.height * this._anchorPoint.y + i)
			}
			cc.INCREMENT_GL_DRAWS(1)
		}
	},
	_multiLineText: function(e) {
		var t = this._fontSize * 1.2,
			n = this._string.split("\n"),
			r = n.length,
			i = [],
			s = 0;
		for (var o = 0; o < r; o++) i[o] = e.measureText(n[o]).width, i[o] > s && (s = i[o]);
		var u = cc.p(s / 2, r * t / 2);
		for (o = 0; o < r; o++) {
			var a = -i[o] / 2;
			this._hAlignment == cc.TEXT_ALIGNMENT_RIGHT && (a = u.x - s), this._hAlignment == cc.TEXT_ALIGNMENT_CENTER && (a = s - i[o]), e.fillText(n[o], a, o * t - u.y + t / 2)
		}
	},
	_wrapText: function(e, t, n, r, i, s, o) {
		var u = this._lineCount() - 1,
			a = 0,
			f = 0;
		this._hAlignment === cc.TEXT_ALIGNMENT_RIGHT && (a = i), this._hAlignment === cc.TEXT_ALIGNMENT_CENTER && (a = i / 2), this._vAlignment === cc.VERTICAL_TEXT_ALIGNMENT_TOP && (f = -s), this._vAlignment === cc.VERTICAL_TEXT_ALIGNMENT_BOTTOM && (f = -o * u), this._vAlignment === cc.VERTICAL_TEXT_ALIGNMENT_CENTER && (f = -s / 2 - o * u / 2);
		var l = t.split("\n");
		for (var c = 0; c < l.length; c++) {
			var h = c * o,
				p = l[c].split(" "),
				d = "";
			for (var v = 0; v < p.length; v++) {
				var m = d + p[v] + " ",
					g = e.measureText(m).width - e.measureText(" ").width;
				g >= i ? (e.fillText(d, n + a, r + f + h), r += o, d = p[v] + " ") : d = m, v == p.length - 1 && e.fillText(d, n + a, r + f + h)
			}
		}
	},
	_lineCount: function() {
		if (this._dimensions.width == 0) return 1;
		var e = cc.renderContext,
			t = this._string.split(" "),
			n = "",
			r = 0;
		cc.renderContext.save();
		for (var i = 0; i < t.length; i++) {
			var s = n + t[i] + " ",
				o = e.measureText(s).width - e.measureText(" ").width;
			o >= this._dimensions.width ? (r++, n = t[i] + " ") : n = s, i == t.length - 1 && r++
		}
		return cc.renderContext.restore(), r
	},
	_updateTTF: function() {
		var e = cc.renderContext.font;
		this._fontStyleStr = this._fontSize + "px '" + this._fontName + "'", cc.renderContext.font = this._fontStyleStr;
		var t = cc.renderContext.measureText(this._string);
		this.setContentSize(cc.size(t.width, this._fontSize)), cc.renderContext.font = e, this.setNodeDirty()
	}
}), cc.LabelTTF._textAlign = ["left", "center", "right"], cc.LabelTTF._textBaseline = ["top", "middle", "bottom"], cc.LabelTTF.create = function() {
	var e = new cc.LabelTTF();
	return e.initWithString(arguments) ? e : null
}, cc.LabelTTF.node = function() {
	return cc.LabelTTF.create()
};
cc.PARTICLE_SHAPE_MODE = 0, cc.PARTICLE_TEXTURE_MODE = 1, cc.PARTICLE_STAR_SHAPE = 0, cc.PARTICLE_BALL_SHAPE = 1, cc.PARTICLE_DURATION_INFINITY = -1, cc.PARTICLE_START_SIZE_EQUAL_TO_END_SIZE = -1, cc.PARTICLE_START_RADIUS_EQUAL_TO_END_RADIUS = -1, cc.PARTICLE_MODE_GRAVITY = 0, cc.PARTICLE_MODE_RADIUS = 1, cc.PARTICLE_TYPE_FREE = 0, cc.PARTICLE_TYPE_RELATIVE = 1, cc.PARTICLE_TYPE_GROUPED = 2, cc.PARTICLE_TYPE_FREE = cc.PARTICLE_TYPE_FREE, cc.PARTICLE_TYPE_GROUPED = cc.PARTICLE_TYPE_GROUPED, cc.Particle = function(e, t, n, r, i, s, o, u, a, f, l, c) {
	this.pos = e ? e : cc.PointZero(), this.startPos = t ? t : cc.PointZero(), this.color = n ? n : new cc.Color4F(0, 0, 0, 1), this.deltaColor = r ? r : new cc.Color4F(0, 0, 0, 1), this.size = i || 0, this.deltaSize = s || 0, this.rotation = o || 0, this.deltaRotation = u || 0, this.timeToLive = a || 0, this.atlasIndex = f || 0, this.modeA = l ? l : new cc.Particle.ModeA(), this.modeB = c ? c : new cc.Particle.ModeB(), this.isChangeColor = !1, this.drawPos = cc.p(0, 0)
}, cc.Particle.ModeA = function(e, t, n) {
	this.dir = e ? e : cc.PointZero(), this.radialAccel = t || 0, this.tangentialAccel = n || 0
}, cc.Particle.ModeB = function(e, t, n, r) {
	this.angle = e || 0, this.degreesPerSecond = t || 0, this.radius = n || 0, this.deltaRadius = r || 0
}, cc.ParticleSystem = cc.Node.extend({
	_plistFile: "",
	_elapsed: 0,
	_dontTint: !1,
	modeA: null,
	modeB: null,
	_pointZeroForParticle: cc.p(0, 0),
	_particles: null,
	_particlePool: null,
	_emitCounter: 0,
	_particleIdx: 0,
	_batchNode: null,
	getBatchNode: function() {
		return this._batchNode
	},
	setBatchNode: function(e) {
		if (this._batchNode != e) {
			this._batchNode = e;
			if (e) for (var t = 0; t < this._totalParticles; t++) this._particles[t].atlasIndex = t
		}
	},
	_atlasIndex: 0,
	getAtlasIndex: function() {
		return this._atlasIndex
	},
	setAtlasIndex: function(e) {
		this._atlasIndex = e
	},
	_transformSystemDirty: !1,
	_allocatedParticles: 0,
	_drawMode: cc.PARTICLE_SHAPE_MODE,
	getDrawMode: function() {
		return this._drawMode
	},
	setDrawMode: function(e) {
		this._drawMode = e
	},
	_shapeType: cc.PARTICLE_BALL_SHAPE,
	getShapeType: function() {
		return this._shapeType
	},
	setShapeType: function(e) {
		this._shapeType = e
	},
	_isActive: !1,
	isActive: function() {
		return this._isActive
	},
	_particleCount: 0,
	getParticleCount: function() {
		return this._particleCount
	},
	setParticleCount: function(e) {
		this._particleCount = e
	},
	_duration: 0,
	getDuration: function() {
		return this._duration
	},
	setDuration: function(e) {
		this._duration = e
	},
	_sourcePosition: cc.PointZero(),
	getSourcePosition: function() {
		return this._sourcePosition
	},
	setSourcePosition: function(e) {
		this._sourcePosition = e
	},
	_posVar: cc.PointZero(),
	getPosVar: function() {
		return this._posVar
	},
	setPosVar: function(e) {
		this._posVar = e
	},
	_life: 0,
	getLife: function() {
		return this._life
	},
	setLife: function(e) {
		this._life = e
	},
	_lifeVar: 0,
	getLifeVar: function() {
		return this._lifeVar
	},
	setLifeVar: function(e) {
		this._lifeVar = e
	},
	_angle: 0,
	getAngle: function() {
		return this._angle
	},
	setAngle: function(e) {
		this._angle = e
	},
	_angleVar: 0,
	getAngleVar: function() {
		return this._angleVar
	},
	setAngleVar: function(e) {
		this._angleVar = e
	},
	getGravity: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.gravity
	},
	setGravity: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.gravity = e
	},
	getSpeed: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.speed
	},
	setSpeed: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.speed = e
	},
	getSpeedVar: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.speedVar
	},
	setSpeedVar: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.speedVar = e
	},
	getTangentialAccel: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.tangentialAccel
	},
	setTangentialAccel: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.tangentialAccel = e
	},
	getTangentialAccelVar: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.tangentialAccelVar
	},
	setTangentialAccelVar: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.tangentialAccelVar = e
	},
	getRadialAccel: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.radialAccel
	},
	setRadialAccel: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.radialAccel = e
	},
	getRadialAccelVar: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.radialAccelVar
	},
	setRadialAccelVar: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_GRAVITY, "Particle Mode should be Gravity"), this.modeA.radialAccelVar = e
	},
	getStartRadius: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.startRadius
	},
	setStartRadius: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.startRadius = e
	},
	getStartRadiusVar: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.startRadiusVar
	},
	setStartRadiusVar: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.startRadiusVar = e
	},
	getEndRadius: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.endRadius
	},
	setEndRadius: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.endRadius = e
	},
	getEndRadiusVar: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.endRadiusVar
	},
	setEndRadiusVar: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.endRadiusVar = e
	},
	getRotatePerSecond: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.rotatePerSecond
	},
	setRotatePerSecond: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.rotatePerSecond = e
	},
	getRotatePerSecondVar: function() {
		return cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.rotatePerSecondVar
	},
	setRotatePerSecondVar: function(e) {
		cc.Assert(this._emitterMode == cc.PARTICLE_MODE_RADIUS, "Particle Mode should be Radius"), this.modeB.rotatePerSecondVar = e
	},
	setScale: function(e, t) {
		this._transformSystemDirty = !0, this._super(e, t)
	},
	setRotation: function(e) {
		this._transformSystemDirty = !0, this._super(e)
	},
	setScaleX: function(e) {
		this._transformSystemDirty = !0, this._super(e)
	},
	setScaleY: function(e) {
		this._transformSystemDirty = !0, this._super(e)
	},
	_startSize: 0,
	getStartSize: function() {
		return this._startSize
	},
	setStartSize: function(e) {
		this._startSize = e
	},
	_startSizeVar: 0,
	getStartSizeVar: function() {
		return this._startSizeVar
	},
	setStartSizeVar: function(e) {
		this._startSizeVar = e
	},
	_endSize: 0,
	getEndSize: function() {
		return this._endSize
	},
	setEndSize: function(e) {
		this._endSize = e
	},
	_endSizeVar: 0,
	getEndSizeVar: function() {
		return this._endSizeVar
	},
	setEndSizeVar: function(e) {
		this._endSizeVar = e
	},
	_startColor: new cc.Color4F(0, 0, 0, 1),
	getStartColor: function() {
		return this._startColor
	},
	setStartColor: function(e) {
		this._startColor = e
	},
	_startColorVar: new cc.Color4F(0, 0, 0, 1),
	getStartColorVar: function() {
		return this._startColorVar
	},
	setStartColorVar: function(e) {
		this._startColorVar = e
	},
	_endColor: new cc.Color4F(0, 0, 0, 1),
	getEndColor: function() {
		return this._endColor
	},
	setEndColor: function(e) {
		this._endColor = e
	},
	_endColorVar: new cc.Color4F(0, 0, 0, 1),
	getEndColorVar: function() {
		return this._endColorVar
	},
	setEndColorVar: function(e) {
		this._endColorVar = e
	},
	_startSpin: 0,
	getStartSpin: function() {
		return this._startSpin
	},
	setStartSpin: function(e) {
		this._startSpin = e
	},
	_startSpinVar: 0,
	getStartSpinVar: function() {
		return this._startSpinVar
	},
	setStartSpinVar: function(e) {
		this._startSpinVar = e
	},
	_endSpin: 0,
	getEndSpin: function() {
		return this._endSpin
	},
	setEndSpin: function(e) {
		this._endSpin = e
	},
	_endSpinVar: 0,
	getEndSpinVar: function() {
		return this._endSpinVar
	},
	setEndSpinVar: function(e) {
		this._endSpinVar = e
	},
	_emissionRate: 0,
	getEmissionRate: function() {
		return this._emissionRate
	},
	setEmissionRate: function(e) {
		this._emissionRate = e
	},
	_totalParticles: 0,
	getTotalParticles: function() {
		return this._totalParticles
	},
	setTotalParticles: function(e) {
		cc.Assert(e <= this._allocatedParticles, "Particle: resizing particle array only supported for quads"), this._totalParticles = e
	},
	_texture: null,
	getTexture: function() {
		return this._texture
	},
	setTexture: function(e) {
		this._texture != e && (this._texture = e, this._updateBlendFunc())
	},
	_blendFunc: {
		src: gl.ONE,
		dst: gl.ONE
	},
	getBlendFunc: function() {
		return this._blendFunc
	},
	setBlendFunc: function(e, t) {
		if (arguments.length == 1) this._blendFunc != e && (this._blendFunc = e, this._updateBlendFunc());
		else if (this._blendFunc.src != e || this._blendFunc.dst != t) this._blendFunc = {
			src: e,
			dst: t
		}, this._updateBlendFunc()
	},
	_opacityModifyRGB: !1,
	getOpacityModifyRGB: function() {
		return this._opacityModifyRGB
	},
	setOpacityModifyRGB: function(e) {
		this._opacityModifyRGB = e
	},
	_isBlendAdditive: !1,
	isBlendAdditive: function() {
		return this._blendFunc.src == gl.SRC_ALPHA && this._blendFunc.dst == gl.ONE || this._blendFunc.src == gl.ONE && this._blendFunc.dst == gl.ONE
	},
	setBlendAdditive: function(e) {
		this._isBlendAdditive = e, e ? (this._blendFunc.src = gl.SRC_ALPHA, this._blendFunc.dst = gl.ONE) : (this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST)
	},
	_positionType: cc.PARTICLE_TYPE_FREE,
	getPositionType: function() {
		return this._positionType
	},
	setPositionType: function(e) {
		this._positionType = e
	},
	_isAutoRemoveOnFinish: !1,
	isAutoRemoveOnFinish: function() {
		return this._isAutoRemoveOnFinish
	},
	setAutoRemoveOnFinish: function(e) {
		this._isAutoRemoveOnFinish = e
	},
	_emitterMode: 0,
	getEmitterMode: function() {
		return this._emitterMode
	},
	setEmitterMode: function(e) {
		this._emitterMode = e
	},
	ctor: function() {
		this._super(), this._emitterMode = cc.PARTICLE_MODE_GRAVITY, this.modeA = new cc.ParticleSystem.ModeA(), this.modeB = new cc.ParticleSystem.ModeB(), this._blendFunc = {
			src: cc.BLEND_SRC,
			dst: cc.BLEND_DST
		}, this._particles = [], this._sourcePosition = new cc.Point(0, 0), this._posVar = new cc.Point(0, 0), this._startColor = new cc.Color4F(1, 1, 1, 1), this._startColorVar = new cc.Color4F(1, 1, 1, 1), this._endColor = new cc.Color4F(1, 1, 1, 1), this._endColorVar = new cc.Color4F(1, 1, 1, 1), this._particlePool = []
	},
	init: function() {
		return this.initWithTotalParticles(150)
	},
	initWithFile: function(e) {
		var t = !1;
		this._plistFile = e;
		var n = cc.FileUtils.getInstance().dictionaryWithContentsOfFileThreadSafe(this._plistFile);
		return cc.Assert(n != null, "Particles: file not found"), this.initWithDictionary(n)
	},
	getBoundingBoxToWorld: function() {
		return cc.rect(0, 0, cc.canvas.width, cc.canvas.height)
	},
	initWithDictionary: function(e) {
		var t = !1,
			n = null,
			r = null,
			i = null,
			s = parseInt(this._valueForKey("maxParticles", e));
		if (this.initWithTotalParticles(s)) {
			this._angle = parseFloat(this._valueForKey("angle", e)), this._angleVar = parseFloat(this._valueForKey("angleVariance", e)), this._duration = parseFloat(this._valueForKey("duration", e)), this._blendFunc.src = parseInt(this._valueForKey("blendFuncSource", e)), this._blendFunc.dst = parseInt(this._valueForKey("blendFuncDestination", e)), this._startColor.r = parseFloat(this._valueForKey("startColorRed", e)), this._startColor.g = parseFloat(this._valueForKey("startColorGreen", e)), this._startColor.b = parseFloat(this._valueForKey("startColorBlue", e)), this._startColor.a = parseFloat(this._valueForKey("startColorAlpha", e)), this._startColorVar.r = parseFloat(this._valueForKey("startColorVarianceRed", e)), this._startColorVar.g = parseFloat(this._valueForKey("startColorVarianceGreen", e)), this._startColorVar.b = parseFloat(this._valueForKey("startColorVarianceBlue", e)), this._startColorVar.a = parseFloat(this._valueForKey("startColorVarianceAlpha", e)), this._endColor.r = parseFloat(this._valueForKey("finishColorRed", e)), this._endColor.g = parseFloat(this._valueForKey("finishColorGreen", e)), this._endColor.b = parseFloat(this._valueForKey("finishColorBlue", e)), this._endColor.a = parseFloat(this._valueForKey("finishColorAlpha", e)), this._endColorVar.r = parseFloat(this._valueForKey("finishColorVarianceRed", e)), this._endColorVar.g = parseFloat(this._valueForKey("finishColorVarianceGreen", e)), this._endColorVar.b = parseFloat(this._valueForKey("finishColorVarianceBlue", e)), this._endColorVar.a = parseFloat(this._valueForKey("finishColorVarianceAlpha", e)), this._startSize = parseFloat(this._valueForKey("startParticleSize", e)), this._startSizeVar = parseFloat(this._valueForKey("startParticleSizeVariance", e)), this._endSize = parseFloat(this._valueForKey("finishParticleSize", e)), this._endSizeVar = parseFloat(this._valueForKey("finishParticleSizeVariance", e));
			var o = parseFloat(this._valueForKey("sourcePositionx", e)),
				u = parseFloat(this._valueForKey("sourcePositiony", e));
			this.setPosition(cc.p(o, u)), this._posVar.x = parseFloat(this._valueForKey("sourcePositionVariancex", e)), this._posVar.y = parseFloat(this._valueForKey("sourcePositionVariancey", e)), this._startSpin = parseFloat(this._valueForKey("rotationStart", e)), this._startSpinVar = parseFloat(this._valueForKey("rotationStartVariance", e)), this._endSpin = parseFloat(this._valueForKey("rotationEnd", e)), this._endSpinVar = parseFloat(this._valueForKey("rotationEndVariance", e)), this._emitterMode = parseInt(this._valueForKey("emitterType", e));
			if (this._emitterMode == cc.PARTICLE_MODE_GRAVITY) {
				this.modeA.gravity.x = parseFloat(this._valueForKey("gravityx", e)), this.modeA.gravity.y = parseFloat(this._valueForKey("gravityy", e)), this.modeA.speed = parseFloat(this._valueForKey("speed", e)), this.modeA.speedVar = parseFloat(this._valueForKey("speedVariance", e));
				var a = this._valueForKey("radialAcceleration", e);
				this.modeA.radialAccel = a ? parseFloat(a) : 0, a = this._valueForKey("radialAccelVariance", e), this.modeA.radialAccelVar = a ? parseFloat(a) : 0, a = this._valueForKey("tangentialAcceleration", e), this.modeA.tangentialAccel = a ? parseFloat(a) : 0, a = this._valueForKey("tangentialAccelVariance", e), this.modeA.tangentialAccelVar = a ? parseFloat(a) : 0
			} else {
				if (this._emitterMode != cc.PARTICLE_MODE_RADIUS) return cc.Assert(!1, "Invalid emitterType in config file"), !1;
				this.modeB.startRadius = parseFloat(this._valueForKey("maxRadius", e)), this.modeB.startRadiusVar = parseFloat(this._valueForKey("maxRadiusVariance", e)), this.modeB.endRadius = parseFloat(this._valueForKey("minRadius", e)), this.modeB.endRadiusVar = 0, this.modeB.rotatePerSecond = parseFloat(this._valueForKey("rotatePerSecond", e)), this.modeB.rotatePerSecondVar = parseFloat(this._valueForKey("rotatePerSecondVariance", e))
			}
			this._life = parseFloat(this._valueForKey("particleLifespan", e)), this._lifeVar = parseFloat(this._valueForKey("particleLifespanVariance", e)), this._emissionRate = this._totalParticles / this._life;
			if (!this._batchNode) {
				this._opacityModifyRGB = !1;
				var f = this._valueForKey("textureFileName", e),
					l = cc.FileUtils.getInstance().fullPathFromRelativeFile(f, this._plistFile),
					c = cc.TextureCache.getInstance().textureForKey(l);
				if (c) this._texture = c;
				else {
					var h = this._valueForKey("textureImageData", e);
					if (h && h.length == 0) {
						cc.Assert(h, "cc.ParticleSystem.initWithDictory:textureImageData is null"), c = cc.TextureCache.getInstance().addImage(l);
						if (!c) return !1;
						this._texture = c
					} else {
						n = cc.unzipBase64AsArray(h, 1);
						if (!n) return !1;
						var p = cc.encodeToBase64(n);
						if (!p) return !1;
						var d = new Image();
						d.src = "data:image/png;base64," + p, this._texture = d, cc.TextureCache.getInstance().cacheImage(l, d)
					}
				}
				cc.Assert(this._texture != null, "cc.ParticleSystem: error loading the texture")
			}
			t = !0
		}
		return t
	},
	initWithTotalParticles: function(e) {
		this._totalParticles = e, this._particles = [], this._particlePool = [];
		if (!this._particles) return cc.log("Particle system: not enough memory"), !1;
		this._allocatedParticles = e;
		if (this._batchNode) for (var t = 0; t < this._totalParticles; t++) this._particles[t].atlasIndex = t;
		return this._isActive = !0, this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, this._positionType = cc.PARTICLE_TYPE_FREE, this._emitterMode = cc.PARTICLE_MODE_GRAVITY, this._isAutoRemoveOnFinish = !1, this._transformSystemDirty = !1, this.scheduleUpdateWithPriority(1), !0
	},
	destroyParticleSystem: function() {
		this._particlePool = null, this.unscheduleUpdate()
	},
	_getParticleObject: function() {
		return this._particlePool.length > 0 ? this._particlePool.pop() : new cc.Particle()
	},
	addParticle: function() {
		if (this.isFull()) return !1;
		var e = this._getParticleObject();
		return this.initParticle(e), this._particles.push(e), ++this._particleCount, !0
	},
	initParticle: function(e) {
		e.timeToLive = this._life + this._lifeVar * cc.RANDOM_MINUS1_1(), e.timeToLive = Math.max(0, e.timeToLive), e.pos.x = this._sourcePosition.x + this._posVar.x * cc.RANDOM_MINUS1_1(), e.pos.y = this._sourcePosition.y + this._posVar.y * cc.RANDOM_MINUS1_1();
		var t = new cc.Color4F(cc.clampf(this._startColor.r + this._startColorVar.r * cc.RANDOM_MINUS1_1(), 0, 1), cc.clampf(this._startColor.g + this._startColorVar.g * cc.RANDOM_MINUS1_1(), 0, 1), cc.clampf(this._startColor.b + this._startColorVar.b * cc.RANDOM_MINUS1_1(), 0, 1), cc.clampf(this._startColor.a + this._startColorVar.a * cc.RANDOM_MINUS1_1(), 0, 1)),
			n = new cc.Color4F(cc.clampf(this._endColor.r + this._endColorVar.r * cc.RANDOM_MINUS1_1(), 0, 1), cc.clampf(this._endColor.g + this._endColorVar.g * cc.RANDOM_MINUS1_1(), 0, 1), cc.clampf(this._endColor.b + this._endColorVar.b * cc.RANDOM_MINUS1_1(), 0, 1), cc.clampf(this._endColor.a + this._endColorVar.a * cc.RANDOM_MINUS1_1(), 0, 1));
		e.color = t, e.deltaColor.r = (n.r - t.r) / e.timeToLive, e.deltaColor.g = (n.g - t.g) / e.timeToLive, e.deltaColor.b = (n.b - t.b) / e.timeToLive, e.deltaColor.a = (n.a - t.a) / e.timeToLive;
		var r = this._startSize + this._startSizeVar * cc.RANDOM_MINUS1_1();
		r = Math.max(0, r), e.size = r;
		if (this._endSize == cc.PARTICLE_START_SIZE_EQUAL_TO_END_SIZE) e.deltaSize = 0;
		else {
			var i = this._endSize + this._endSizeVar * cc.RANDOM_MINUS1_1();
			i = Math.max(0, i), e.deltaSize = (i - r) / e.timeToLive
		}
		var s = this._startSpin + this._startSpinVar * cc.RANDOM_MINUS1_1(),
			o = this._endSpin + this._endSpinVar * cc.RANDOM_MINUS1_1();
		e.rotation = s, e.deltaRotation = (o - s) / e.timeToLive, this._positionType == cc.PARTICLE_TYPE_FREE ? e.startPos = this.convertToWorldSpace(this._pointZeroForParticle) : this._positionType == cc.PARTICLE_TYPE_RELATIVE && (e.startPos = this._position);
		var u = cc.DEGREES_TO_RADIANS(this._angle + this._angleVar * cc.RANDOM_MINUS1_1());
		if (this._emitterMode == cc.PARTICLE_MODE_GRAVITY) {
			var a = cc.p(Math.cos(u), Math.sin(u)),
				f = this.modeA.speed + this.modeA.speedVar * cc.RANDOM_MINUS1_1();
			e.modeA.dir = cc.pMult(a, f), e.modeA.radialAccel = this.modeA.radialAccel + this.modeA.radialAccelVar * cc.RANDOM_MINUS1_1(), e.modeA.tangentialAccel = this.modeA.tangentialAccel + this.modeA.tangentialAccelVar * cc.RANDOM_MINUS1_1()
		} else {
			var l = this.modeB.startRadius + this.modeB.startRadiusVar * cc.RANDOM_MINUS1_1(),
				c = this.modeB.endRadius + this.modeB.endRadiusVar * cc.RANDOM_MINUS1_1();
			e.modeB.radius = l, this.modeB.endRadius == cc.PARTICLE_START_RADIUS_EQUAL_TO_END_RADIUS ? e.modeB.deltaRadius = 0 : e.modeB.deltaRadius = (c - l) / e.timeToLive, e.modeB.angle = u, e.modeB.degreesPerSecond = cc.DEGREES_TO_RADIANS(this.modeB.rotatePerSecond + this.modeB.rotatePerSecondVar * cc.RANDOM_MINUS1_1())
		}
	},
	stopSystem: function() {
		this._isActive = !1, this._elapsed = this._duration, this._emitCounter = 0, this._particlePool = []
	},
	resetSystem: function() {
		this._isActive = !0, this._elapsed = 0;
		for (this._particleIdx = 0; this._particleIdx < this._particleCount; ++this._particleIdx) {
			var e = this._particles[this._particleIdx];
			e.timeToLive = 0
		}
	},
	isFull: function() {
		return this._particleCount >= this._totalParticles
	},
	updateQuadWithParticle: function(e, t) {},
	postStep: function() {},
	update: function(e) {
		if (this._isActive && this._emissionRate) {
			var t = 1 / this._emissionRate;
			this._particleCount < this._totalParticles && (this._emitCounter += e);
			while (this._particleCount < this._totalParticles && this._emitCounter > t) this.addParticle(), this._emitCounter -= t;
			this._elapsed += e, this._duration != -1 && this._duration < this._elapsed && this.stopSystem()
		}
		this._particleIdx = 0;
		var n;
		this._positionType == cc.PARTICLE_TYPE_FREE ? n = this.convertToWorldSpace(this._pointZeroForParticle) : this._positionType == cc.PARTICLE_TYPE_RELATIVE && (n = cc.p(this._position.x, this._position.y));
		if (this._visible) {
			while (this._particleIdx < this._particleCount) {
				var r = this._particles[this._particleIdx];
				r.timeToLive -= e;
				if (r.timeToLive > 0) {
					if (this._emitterMode == cc.PARTICLE_MODE_GRAVITY) {
						var i, s, o;
						r.pos.x || r.pos.y ? s = cc.pNormalize(r.pos) : s = cc.PointZero(), o = s, s = cc.pMult(s, r.modeA.radialAccel);
						var u = o.x;
						o.x = -o.y, o.y = u, o = cc.pMult(o, r.modeA.tangentialAccel), i = cc.pAdd(cc.pAdd(s, o), this.modeA.gravity), i = cc.pMult(i, e), r.modeA.dir = cc.pAdd(r.modeA.dir, i), i = cc.pMult(r.modeA.dir, e), r.pos = cc.pAdd(r.pos, i)
					} else r.modeB.angle += r.modeB.degreesPerSecond * e, r.modeB.radius += r.modeB.deltaRadius * e, r.pos.x = -Math.cos(r.modeB.angle) * r.modeB.radius, r.pos.y = -Math.sin(r.modeB.angle) * r.modeB.radius;
					this._dontTint || (r.color.r += r.deltaColor.r * e, r.color.g += r.deltaColor.g * e, r.color.b += r.deltaColor.b * e, r.color.a += r.deltaColor.a * e, r.isChangeColor = !0), r.size += r.deltaSize * e, r.size = Math.max(0, r.size), r.rotation += r.deltaRotation * e;
					var a;
					if (this._positionType == cc.PARTICLE_TYPE_FREE || this._positionType == cc.PARTICLE_TYPE_RELATIVE) {
						var f = cc.pSub(n, r.startPos);
						a = cc.pSub(r.pos, f)
					} else a = r.pos;
					this._batchNode && (a.x += this._position.x, a.y += this._position.y), cc.renderContextType == cc.WEBGL ? this.updateQuadWithParticle(r, a) : r.drawPos = a, ++this._particleIdx
				} else {
					var l = r.atlasIndex;
					cc.ArrayRemoveObject(this._particles, r), this._particlePool.push(r), this._batchNode && (this._batchNode.disableParticle(this._atlasIndex + l), this._particles[this._particleCount - 1].atlasIndex = l), --this._particleCount;
					if (this._particleCount == 0 && this._isAutoRemoveOnFinish) {
						this.unscheduleUpdate(), this._parent.removeChild(this, !0);
						return
					}
				}
			}
			this._transformSystemDirty = !1
		}
		this._batchNode || this.postStep()
	},
	updateWithNoTime: function() {
		this.update(0)
	},
	_valueForKey: function(e, t) {
		if (t) {
			var n = t[e];
			return n != null ? n : ""
		}
		return ""
	},
	_updateBlendFunc: function() {
		cc.Assert(!this._batchNode, "Can't change blending functions when the particle is being batched");
		if (this._texture && !(this._texture instanceof HTMLImageElement || this._texture instanceof HTMLCanvasElement)) {
			var e = this._texture.hasPremultipliedAlpha();
			this._opacityModifyRGB = !1, this._texture && this._blendFunc.src == cc.BLEND_SRC && this._blendFunc.dst == cc.BLEND_DST && (e ? this._opacityModifyRGB = !0 : (this._blendFunc.src = gl.SRC_ALPHA, this._blendFunc.dst = gl.ONE_MINUS_SRC_ALPHA))
		}
	}
}), function() {
	var e = typeof window != "undefined" ? window : exports,
		t = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
		n = function() {
			try {
				document.createElement("$")
			} catch (e) {
				return e
			}
		}();
	e.btoa || (e.btoa = function(e) {
		for (var r, i, s = 0, o = t, u = ""; e.charAt(s | 0) || (o = "=", s % 1); u += o.charAt(63 & r >> 8 - s % 1 * 8)) {
			i = e.charCodeAt(s += .75);
			if (i > 255) throw n;
			r = r << 8 | i
		}
		return u
	}), e.atob || (e.atob = function(e) {
		e = e.replace(/=+$/, "");
		if (e.length % 4 == 1) throw n;
		for (var r = 0, i, s, o = 0, u = ""; s = e.charAt(o++);~s && (i = r % 4 ? i * 64 + s : s, r++ % 4) ? u += String.fromCharCode(255 & i >> (-2 * r & 6)) : 0) s = t.indexOf(s);
		return u
	})
}(), cc.encodeToBase64 = function(e) {
	return btoa(String.fromCharCode.apply(e, e)).replace(/.{76}(?=.)/g, "$&\n")
}, cc.ParticleSystem.create = function(e) {
	return cc.ParticleSystemQuad.create(e)
}, cc.ParticleSystem.createWithTotalParticles = function(e) {
	var t = cc.ParticleSystemQuad.create(e);
	return t
}, cc.ParticleSystem.ModeA = function(e, t, n, r, i, s, o) {
	this.gravity = e ? e : cc.PointZero(), this.speed = t || 0, this.speedVar = n || 0, this.tangentialAccel = r || 0, this.tangentialAccelVar = i || 0, this.radialAccel = s || 0, this.radialAccelVar = o || 0
}, cc.ParticleSystem.ModeB = function(e, t, n, r, i, s) {
	this.startRadius = e || 0, this.startRadiusVar = t || 0, this.endRadius = n || 0, this.endRadiusVar = r || 0, this.rotatePerSecond = i || 0, this.rotatePerSecondVar = s || 0
};
cc.ParticleSystemQuad = cc.ParticleSystem.extend({
	_quads: null,
	_indices: null,
	_VAOname: 0,
	_buffersVBO: [],
	_pointRect: null,
	ctor: function() {
		this._super(), this._buffersVBO = [0, 0], this._quads = [], this._indices = [], this._pointRect = cc.RectZero()
	},
	setupIndices: function() {
		for (var e = 0; e < this._totalParticles; ++e) {
			var t = e * 6,
				n = e * 4;
			this._indices[t + 0] = n + 0, this._indices[t + 1] = n + 1, this._indices[t + 2] = n + 2, this._indices[t + 5] = n + 1, this._indices[t + 4] = n + 2, this._indices[t + 3] = n + 3
		}
	},
	initTexCoordsWithRect: function(e) {
		var t = cc.rect(e.origin.x * cc.CONTENT_SCALE_FACTOR(), e.origin.y * cc.CONTENT_SCALE_FACTOR(), e.size.width * cc.CONTENT_SCALE_FACTOR(), e.size.height * cc.CONTENT_SCALE_FACTOR()),
			n = e.size.width,
			r = e.size.height;
		this._texture && (this._texture instanceof HTMLImageElement || this._texture instanceof HTMLCanvasElement ? (n = this._texture.width, r = this._texture.height) : (n = this._texture.getPixelsWide(), r = this._texture.getPixelsHigh()));
		var i, s, o, u;
		cc.FIX_ARTIFACTS_BY_STRECHING_TEXEL ? (i = (t.origin.x * 2 + 1) / (n * 2), s = (t.origin.y * 2 + 1) / (r * 2), o = i + (t.size.width * 2 - 2) / (n * 2), u = s + (t.size.height * 2 - 2) / (r * 2)) : (i = t.origin.x / n, s = t.origin.y / r, o = i + t.size.width / n, u = s + t.size.height / r);
		var a = u;
		u = s, s = a;
		var f = null,
			l = 0,
			c = 0;
		this._batchNode ? (f = this._batchNode.getTextureAtlas().getQuads(), l = this._atlasIndex, c = this._atlasIndex + this._totalParticles) : (f = this._quads, l = 0, c = this._totalParticles);
		for (var h = l; h < this.end; h++) f[h] || (f[h] = cc.V3F_C4B_T2F_QuadZero()), f[h].bl.texCoords.u = i, f[h].bl.texCoords.v = s, f[h].br.texCoords.u = o, f[h].br.texCoords.v = s, f[h].tl.texCoords.u = i, f[h].tl.texCoords.v = u, f[h].tr.texCoords.u = o, f[h].tr.texCoords.v = u
	},
	clone: function() {
		var e = new cc.ParticleSystemQuad();
		return e.initWithTotalParticles(this._totalParticles) && (e._angle = this._angle, e._angleVar = this._angleVar, e._duration = this._duration, e._blendFunc.src = this._blendFunc.src, e._blendFunc.dst = this._blendFunc.dst, e._startColor.r = this._startColor.r, e._startColor.g = this._startColor.g, e._startColor.b = this._startColor.b, e._startColor.a = this._startColor.a, e._startColorVar.r = this._startColorVar.r, e._startColorVar.g = this._startColorVar.g, e._startColorVar.b = this._startColorVar.b, e._startColorVar.a = this._startColorVar.a, e._endColor.r = this._endColor.r, e._endColor.g = this._endColor.g, e._endColor.b = this._endColor.b, e._endColor.a = this._endColor.a, e._endColorVar.r = this._endColorVar.r, e._endColorVar.g = this._endColorVar.g, e._endColorVar.b = this._endColorVar.b, e._endColorVar.a = this._endColorVar.a, e._startSize = this._startSize, e._startSizeVar = this._startSizeVar, e._endSize = this._endSize, e._endSizeVar = this._endSizeVar, e.setPosition(new cc.Point(this._position.x, this._position.y)), e._posVar.x = this._posVar.x, e._posVar.y = this._posVar.y, e._startSpin = this._startSpin, e._startSpinVar = this._startSpinVar, e._endSpin = this._endSpin, e._endSpinVar = this._endSpinVar, e._emitterMode = this._emitterMode, this._emitterMode == cc.PARTICLE_MODE_GRAVITY ? (e.modeA.gravity.x = this.modeA.gravity.x, e.modeA.gravity.y = this.modeA.gravity.y, e.modeA.speed = this.modeA.speed, e.modeA.speedVar = this.modeA.speedVar, e.modeA.radialAccel = this.modeA.radialAccel, e.modeA.radialAccelVar = this.modeA.radialAccelVar, e.modeA.tangentialAccel = this.modeA.tangentialAccel, e.modeA.tangentialAccelVar = this.modeA.tangentialAccelVar) : this._emitterMode == cc.PARTICLE_MODE_RADIUS && (e.modeB.startRadius = this.modeB.startRadius, e.modeB.startRadiusVar = this.modeB.startRadiusVar, e.modeB.endRadius = this.modeB.endRadius, e.modeB.endRadiusVar = this.modeB.endRadiusVar, e.modeB.rotatePerSecond = this.modeB.rotatePerSecond, e.modeB.rotatePerSecondVar = this.modeB.rotatePerSecondVar), e._life = this._life, e._lifeVar = this._lifeVar, e._emissionRate = this._emissionRate, this._batchNode || (e._opacityModifyRGB = this._opacityModifyRGB, this._texture instanceof cc.Texture2D ? e._texture = this._texture : e._texture = this._texture)), e
	},
	setDisplayFrame: function(e) {
		cc.Assert(cc.Point.CCPointEqualToPoint(e.getOffsetInPixels(), cc.PointZero()), "QuadParticle only supports SpriteFrames with no offsets"), (!this._texture || e.getTexture().getName() != this._texture.getName()) && this.setTexture(e.getTexture())
	},
	setTextureWithRect: function(e, t) {
		e instanceof cc.Texture2D && ((!this._texture || e.getName() != this._texture.getName()) && this.setTexture(e, !0), this._pointRect = t, this.initTexCoordsWithRect(t)), e instanceof HTMLImageElement && ((!this._texture || e != this._texture) && this.setTexture(e, !0), this._pointRect = t, this.initTexCoordsWithRect(t))
	},
	initWithTotalParticles: function(e) {
		return this._super(e) ? this._allocMemory() ? (this.setupIndices(), cc.TEXTURE_ATLAS_USE_VAO ? this._setupVBOandVAO() : this._setupVBO(), !0) : !1 : !1
	},
	setTexture: function(e, t) {
		if (t && t == 1) {
			this._super(e);
			return
		}
		var n = null;
		e instanceof HTMLImageElement || e instanceof HTMLCanvasElement ? n = cc.size(e.width, e.height) : n = e.getContentSize(), this.setTextureWithRect(e, cc.rect(0, 0, n.width, n.height))
	},
	updateQuadWithParticle: function(e, t) {
		var n = null;
		if (this._batchNode) {
			var r = this._batchNode.getTextureAtlas().getQuads();
			n = r[this._atlasIndex + e.atlasIndex]
		} else n = this._quads[this._particleIdx];
		var i = this._opacityModifyRGB ? new cc.Color4B(0 | e.color.r * e.color.a * 255, 0 | e.color.g * e.color.a * 255, 0 | e.color.b * e.color.a * 255, 0 | e.color.a * 255) : new cc.Color4B(0 | e.color.r * 255, 0 | e.color.g * 255, 0 | e.color.b * 255, 0 | e.color.a * 255);
		n.bl.colors = i, n.br.colors = i, n.tl.colors = i, n.tr.colors = i;
		var s = e.size / 2;
		if (e.rotation) {
			var o = -s,
				u = -s,
				a = s,
				f = s,
				l = t.x,
				c = t.y,
				h = -cc.DEGREES_TO_RADIANS(e.rotation),
				p = Math.cos(h),
				d = Math.sin(h),
				v = o * p - u * d + l,
				m = o * d + u * p + c,
				g = a * p - u * d + l,
				y = a * d + u * p + c,
				b = a * p - f * d + l,
				w = a * d + f * p + c,
				E = o * p - f * d + l,
				S = o * d + f * p + c;
			n.bl.vertices.x = v, n.bl.vertices.y = m, n.br.vertices.x = g, n.br.vertices.y = y, n.tl.vertices.x = E, n.tl.vertices.y = S, n.tr.vertices.x = b, n.tr.vertices.y = w
		} else n.bl.vertices.x = t.x - s, n.bl.vertices.y = t.y - s, n.br.vertices.x = t.x + s, n.br.vertices.y = t.y - s, n.tl.vertices.x = t.x - s, n.tl.vertices.y = t.y + s, n.tr.vertices.x = t.x + s, n.tr.vertices.y = t.y + s
	},
	postStep: function() {
		cc.renderContextType != cc.CANVAS && (glBindBuffer(GL_ARRAY_BUFFER, this._buffersVBO[0]), glBufferSubData(GL_ARRAY_BUFFER, 0, sizeof(this._quads[0]) * particleCount, this._quads), glBindBuffer(GL_ARRAY_BUFFER, 0), CHECK_GL_ERROR_DEBUG())
	},
	draw: function(e) {
		cc.Assert(!this._batchNode, "draw should not be called when added to a particleBatchNode");
		var t = e || cc.renderContext;
		t.save(), this.isBlendAdditive() ? t.globalCompositeOperation = "lighter" : t.globalCompositeOperation = "source-over";
		for (var n = 0; n < this._particleCount; n++) {
			var r = this._particles[n],
				i = 0 | r.size * .5;
			if (this._drawMode == cc.PARTICLE_TEXTURE_MODE) {
				var s = this.getTexture();
				if (r.isChangeColor) {
					var o = cc.TextureCache.getInstance().getTextureColors(this.getTexture());
					o && (s = cc.generateTintImage(this.getTexture(), o, r.color, this._pointRect))
				}
				t.save(), t.globalAlpha = r.color.a, t.translate(0 | r.drawPos.x, -(0 | r.drawPos.y)), r.rotation && t.rotate(cc.DEGREES_TO_RADIANS(r.rotation)), t.drawImage(s, -i, -i, r.size, r.size), t.restore()
			} else t.save(), t.globalAlpha = r.color.a, t.translate(0 | r.drawPos.x, -(0 | r.drawPos.y)), this._shapeType == cc.PARTICLE_STAR_SHAPE ? (r.rotation && t.rotate(cc.DEGREES_TO_RADIANS(r.rotation)), cc.drawingUtil.drawStar(t, i, r.color)) : cc.drawingUtil.drawColorBall(t, i, r.color), t.restore()
		}
		t.restore(), cc.INCREMENT_GL_DRAWS(1)
	},
	_drawForWebGL: function(e) {
		cc.NODE_DRAW_SETUP(), ccGLBindTexture2D(this._texture.getName()), ccGLBlendFunc(m_tBlendFunc.src, m_tBlendFunc.dst), cc.Assert(this._particleIdx == this._particleCount, "Abnormal error in particle quad");
		if (cc.TEXTURE_ATLAS_USE_VAO) glBindVertexArray(this._VAOname), cc.REBIND_INDICES_BUFFER && glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, this._buffersVBO[1]), glDrawElements(GL_TRIANGLES, this._particleIdx * 6, GL_UNSIGNED_SHORT, 0), cc.REBIND_INDICES_BUFFER && glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, 0), glBindVertexArray(0);
		else {
			var t = sizeof(m_pQuads[0].bl);
			ccGLEnableVertexAttribs(kCCVertexAttribFlag_PosColorTex), glBindBuffer(GL_ARRAY_BUFFER, this._buffersVBO[0]), glVertexAttribPointer(kCCVertexAttrib_Position, 3, GL_FLOAT, GL_FALSE, t, offsetof(ccV3F_C4B_T2F, vertices)), glVertexAttribPointer(kCCVertexAttrib_Color, 4, GL_UNSIGNED_BYTE, GL_TRUE, t, offsetof(ccV3F_C4B_T2F, colors)), glVertexAttribPointer(kCCVertexAttrib_TexCoords, 2, GL_FLOAT, GL_FALSE, t, offsetof(ccV3F_C4B_T2F, texCoords)), glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, this._buffersVBO[1]), glDrawElements(GL_TRIANGLES, this._particleIdx * 6, GL_UNSIGNED_SHORT, 0), glBindBuffer(GL_ARRAY_BUFFER, 0), glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, 0)
		}
		CHECK_GL_ERROR_DEBUG(), cc.INCREMENT_GL_DRAWS(1)
	},
	setBatchNode: function(e) {
		if (this._batchNode != e) {
			var t = this._batchNode;
			this._super(e);
			if (!e) this._allocMemory(), this.setupIndices(), this.setTexture(t.getTexture()), cc.TEXTURE_ATLAS_USE_VAO ? this._setupVBOandVAO() : this._setupVBO();
			else if (!t) {
				var n = this._batchNode.getTextureAtlas().getQuads(),
					r = n[this._atlasIndex];
				glDeleteBuffers(2, this._buffersVBO[0]), cc.TEXTURE_ATLAS_USE_VAO && glDeleteVertexArrays(1, this._VAOname)
			}
		}
	},
	setTotalParticles: function(e) {
		e < 200 ? this._totalParticles = e : this._totalParticles = 200;
		return
	},
	listenBackToForeground: function(e) {
		cc.TEXTURE_ATLAS_USE_VAO ? this._setupVBOandVAO() : this._setupVBO()
	},
	_setupVBOandVAO: function() {
		if (cc.renderContextType == cc.CANVAS) return;
		glGenVertexArrays(1, this._VAOname), glBindVertexArray(this._VAOname);
		var e = sizeof(m_pQuads[0].bl);
		glGenBuffers(2, this._buffersVBO[0]), glBindBuffer(GL_ARRAY_BUFFER, this._buffersVBO[0]), glBufferData(GL_ARRAY_BUFFER, sizeof(this._quads[0]) * this._totalParticles, this._quads, GL_DYNAMIC_DRAW), glEnableVertexAttribArray(kCCVertexAttrib_Position), glVertexAttribPointer(kCCVertexAttrib_Position, 2, GL_FLOAT, GL_FALSE, e, offsetof(ccV3F_C4B_T2F, vertices)), glEnableVertexAttribArray(kCCVertexAttrib_Color), glVertexAttribPointer(kCCVertexAttrib_Color, 4, GL_UNSIGNED_BYTE, GL_TRUE, e, offsetof(ccV3F_C4B_T2F, colors)), glEnableVertexAttribArray(kCCVertexAttrib_TexCoords), glVertexAttribPointer(kCCVertexAttrib_TexCoords, 2, GL_FLOAT, GL_FALSE, e, offsetof(ccV3F_C4B_T2F, texCoords)), glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, this._buffersVBO[1]), glBufferData(GL_ELEMENT_ARRAY_BUFFER, sizeof(m_pIndices[0]) * m_uTotalParticles * 6, m_pIndices, GL_STATIC_DRAW), glBindVertexArray(0), glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, 0), glBindBuffer(GL_ARRAY_BUFFER, 0), CHECK_GL_ERROR_DEBUG()
	},
	_setupVBO: function() {
		if (cc.renderContextType == cc.CANVAS) return;
		glGenBuffers(2, this._buffersVBO[0]), glBindBuffer(GL_ARRAY_BUFFER, this._buffersVBO[0]), glBufferData(GL_ARRAY_BUFFER, sizeof(m_pQuads[0]) * m_uTotalParticles, m_pQuads, GL_DYNAMIC_DRAW), glBindBuffer(GL_ARRAY_BUFFER, 0), glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, this._buffersVBO[1]), glBufferData(GL_ELEMENT_ARRAY_BUFFER, sizeof(m_pIndices[0]) * m_uTotalParticles * 6, m_pIndices, GL_STATIC_DRAW), glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, 0), CHECK_GL_ERROR_DEBUG()
	},
	_allocMemory: function() {
		cc.Assert(!this._batchNode, "Memory should not be alloced when not using batchNode"), this._quads = [], this._indices = [];
		for (var e = 0; e < this._totalParticles; e++) this._quads[e] = new cc.V3F_C4B_T2F_Quad(), this._indices[e * 6] = 0, this._indices[e * 6 + 1] = 0, this._indices[e * 6 + 2] = 0, this._indices[e * 6 + 3] = 0, this._indices[e * 6 + 4] = 0, this._indices[e * 6 + 5] = 0;
		return !this._quads || !this._indices ? (cc.log("cocos2d: Particle system: not enough memory"), !1) : !0
	}
}), cc.ParticleSystemQuad.create = function(e) {
	var t = new cc.ParticleSystemQuad();
	if (!e || typeof e == "number") {
		var n = e || 100;
		return t.setDrawMode(cc.PARTICLE_TEXTURE_MODE), t.initWithTotalParticles(n), t
	}
	return t && t.initWithFile(e) ? t : null
}, cc.ARCH_OPTIMAL_PARTICLE_SYSTEM = cc.ParticleSystemQuad;
cc.PARTICLE_DEFAULT_CAPACITY = 100, cc.ParticleBatchNode = cc.Node.extend({
	TextureProtocol: !0,
	_blendFunc: {
		src: cc.BLEND_SRC,
		dst: cc.BLEND_DST
	},
	_textureAtlas: null,
	initWithTexture: function(e, t) {
		return this._textureAtlas = new cc.TextureAtlas(), this._textureAtlas.initWithTexture(e, t), this._children = [], this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST, !0
	},
	init: function(e, t) {
		var n = cc.TextureCache.getInstance().addImage(e);
		return this.initWithTexture(n, t)
	},
	addChild: function(e, t, n) {
		switch (arguments.length) {
		case 1:
			this._super(e);
			break;
		case 2:
			this._super(e, t);
			break;
		case 3:
			cc.Assert(e != null, "Argument must be non-NULL"), cc.Assert(e instanceof cc.ParticleSystem, "cc.ParticleBatchNode only supports cc.QuadParticleSystems as children"), cc.Assert(e.getTexture() == this._textureAtlas.getTexture(), "cc.ParticleSystem is not using the same texture id");
			if (this._children.length == 0) {
				var r = e.getBlendFunc();
				this.setBlendFunc(r.src, r.dst)
			}
			cc.Assert(this._blendFunc.src == e.getBlendFunc().src && this._blendFunc.dst == pChild.getBlendFunc().dst, "Can't add a PaticleSystem that uses a differnt blending function");
			var i = this._addChildHelper(pChild, t, n),
				s = 0;
			if (i != 0) {
				var o = this._children[i - 1];
				s = o.getAtlasIndex() + o.getTotalParticles()
			} else s = 0;
			this.insertChild(e, s), e.setBatchNode(this);
			break;
		default:
			throw "Argument must be non-nil "
		}
	},
	insertChild: function(e, t) {
		e.setAtlasIndex(t), this._textureAtlas.getTotalQuads() + e.getTotalParticles() > this._textureAtlas.getCapacity() && (this._increaseAtlasCapacityTo(this._textureAtlas.getTotalQuads() + e.getTotalParticles()), this._textureAtlas.fillWithEmptyQuadsFromIndex(this._textureAtlas.getCapacity() - e.getTotalParticles(), e.getTotalParticles())), e.getAtlasIndex() + e.getTotalParticles() != this._textureAtlas.getTotalQuads() && this._textureAtlas.moveQuadsFromIndex(t, t + e.getTotalParticles()), this._textureAtlas.increaseTotalQuadsWith(e.getTotalParticles()), this._updateAllAtlasIndexes()
	},
	removeChild: function(e, t) {
		if (e == null) return;
		cc.Assert(e instanceof cc.ParticleSystem, "cc.ParticleBatchNode only supports cc.QuadParticleSystems as children"), cc.Assert(this._children.indexOf(e) > -1, "cc.ParticleBatchNode doesn't contain the sprite. Can't remove it"), this._super(e, t), this._textureAtlas.removeQuadsAtIndex(e.getAtlasIndex(), pChild.getTotalParticles()), this._textureAtlas.fillWithEmptyQuadsFromIndex(this._textureAtlas.getTotalQuads(), e.getTotalParticles()), e.setBatchNode(null), this._updateAllAtlasIndexes()
	},
	reorderChild: function(e, t) {
		cc.Assert(e != null, "Child must be non-NULL"), cc.Assert(e instanceof cc.ParticleSystem, "cc.ParticleBatchNode only supports cc.QuadParticleSystems as children");
		if (t == e.getZOrder()) return;
		if (this._children.length > 1) {
			var n = this._getCurrentIndex(e, t);
			if (n.oldIndex != n.newIndex) {
				cc.ArrayRemoveObjectAtIndex(this._children, n.oldIndex), this._children = cc.ArrayAppendObjectToIndex(this._children, e, n.newIndex);
				var r = e.getAtlasIndex();
				this._updateAllAtlasIndexes();
				var i = 0;
				for (var s = 0; s < this._children.length; s++) {
					var o = this._children[s];
					if (o == e) {
						i = e.getAtlasIndex();
						break
					}
				}
				this._textureAtlas.moveQuadsFromIndex(r, e.getTotalParticles(), i), e.updateWithNoTime()
			}
		}
		e._setZOrder(t)
	},
	removeChildAtIndex: function(e, t) {
		this.removeChild(this._children[i], t)
	},
	removeAllChildren: function(e) {
		for (var t = 0; t < this._children.length; t++) this._children[t].setBatchNode(null);
		this._super(e), this._textureAtlas.removeAllQuads()
	},
	disableParticle: function(e) {
		var t = this._textureAtlas.getQuads()[e];
		t.br.vertices.x = t.br.vertices.y = t.tr.vertices.x = t.tr.vertices.y = t.tl.vertices.x = t.tl.vertices.y = t.bl.vertices.x = t.bl.vertices.y = 0
	},
	XXX_draw: function(e) {
		cc.PROFILER_STOP("CCParticleBatchNode - draw");
		if (this._textureAtlas.getTotalQuads() == 0) return;
		cc.NODE_DRAW_SETUP(), ccGLBlendFunc(m_tBlendFunc.src, m_tBlendFunc.dst), this._textureAtlas.drawQuads(), cc.PROFILER_STOP("CCParticleBatchNode - draw")
	},
	getTexture: function() {
		return this._textureAtlas.getTexture()
	},
	setTexture: function(e) {
		this._textureAtlas.setTexture(e), e && !e.hasPremultipliedAlpha() && m_tBlendFunc.src == gl.BLEND_SRC && m_tBlendFunc.dst == gl.BLEND_DST && (this._blendFunc.src = gl.SRC_ALPHA, this._blendFunc.dst = gl.ONE_MINUS_SRC_ALPHA)
	},
	setBlendFunc: function(e, t) {
		arguments.length == 1 ? this._blendFunc = e : this._blendFunc = {
			src: e,
			dst: t
		}
	},
	getBlendFunc: function() {
		return this._blendFunc
	},
	XXX_visit: function(e) {
		if (!this._visible) return;
		kmGLPushMatrix(), this._grid && this._grid.isActive() && (this._grid.beforeDraw(), this.transformAncestors()), this.transform(), this.draw(), this._grid && this._grid.isActive() && this._grid.afterDraw(this), kmGLPopMatrix()
	},
	_updateAllAtlasIndexes: function() {
		var e = 0;
		for (var t = 0; t < this._children[0].length; t++) {
			var n = this._children[t];
			n.setAtlasIndex(e), e += n.getTotalParticles()
		}
	},
	_increaseAtlasCapacityTo: function(e) {
		cc.log("cocos2d: cc.ParticleBatchNode: resizing TextureAtlas capacity from [" + this._textureAtlas.getCapacity() + "] to [" + e + "]."), this._textureAtlas.resizeCapacity(e) || (cc.log("cocos2d: WARNING: Not enough memory to resize the atlas"), cc.Assert(!1, "XXX: cc.ParticleBatchNode #increaseAtlasCapacity SHALL handle this assert"))
	},
	_searchNewPositionInChildrenForZ: function(e) {
		var t = this._children.length;
		for (var n = 0; n < t; n++) if (this._children[n].getZOrder() > e) return n;
		return t
	},
	_getCurrentIndex: function(e, t) {
		var n = !1,
			r = !1,
			i = 0,
			s = 0,
			o = 0,
			u = this._children.length;
		for (var a = 0; a < u; a++) {
			var f = this._children[a];
			if (f.getZOrder() > t && !r) {
				i = a, r = !0;
				if (n && r) break
			}
			if (e == f) {
				s = a, n = !0, r || (o = -1);
				if (n && r) break
			}
		}
		return r || (i = u), i += o, {
			newIndex: i,
			oldIndex: s
		}
	},
	_addChildHelper: function(e, t, n) {
		cc.Assert(e != null, "Argument must be non-nil"), cc.Assert(e.getParent() == null, "child already added. It can't be added again"), this._children || (this._children = []);
		var r = this._searchNewPositionInChildrenForZ(t);
		return this._children = cc.ArrayAppendObjectToIndex(this._children, e, r), e.setTag(n), e._setZOrder(t), e.setParent(this), this._running && (e.onEnter(), e.onEnterTransitionDidFinish()), r
	},
	_updateBlendFunc: function() {
		this._textureAtlas.getTexture().hasPremultipliedAlpha() || (this._blendFunc.src = gl.SRC_ALPHA, this._blendFunc.dst = gl.ONE_MINUS_SRC_ALPHA)
	},
	_getTextureAtlas: function() {},
	_setTextureAtlas: function(e) {}
}), cc.ParticleBatchNode.createWithTexture = function(e, t) {
	var n = new cc.ParticleBatchNode();
	return n && n.initWithTexture(e, t) ? n : null
}, cc.ParticleBatchNode.create = function(e, t) {
	var n = new cc.ParticleBatchNode();
	return n && n.init(e, t) ? n : null
};
cc.Touch = cc.Class.extend({
	_point: null,
	_prevPoint: cc.PointZero(),
	_id: 0,
	ctor: function(e, t, n) {
		this._point = cc.p(e || 0, t || 0), this._id = n || 0
	},
	getLocation: function() {
		return this._point
	},
	getPreviousLocation: function() {
		return this._prevPoint
	},
	getDelta: function() {
		return cc.pSub(this._point, this._prevPoint)
	},
	getID: function() {
		return this._id
	},
	getId: function() {
		return this._id
	},
	setTouchInfo: function(e, t, n) {
		this._prevPoint = this._point, this._point = cc.p(t || 0, n || 0), this._id = e
	},
	_setPrevPoint: function(e, t) {
		this._prevPoint = cc.p(e || 0, t || 0)
	}
}), cc.TouchDelegate = cc.Class.extend({
	_eventTypeFuncMap: null,
	onTouchBegan: function(e, t) {
		return !1
	},
	onTouchMoved: function(e, t) {},
	onTouchEnded: function(e, t) {},
	onTouchCancelled: function(e, t) {},
	onTouchesBegan: function(e, t) {},
	onTouchesMoved: function(e, t) {},
	onTouchesEnded: function(e, t) {},
	onTouchesCancelled: function(e, t) {},
	touchDelegateRetain: function() {},
	touchDelegateRelease: function() {}
}), cc.TargetedTouchDelegate = cc.TouchDelegate.extend({
	onTouchBegan: function(e, t) {
		return !1
	},
	onTouchMoved: function(e, t) {},
	onTouchEnded: function(e, t) {},
	onTouchCancelled: function(e, t) {}
}), cc.StandardTouchDelegate = cc.TouchDelegate.extend({
	onTouchesBegan: function(e, t) {},
	onTouchesMoved: function(e, t) {},
	onTouchesEnded: function(e, t) {},
	onTouchesCancelled: function(e, t) {}
});
cc.TouchHandler = cc.Class.extend({
	_delegate: null,
	_priority: 0,
	_enabledSelectors: 0,
	getDelegate: function() {
		return this._delegate
	},
	setDelegate: function(e) {
		this._delegate = e
	},
	getPriority: function() {
		return this._priority
	},
	setPriority: function(e) {
		this._priority = e
	},
	getEnabledSelectors: function() {
		return this._enabledSelectors
	},
	setEnalbedSelectors: function(e) {
		this._enabledSelectors = e
	},
	initWithDelegate: function(e, t) {
		return cc.Assert(e != null, "TouchHandler.initWithDelegate():touch delegate should not be null"), this._delegate = e, this._priority = t, this._enabledSelectors = 0, !0
	}
}), cc.TouchHandler.handlerWithDelegate = function(e, t) {
	var n = new cc.TouchHandler();
	return n && n.initWithDelegate(e, t), n
}, cc.StandardTouchHandler = cc.TouchHandler.extend({
	initWithDelegate: function(e, t) {
		return this._super(e, t) ? !0 : !1
	}
}), cc.StandardTouchHandler.handlerWithDelegate = function(e, t) {
	var n = new cc.StandardTouchHandler();
	return n && n.initWithDelegate(e, t), n
}, cc.TargetedTouchHandler = cc.TouchHandler.extend({
	_swallowsTouches: !1,
	_claimedTouches: null,
	isSwallowsTouches: function() {
		return this._swallowsTouches
	},
	setSwallowsTouches: function(e) {
		this._swallowsTouches = e
	},
	getClaimedTouches: function() {
		return this._claimedTouches
	},
	initWithDelegate: function(e, t, n) {
		return this._super(e, t) ? (this._claimedTouches = [], this._swallowsTouches = n, !0) : !1
	}
}), cc.TargetedTouchHandler.handlerWithDelegate = function(e, t, n) {
	var r = new cc.TargetedTouchHandler();
	return r && r.initWithDelegate(e, t, n), r
};
cc.TouchSelectorBeganBit = 1, cc.TouchSelectorMovedBit = 2, cc.TouchSelectorEndedBit = 4, cc.TouchSelectorCancelledBit = 8, cc.TouchSelectorAllBits = cc.TouchSelectorBeganBit | cc.TouchSelectorMovedBit | cc.TouchSelectorEndedBit | cc.TouchSelectorCancelledBit, cc.TOUCH_BEGAN = 0, cc.TOUCH_MOVED = 1, cc.TOUCH_ENDED = 2, cc.TOUCH_CANCELLED = 3, cc.TouchMax = 4, cc.less = function(e, t) {
	return e.getPriority() > t.getPriority()
}, cc.TouchHandlerHelperData = function(e) {
	this.type = e
}, cc.TouchDispatcher = cc.Class.extend({
	_mousePressed: !1,
	_targetedHandlers: null,
	_standardHandlers: null,
	_locked: !1,
	_toAdd: !1,
	_toRemove: !1,
	_handlersToAdd: null,
	_handlersToRemove: null,
	_toQuit: !1,
	_dispatchEvents: !1,
	_handlerHelperData: [new cc.TouchHandlerHelperData(cc.TOUCH_BEGAN), new cc.TouchHandlerHelperData(cc.TOUCH_MOVED), new cc.TouchHandlerHelperData(cc.TOUCH_ENDED), new cc.TouchHandlerHelperData(cc.TOUCH_CANCELLED)],
	init: function() {
		return this._dispatchEvents = !0, this._targetedHandlers = [], this._standardHandlers = [], this._handlersToAdd = [], this._handlersToRemove = [], this._toRemove = !1, this._toAdd = !1, this._toQuit = !1, this._locked = !1, this._mousePressed = !1, cc.TouchDispatcher.registerHtmlElementEvent(cc.canvas), !0
	},
	_setMousePressed: function(e) {
		this._mousePressed = e
	},
	_getMousePressed: function() {
		return this._mousePressed
	},
	isDispatchEvents: function() {
		return this._dispatchEvents
	},
	setDispatchEvents: function(e) {
		this._dispatchEvents = e
	},
	addStandardDelegate: function(e, t) {
		var n = cc.StandardTouchHandler.handlerWithDelegate(e, t);
		if (!this._locked) this._standardHandlers = this.forceAddHandler(n, this._standardHandlers);
		else {
			if (this._handlersToRemove.indexOf(e) != -1) {
				cc.ArrayRemoveObject(this._handlersToRemove, e);
				return
			}
			this._handlersToAdd.push(n), this._toAdd = !0
		}
	},
	addTargetedDelegate: function(e, t, n) {
		var r = cc.TargetedTouchHandler.handlerWithDelegate(e, t, n);
		if (!this._locked) this._targetedHandlers = this.forceAddHandler(r, this._targetedHandlers);
		else {
			if (this._handlersToRemove.indexOf(e) != -1) {
				cc.ArrayRemoveObject(this._handlersToRemove, e);
				return
			}
			this._handlersToAdd.push(r), this._toAdd = !0
		}
	},
	forceAddHandler: function(e, t) {
		var n = 0;
		for (var r = 0; r < t.length; r++) {
			var i = t[r];
			if (i) {
				i.getPriority() < e.getPriority() && ++n;
				if (i.getDelegate() == e.getDelegate()) return cc.Assert(0, "TouchDispatcher.forceAddHandler()"), t
			}
		}
		return cc.ArrayAppendObjectToIndex(t, e, n)
	},
	forceRemoveAllDelegates: function() {
		this._standardHandlers.length = 0, this._targetedHandlers.length = 0
	},
	removeDelegate: function(e) {
		if (e == null) return;
		if (!this._locked) this.forceRemoveDelegate(e);
		else {
			var t = this.findHandler(this._handlersToAdd, e);
			if (t) {
				cc.ArrayRemoveObject(this._handlersToAdd, t);
				return
			}
			this._handlersToRemove.push(e), this._toRemove = !0
		}
	},
	removeAllDelegates: function() {
		this._locked ? this._toQuit = !0 : this.forceRemoveAllDelegates()
	},
	setPriority: function(e, t) {
		cc.Assert(t != null, "TouchDispatcher.setPriority():Arguments is null");
		var n = this.findHandler(t);
		cc.Assert(n != null, "TouchDispatcher.setPriority():Cant find TouchHandler"), n.getPriority() != e && (n.setPriority(e), this.rearrangeHandlers(this._targetedHandlers), this.rearrangeHandlers(this._standardHandlers))
	},
	touches: function(e, t, n) {
		cc.Assert(n >= 0 && n < 4, "TouchDispatcher.touches()"), this._locked = !0;
		var r = this._targetedHandlers.length,
			i = this._standardHandlers.length,
			s = r && i,
			o = s ? e.slice() : e,
			u = this._handlerHelperData[n];
		if (r > 0) {
			var a;
			for (var f = 0; f < e.length; f++) {
				a = e[f];
				var l;
				for (var c = 0; c < this._targetedHandlers.length; c++) {
					l = this._targetedHandlers[c];
					if (!l) break;
					var h = !1;
					if (n == cc.TOUCH_BEGAN) l.getDelegate().onTouchBegan && (h = l.getDelegate().onTouchBegan(a, t), h && l.getClaimedTouches().push(a));
					else if (l.getClaimedTouches().length > 0) {
						h = !0;
						switch (u.type) {
						case cc.TOUCH_MOVED:
							cc.Browser.isMobile ? l.getDelegate().onTouchMoved && l.getDelegate().onTouchMoved(a, t) : this._mousePressed && l.getDelegate().onTouchMoved && l.getDelegate().onTouchMoved(a, t);
							break;
						case cc.TOUCH_ENDED:
							l.getDelegate().onTouchEnded && l.getDelegate().onTouchEnded(a, t), l.getClaimedTouches().length = 0;
							break;
						case cc.TOUCH_CANCELLED:
							l.getDelegate().onTouchCancelled && l.getDelegate().onTouchCancelled(a, t), l.getClaimedTouches().length = 0
						}
					}
					if (h && l.isSwallowsTouches()) {
						s && cc.ArrayRemoveObject(o, a);
						break
					}
				}
			}
		}
		if (i > 0) for (f = 0; f < this._standardHandlers.length; f++) {
			l = this._standardHandlers[f];
			if (!l) break;
			switch (u.type) {
			case cc.TOUCH_BEGAN:
				o.length > 0 && l.getDelegate().onTouchesBegan && l.getDelegate().onTouchesBegan(o, t);
				break;
			case cc.TOUCH_MOVED:
				o.length > 0 && (cc.Browser.isMobile ? l.getDelegate().onTouchesMoved && l.getDelegate().onTouchesMoved(o, t) : this._mousePressed && l.getDelegate().onTouchesMoved && l.getDelegate().onTouchesMoved(o, t));
				break;
			case cc.TOUCH_ENDED:
				l.getDelegate().onTouchesEnded && l.getDelegate().onTouchesEnded(o, t);
				break;
			case cc.TOUCH_CANCELLED:
				l.getDelegate().onTouchesCancelled && l.getDelegate().onTouchesCancelled(o, t)
			}
		}
		s && (o = null), this._locked = !1;
		if (this._toRemove) {
			this._toRemove = !1;
			for (f = 0; f < this._handlersToRemove.length; f++) this.forceRemoveDelegate(this._handlersToRemove[f]);
			this._handlersToRemove.length = 0
		}
		if (this._toAdd) {
			this._toAdd = !1;
			for (f = 0; f < this._handlersToAdd.length; f++) {
				l = this._handlersToAdd[f];
				if (!l) break;
				l instanceof cc.TargetedTouchHandler ? this._targetedHandlers = this.forceAddHandler(l, this._targetedHandlers) : this._standardHandlers = this.forceAddHandler(l, this._standardHandlers)
			}
			this._handlersToAdd.length = 0
		}
		this._toQuit && (this._toQuit = !1, this.forceRemoveAllDelegates())
	},
	touchesBegan: function(e, t) {
		this._dispatchEvents && this.touches(e, t, cc.TOUCH_BEGAN)
	},
	touchesMoved: function(e, t) {
		this._dispatchEvents && this.touches(e, t, cc.TOUCH_MOVED)
	},
	touchesEnded: function(e, t) {
		this._dispatchEvents && this.touches(e, t, cc.TOUCH_ENDED)
	},
	touchesCancelled: function(e, t) {
		this._dispatchEvents && this.touches(e, t, cc.TOUCH_CANCELLED)
	},
	findHandler: function(e, t) {
		switch (arguments.length) {
		case 1:
			t = arguments[0];
			for (var n = 0; n < this._targetedHandlers.length; n++) if (this._targetedHandlers[n].getDelegate() == t) return this._targetedHandlers[n];
			for (n = 0; n < this._standardHandlers.length; n++) if (this._standardHandlers[n].getDelegate() == t) return this._standardHandlers[n];
			return null;
		case 2:
			cc.Assert(e != null && t != null, "TouchDispatcher.findHandler():Arguments is null");
			for (n = 0; n < e.length; n++) if (e[n].getDelegate() == t) return e[n];
			return null;
		default:
			throw "Argument must be non-nil "
		}
	},
	forceRemoveDelegate: function(e) {
		var t;
		for (var n = 0; n < this._standardHandlers.length; n++) {
			t = this._standardHandlers[n];
			if (t && t.getDelegate() == e) {
				cc.ArrayRemoveObject(this._standardHandlers, t);
				break
			}
		}
		for (n = 0; n < this._targetedHandlers.length; n++) {
			t = this._targetedHandlers[n];
			if (t && t.getDelegate() == e) {
				cc.ArrayRemoveObject(this._targetedHandlers, t);
				break
			}
		}
	},
	rearrangeHandlers: function(e) {
		e.sort(cc.less)
	}
}), cc.TouchDispatcher.preTouchPoint = cc.p(0, 0), cc.TouchDispatcher.isRegisterEvent = !1, cc.getHTMLElementPosition = function(e) {
	var t = null;
	e instanceof HTMLCanvasElement ? t = {
		left: 0,
		top: 0,
		width: e.width,
		height: e.height
	} : t = {
		left: 0,
		top: 0,
		width: parseInt(e.style.width),
		height: parseInt(e.style.height)
	};
	while (e != null) t.left += e.offsetLeft, t.top += e.offsetTop, e = e.offsetParent;
	return t
}, cc.ProcessMouseupEvent = function(e, t) {
	var n = cc.getHTMLElementPosition(e),
		r = t.pageX,
		i = t.pageY,
		s = (r - n.left) / cc.Director.getInstance().getContentScaleFactor(),
		o = (n.height - (i - n.top)) / cc.Director.getInstance().getContentScaleFactor(),
		u = new cc.Touch(s, o);
	u._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = s, cc.TouchDispatcher.preTouchPoint.y = o;
	var a = [];
	a.push(u), cc.Director.getInstance().getTouchDispatcher().touchesEnded(a, null)
}, cc.TouchDispatcher.registerHtmlElementEvent = function(e) {
	if (cc.TouchDispatcher.isRegisterEvent) return;
	cc.Browser.isMobile ? (e.addEventListener("touchstart", function(t) {
		if (!t.touches) return;
		var n = [],
			r = cc.getHTMLElementPosition(e);
		r.left -= document.body.scrollLeft, r.top -= document.body.scrollTop;
		for (var i = 0; i < t.touches.length; i++) {
			var s = t.touches[i].pageX,
				o = t.touches[i].pageY;
			t.touches[i] && (s = t.touches[i].clientX, o = t.touches[i].clientY);
			var u = (s - r.left) / cc.Director.getInstance().getContentScaleFactor(),
				a = (r.height - (o - r.top)) / cc.Director.getInstance().getContentScaleFactor(),
				f = null;
			t.touches[i].hasOwnProperty("identifier") ? f = new cc.Touch(u, a, t.touches[i].identifier) : f = new cc.Touch(u, a), f._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = u, cc.TouchDispatcher.preTouchPoint.y = a, n.push(f)
		}
		cc.Director.getInstance().getTouchDispatcher().touchesBegan(n, null), t.stopPropagation(), t.preventDefault()
	}, !1), e.addEventListener("touchmove", function(t) {
		if (!t.touches) return;
		var n = [],
			r = cc.getHTMLElementPosition(e);
		r.left -= document.body.scrollLeft, r.top -= document.body.scrollTop;
		for (var i = 0; i < t.touches.length; i++) {
			var s = t.touches[i].pageX,
				o = t.touches[i].pageY;
			t.touches[i] && (s = t.touches[i].clientX, o = t.touches[i].clientY);
			var u = (s - r.left) / cc.Director.getInstance().getContentScaleFactor(),
				a = (r.height - (o - r.top)) / cc.Director.getInstance().getContentScaleFactor(),
				f = null;
			t.touches[i].hasOwnProperty("identifier") ? f = new cc.Touch(u, a, t.touches[i].identifier) : f = new cc.Touch(u, a), f._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = u, cc.TouchDispatcher.preTouchPoint.y = a, n.push(f)
		}
		cc.Director.getInstance().getTouchDispatcher().touchesMoved(n, null), t.stopPropagation(), t.preventDefault()
	}, !1), e.addEventListener("touchend", function(t) {
		if (!t.touches) return;
		var n = [],
			r = cc.getHTMLElementPosition(e);
		r.left -= document.body.scrollLeft, r.top -= document.body.scrollTop;
		var i = t.touches;
		if (!i || i.length == 0) i = t.changedTouches;
		for (var s = 0; s < i.length; s++) {
			var o = i[s].pageX,
				u = i[s].pageY;
			i[s] && (o = i[s].clientX, u = i[s].clientY);
			var a = (o - r.left) / cc.Director.getInstance().getContentScaleFactor(),
				f = (r.height - (u - r.top)) / cc.Director.getInstance().getContentScaleFactor(),
				l = null;
			i[s].hasOwnProperty("identifier") ? l = new cc.Touch(a, f, i[s].identifier) : l = new cc.Touch(a, f), l._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = a, cc.TouchDispatcher.preTouchPoint.y = f, n.push(l)
		}
		cc.Director.getInstance().getTouchDispatcher().touchesEnded(n, null), t.stopPropagation(), t.preventDefault()
	}, !1), e.addEventListener("touchcancel", function(t) {
		if (!t.touches) return;
		var n = [],
			r = cc.getHTMLElementPosition(e);
		r.left -= document.body.scrollLeft, r.top -= document.body.scrollTop;
		for (var i = 0; i < t.touches.length; i++) {
			var s = t.touches[i].pageX,
				o = t.touches[i].pageY;
			t.touches[i] && (s = t.touches[i].clientX, o = t.touches[i].clientY);
			var u = (s - r.left) / cc.Director.getInstance().getContentScaleFactor(),
				a = (r.height - (o - r.top)) / cc.Director.getInstance().getContentScaleFactor(),
				f = null;
			t.touches[i].hasOwnProperty("identifier") ? f = new cc.Touch(u, a, t.touches[i].identifier) : f = new cc.Touch(u, a), f._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = u, cc.TouchDispatcher.preTouchPoint.y = a, n.push(f)
		}
		cc.Director.getInstance().getTouchDispatcher().touchesCancelled(n, null), t.stopPropagation(), t.preventDefault()
	}, !1)) : (window.addEventListener("mousedown", function(e) {
		cc.Director.getInstance().getTouchDispatcher()._setMousePressed(!0)
	}), window.addEventListener("mouseup", function(t) {
		cc.Director.getInstance().getTouchDispatcher()._setMousePressed(!1);
		var n = cc.getHTMLElementPosition(e),
			r = t.pageX,
			i = t.pageY;
		if (!cc.rectContainsPoint(new cc.Rect(n.left, n.top, n.width, n.height), cc.p(r, i))) {
			var s = (r - n.left) / cc.Director.getInstance().getContentScaleFactor(),
				o = (n.height - (i - n.top)) / cc.Director.getInstance().getContentScaleFactor(),
				u = new cc.Touch(s, o);
			u._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = s, cc.TouchDispatcher.preTouchPoint.y = o;
			var a = [];
			a.push(u), cc.Director.getInstance().getTouchDispatcher().touchesEnded(a, null)
		}
	}), e.addEventListener("mousedown", function(t) {
		var n = cc.getHTMLElementPosition(e),
			r = t.pageX,
			i = t.pageY,
			s = (r - n.left) / cc.Director.getInstance().getContentScaleFactor(),
			o = (n.height - (i - n.top)) / cc.Director.getInstance().getContentScaleFactor(),
			u = new cc.Touch(s, o);
		u._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = s, cc.TouchDispatcher.preTouchPoint.y = o;
		var a = [];
		a.push(u), cc.Director.getInstance().getTouchDispatcher().touchesBegan(a, null)
	}), e.addEventListener("mouseup", function(t) {
		cc.ProcessMouseupEvent(e, t)
	}), e.addEventListener("mousemove", function(t) {
		var n = cc.getHTMLElementPosition(e),
			r = t.pageX,
			i = t.pageY,
			s = (r - n.left) / cc.Director.getInstance().getContentScaleFactor(),
			o = (n.height - (i - n.top)) / cc.Director.getInstance().getContentScaleFactor(),
			u = new cc.Touch(s, o);
		u._setPrevPoint(cc.TouchDispatcher.preTouchPoint.x, cc.TouchDispatcher.preTouchPoint.y), cc.TouchDispatcher.preTouchPoint.x = s, cc.TouchDispatcher.preTouchPoint.y = o;
		var a = [];
		a.push(u), cc.Director.getInstance().getTouchDispatcher().touchesMoved(a, null)
	})), cc.TouchDispatcher.isRegisterEvent = !0
};
cc.MOUSE_DOWN = 1, cc.MOUSE_MOVED = 2, cc.MOUSE_DRAGGED = 4, cc.MOUSE_UP = 8, cc.RIGHT_MOUSE_DOWN = 16, cc.RIGHT_MOUSE_DRAGGED = 32, cc.RIGHT_MOUSE_UP = 64, cc.OTHER_MOUSE_DOWN = 128, cc.OTHER_MOUSE_DRAGGED = 256, cc.OTHER_MOUSE_UP = 512, cc.SCROLL_WHEEL = 1024, cc.MOUSE_ENTERED = 2048, cc.MOUSE_EXITED = 4096, cc.MOUSE_LEFTBUTTON = 0, cc.MOUSE_MIDDLEBUTTON = 1, cc.MOUSE_RIGHTBUTTON = 2, cc.MouseEventDelegate = cc.Class.extend({
	onMouseDown: function(e) {
		return !1
	},
	onMouseDragged: function(e) {
		return !1
	},
	onMouseMoved: function(e) {
		return !1
	},
	onMouseUp: function(e) {
		return !1
	},
	onRightMouseDown: function(e) {
		return !1
	},
	onRightMouseDragged: function(e) {
		return !1
	},
	onRightMouseUp: function(e) {
		return !1
	},
	onOtherMouseDown: function(e) {
		return !1
	},
	onOtherMouseDragged: function(e) {
		return !1
	},
	onOtherMouseUp: function(e) {
		return !1
	},
	onScrollWheel: function(e) {
		return !1
	},
	onMouseEntered: function(e) {
		return !1
	},
	onMouseExited: function(e) {
		return !1
	}
}), cc.Mouse = cc.Touch.extend({
	_wheelDelta: 0,
	_button: cc.MOUSE_LEFTBUTTON,
	getWheelDelta: function() {
		return this._wheelDelta
	},
	setWheelDelta: function(e) {
		this._wheelDelta = e
	},
	getButton: function() {
		return this._button
	},
	setButton: function(e) {
		this._button = e
	}
}), cc.MouseHandler = cc.Class.extend({
	_delegate: null,
	_priority: 0,
	_enabledSelectors: 0,
	getDelegate: function() {
		return this._delegate
	},
	setDelegate: function(e) {
		this._delegate = e
	},
	getPriority: function() {
		return this._priority
	},
	setPriority: function(e) {
		this._priority = e
	},
	getEnabledSelectors: function() {
		return this._enabledSelectors
	},
	setEnalbedSelectors: function(e) {
		this._enabledSelectors = e
	},
	initWithDelegate: function(e, t) {
		this._delegate = e, this._priority = t
	}
}), cc.MouseHandler.create = function(e, t) {
	var n = new cc.MouseHandler();
	return n.initWithDelegate(e, t), n
}, cc.MouseDispatcher = cc.Class.extend({
	_mousePressed: !1,
	_rightMousePressed: !1,
	_mouseDelegateHandlers: null,
	_dispatchEvents: !1,
	init: function() {
		return this._dispatchEvents = !0, this._mouseDelegateHandlers = [], this._mousePressed = !1, this._rightMousePressed = !1, cc.MouseDispatcher._registerHtmlElementEvent(cc.canvas), !0
	},
	_setMousePressed: function(e) {
		this._mousePressed = e
	},
	_getMousePressed: function() {
		return this._mousePressed
	},
	_setRightMousePressed: function(e) {
		this._rightMousePressed = e
	},
	_getRightMousePressed: function() {
		return this._rightMousePressed
	},
	addMouseDelegate: function(e, t) {
		var n = cc.MouseHandler.create(e, t);
		this._mouseDelegateHandlers = this.forceAddHandler(n, this._mouseDelegateHandlers)
	},
	forceAddHandler: function(e, t) {
		var n = 0;
		for (var r = 0; r < t.length; r++) {
			var i = t[r];
			if (i) {
				i.getPriority() < e.getPriority() && ++n;
				if (i.getDelegate() == e.getDelegate()) return cc.Assert(0, "TouchDispatcher.forceAddHandler()"), t
			}
		}
		return cc.ArrayAppendObjectToIndex(t, e, n)
	},
	removeMouseDelegate: function(e) {
		if (e == null) return;
		for (var t = 0; t < this._mouseDelegateHandlers.length; t++) {
			var n = this._mouseDelegateHandlers[t];
			if (n && n.getDelegate() == e) {
				cc.ArrayRemoveObject(this._mouseDelegateHandlers, n);
				break
			}
		}
	},
	_findHandler: function(e) {
		for (i = 0; i < this._mouseDelegateHandlers.length; i++) if (this._mouseDelegateHandlers[i] && this._mouseDelegateHandlers[i].getDelegate() == e) return this._mouseDelegateHandlers[i]
	},
	setPriority: function(e, t) {
		cc.Assert(t != null, "MouseDispatcher.setPriority():Arguments is null");
		var n = this._findHandler(t);
		cc.Assert(n != null, "MouseDispatcher.setPriority():Cant find MouseHandler"), n.getPriority() != e && (n.setPriority(e), this._mouseDelegateHandlers.sort(cc.less))
	},
	removeAllMouseDelegates: function() {
		this._mouseDelegateHandlers.length = 0
	},
	mouseHandle: function(e, t, n) {
		for (var r = 0; r < this._mouseDelegateHandlers.length; r++) {
			var i = this._mouseDelegateHandlers[r];
			switch (n) {
			case cc.MOUSE_DOWN:
				e.getButton() == cc.MOUSE_RIGHTBUTTON ? i.getDelegate().onRightMouseDown && i.getDelegate().onRightMouseDown(e) : i.getDelegate().onMouseDown && i.getDelegate().onMouseDown(e);
				break;
			case cc.MOUSE_UP:
				e.getButton() == cc.MOUSE_RIGHTBUTTON ? i.getDelegate().onRightMouseUp && i.getDelegate().onRightMouseUp(e) : i.getDelegate().onMouseUp && i.getDelegate().onMouseUp(e);
				break;
			case cc.MOUSE_MOVED:
				this._mousePressed ? i.getDelegate().onMouseDragged && i.getDelegate().onMouseDragged(e) : this._rightMousePressed ? i.getDelegate().onRightMouseDragged && i.getDelegate().onRightMouseDragged(e) : i.getDelegate().onMouseMoved && i.getDelegate().onMouseMoved(e);
				break;
			case cc.MOUSE_ENTERED:
				i.getDelegate().onMouseEntered && i.getDelegate().onMouseEntered(e);
				break;
			case cc.MOUSE_EXITED:
				i.getDelegate().onMouseExited && i.getDelegate().onMouseExited(e);
				break;
			case cc.SCROLL_WHEEL:
				i.getDelegate().onScrollWheel && i.getDelegate().onScrollWheel(e)
			}
		}
	}
}), cc.MouseDispatcher._preMousePoint = cc.p(0, 0), cc.MouseDispatcher._isRegisterEvent = !1, cc.MouseDispatcher._registerHtmlElementEvent = function(e) {
	function t(t) {
		var n = cc.getHTMLElementPosition(e),
			r = t.pageX,
			i = t.pageY,
			s = (r - n.left) / cc.Director.getInstance().getContentScaleFactor(),
			o = (n.height - (i - n.top)) / cc.Director.getInstance().getContentScaleFactor(),
			u = new cc.Mouse(s, o);
		return u._setPrevPoint(cc.MouseDispatcher._preMousePoint.x, cc.MouseDispatcher._preMousePoint.y), u.setButton(t.button), cc.MouseDispatcher._preMousePoint.x = s, cc.MouseDispatcher._preMousePoint.y = o, u
	}
	if (cc.MouseDispatcher._isRegisterEvent) return;
	window.addEventListener("mousedown", function(e) {
		e.button == cc.MOUSE_RIGHTBUTTON ? cc.Director.getInstance().getMouseDispatcher()._setRightMousePressed(!0) : cc.Director.getInstance().getMouseDispatcher()._setMousePressed(!0)
	}), window.addEventListener("mouseup", function(e) {
		e.button == cc.MOUSE_RIGHTBUTTON ? cc.Director.getInstance().getMouseDispatcher()._setRightMousePressed(!1) : cc.Director.getInstance().getMouseDispatcher()._setMousePressed(!1)
	}), e.addEventListener("mousedown", function(e) {
		cc.Director.getInstance().getMouseDispatcher().mouseHandle(t(e), e, cc.MOUSE_DOWN)
	}), e.addEventListener("mouseup", function(e) {
		cc.Director.getInstance().getMouseDispatcher().mouseHandle(t(e), e, cc.MOUSE_UP)
	}), e.addEventListener("mousemove", function(e) {
		cc.Director.getInstance().getMouseDispatcher().mouseHandle(t(e), e, cc.MOUSE_MOVED)
	}), e.addEventListener("mousewheel", function(e) {
		var n = t(e);
		n.setWheelDelta(e.wheelDelta), cc.Director.getInstance().getMouseDispatcher().mouseHandle(n, e, cc.SCROLL_WHEEL)
	}, !1), e.addEventListener("mouseout", function(e) {
		cc.Director.getInstance().getMouseDispatcher().mouseHandle(t(e), e, cc.MOUSE_EXITED)
	}, !1), e.addEventListener("mouseover", function(e) {
		cc.Director.getInstance().getMouseDispatcher().mouseHandle(t(e), e, cc.MOUSE_ENTERED)
	}, !1)
};