cc.KeyboardDelegate = cc.Class.extend({
    onKeyDown: function() {},
    onKeyUp: function() {}
}),
cc.KeyboardHandler = cc.Class.extend({
    getDelegate: function() {
        return this._delegate
    },
    setDelegate: function(e) {
        this._delegate = e
    },
    initWithDelegate: function(e) {
        return cc.Assert(e != null, "It's a wrong delegate!"),
        this._delegate = e,
        !0
    },
    _delegate: null
}),
cc.KeyboardHandler.create = function(e) {
    var t = new cc.KeyboardHandler;
    return t.initWithDelegate(e),
    t
}
cc.TYPE_BACK_CLICKED = 1,
cc.TYPE_MENU_CLICKED = 2,
cc.KEY = {
    backspace: 8,
    tab: 9,
    enter: 13,
    shift: 16,
    ctrl: 17,
    alt: 18,
    pause: 19,
    capslock: 20,
    escape: 27,
    pageup: 33,
    pagedown: 34,
    end: 35,
    home: 36,
    left: 37,
    up: 38,
    right: 39,
    down: 40,
    insert: 45,
    Delete: 46,
    0 : 48,
    1 : 49,
    2 : 50,
    3 : 51,
    4 : 52,
    5 : 53,
    6 : 54,
    7 : 55,
    8 : 56,
    9 : 57,
    a: 65,
    b: 66,
    c: 67,
    d: 68,
    e: 69,
    f: 70,
    g: 71,
    h: 72,
    i: 73,
    j: 74,
    k: 75,
    l: 76,
    m: 77,
    n: 78,
    o: 79,
    p: 80,
    q: 81,
    r: 82,
    s: 83,
    t: 84,
    u: 85,
    v: 86,
    w: 87,
    x: 88,
    y: 89,
    z: 90,
    num0: 96,
    num1: 97,
    num2: 98,
    num3: 99,
    num4: 100,
    num5: 101,
    num6: 102,
    num7: 103,
    num8: 104,
    num9: 105,
    "*": 106,
    "+": 107,
    "-": 109,
    numdel: 110,
    "/": 111,
    f1: 112,
    f2: 113,
    f3: 114,
    f4: 115,
    f5: 116,
    f6: 117,
    f7: 118,
    f8: 119,
    f9: 120,
    f10: 121,
    f11: 122,
    f12: 123,
    numlock: 144,
    scrolllock: 145,
    semicolon: 186,
    ",": 186,
    equal: 187,
    "=": 187,
    ";": 188,
    comma: 188,
    dash: 189,
    ".": 190,
    period: 190,
    forwardslash: 191,
    grave: 192,
    "[": 219,
    openbracket: 219,
    "]": 221,
    closebracket: 221,
    backslash: 220,
    quote: 222,
    space: 32
},
cc.KeyboardDispatcher = cc.Class.extend({
    addDelegate: function(e) {
        if (!e) return;
        this._locked ? (this._handlersToAdd.push(e), this._toAdd = !0) : this.forceAddDelegate(e)
    },
    removeDelegate: function(e) {
        if (!e) return;
        this._locked ? (this._handlersToRemove.push(e), this._toRemove = !0) : this.forceRemoveDelegate(e)
    },
    forceAddDelegate: function(e) {
        var t = cc.KeyboardHandler.create(e);
        if (t) {
            for (var n = 0; n < this._delegates; n++) this._delegates[n].getDelegate() == t.getDelegate();
            this._delegates.push(t)
        }
    },
    forceRemoveDelegate: function(e) {
        for (var t = 0; t < this._delegates.length; t++) if (this._delegates[t].getDelegate() == e) {
            this._delegates.splice(t, 1);
            return
        }
    },
    dispatchKeyboardMSG: function(e, t) {
        this._locked = !0,
        e.stopPropagation(),
        e.preventDefault();
        var n = 0;
        if (t && e) for (n = 0; n < this._delegates.length; n++) this._delegates[n].getDelegate() && this._delegates[n].getDelegate().onKeyDown && this._delegates[n].getDelegate().onKeyDown(e.keyCode);
        else if (!t && e) for (n = 0; n < this._delegates.length; n++) this._delegates[n].getDelegate() && this._delegates[n].getDelegate().onKeyUp && this._delegates[n].getDelegate().onKeyUp(e.keyCode);
        this._locked = !1;
        if (this._toRemove) {
            this._toRemove = !1;
            for (n = 0; n < this._handlersToRemove.length; ++n) this.forceRemoveDelegate(this._handlersToRemove[n]);
            delete this._handlersToRemove,
            this._handlersToRemove = []
        }
        if (this._toAdd) {
            this._toAdd = !1;
            for (n = 0; n < this._handlersToAdd.length; ++n) this.forceAddDelegate(this._handlersToAdd[n]);
            this._handlersToAdd = []
        }
        return ! 0
    },
    _delegates: [],
    _locked: !1,
    _toAdd: !1,
    _toRemove: !1,
    _handlersToAdd: [],
    _handlersToRemove: []
}),
cc.KeyboardDispatcher.getInstance = function() {
    return cc.keyboardDispatcher || (cc.keyboardDispatcher = new cc.KeyboardDispatcher, cc.canvas.setAttribute("tabindex", 1), cc.canvas.style.outline = "none", cc.canvas.style.cursor = "default", cc.canvas.addEventListener("keydown",
    function(e) {
        cc.keyboardDispatcher.dispatchKeyboardMSG(e, !0)
    }), cc.canvas.addEventListener("keyup",
    function(e) {
        cc.keyboardDispatcher.dispatchKeyboardMSG(e, !1)
    })),
    cc.keyboardDispatcher
},
cc.KeyboardDispatcher.purgeSharedDispatcher = function() {
    cc.keyboardDispatcher && (delete cc.keyboardDispatcher, cc.keyboardDispatcher = null)
}
cc.g_NumberOfDraws = 0,
cc.DIRECTOR_PROJECTION_2D = 0,
cc.DIRECTOR_PROJECTION_3D = 1,
cc.DIRECTOR_PROJECTION_CUSTOM = 3,
cc.DIRECTOR_PROJECTION_DEFAULT = cc.DIRECTOR_PROJECTION_3D,
cc.DEVICE_ORIENTATION_PORTRAIT = 0,
cc.DEVICE_ORIENTATION_LANDSCAPE_LEFT = 1,
cc.DEVICE_ORIENTATION_PORTRAIT_UPSIDE_DOWN = 2,
cc.DEVICE_ORIENTATION_LANDSCAPE_RIGHT = 3,
cc.DEVICE_MAX_ORIENTATIONS = 2,
cc.Director = cc.Class.extend({
    _isContentScaleSupported: !1,
    _landscape: !1,
    _nextDeltaTimeZero: !1,
    _paused: !1,
    _purgeDirecotorInNextLoop: !1,
    _sendCleanupToScene: !1,
    _animationInterval: 0,
    _oldAnimationInterval: 0,
    _projection: 0,
    _accumDt: 0,
    _contentScaleFactor: 1,
    _displayStats: !1,
    _deltaTime: 0,
    _frameRate: 0,
    _FPSLabel: null,
    _SPFLabel: null,
    _drawsLabel: null,
    _winSizeInPixels: null,
    _winSizeInPoints: null,
    _lastUpdate: null,
    _nextScene: null,
    _notificationNode: null,
    _openGLView: null,
    _scenesStack: null,
    _projectionDelegate: null,
    _runningScene: null,
    _frames: 0,
    _totalFrames: 0,
    _secondsPerFrame: 0,
    _dirtyRegion: null,
    _scheduler: null,
    _actionManager: null,
    _touchDispatcher: null,
    _keyboardDispatcher: null,
    _accelerometer: null,
    _mouseDispatcher: null,
    _watcherFun: null,
    _watcherSender: null,
    _isBlur: !1,
    ctor: function() {
        this._lastUpdate = Date.now();
        if (!cc.isAddedHiddenEvent) {
            var e = this;
            window.addEventListener("focus",
            function() {
                e._lastUpdate = Date.now()
            },
            !1)
        }
    },
    _resetLastUpdate: function() {
        this._lastUpdate = Date.now()
    },
    init: function() {
        return this._runningScene = null,
        this._nextScene = null,
        this._notificationNode = null,
        this._oldAnimationInterval = this._animationInterval = 1 / cc.defaultFPS,
        this._scenesStack = [],
        this._projection = cc.DIRECTOR_PROJECTION_DEFAULT,
        this._projectionDelegate = null,
        this._accumDt = 0,
        this._frameRate = 0,
        this._displayStats = !1,
        this._totalFrames = this._frames = 0,
        this._lastUpdate = Date.now(),
        this._paused = !1,
        this._purgeDirecotorInNextLoop = !1,
        this._winSizeInPixels = this._winSizeInPoints = cc.size(cc.canvas.width, cc.canvas.height),
        this._openGLView = null,
        this._contentScaleFactor = 1,
        this._isContentScaleSupported = !1,
        this._watcherFun = null,
        this._watcherSender = null,
        this._scheduler = new cc.Scheduler,
        this._actionManager = new cc.ActionManager,
        this._scheduler.scheduleUpdateForTarget(this._actionManager, cc.PRIORITY_SYSTEM, !1),
        this._touchDispatcher = new cc.TouchDispatcher,
        this._touchDispatcher.init(),
        this._keyboardDispatcher = cc.KeyboardDispatcher.getInstance(),
        this._mouseDispatcher = new cc.MouseDispatcher,
        this._mouseDispatcher.init(),
        !0
    },
    calculateDeltaTime: function() {
        var e = Date.now();
        this._nextDeltaTimeZero ? (this._deltaTime = 0, this._nextDeltaTimeZero = !1) : this._deltaTime = (e - this._lastUpdate) / 1e3,
        cc.DEBUG && this._deltaTime > .2 && (this._deltaTime = 1 / 60),
        this._lastUpdate = e
    },
    convertToGL: function(e) {
        var t = this._winSizeInPoints.height - e.y;
        return cc.p(e.x, t)
    },
    convertToUI: function(e) {
        var t = this._winSizeInPoints.height - e.y;
        return cc.p(e.x, t)
    },
    drawScene: function() {
        this.calculateDeltaTime(),
        this._paused || this._scheduler.update(this._deltaTime),
        cc.renderContext.clearRect(0, 0, cc.canvas.width, -cc.canvas.height),
        this._nextScene && this.setNextScene(),
        this._runningScene && this._runningScene.visit(),
        this._notificationNode && this._notificationNode.visit(),
        this._displayStats && this._showStats(),
        this._watcherFun && this._watcherSender && this._watcherFun.call(this._watcherSender),
        this._totalFrames++,
        this._openGLView && this._openGLView.swapBuffers(),
        this._displayStats && this._calculateMPF()
    },
    addRegionToDirtyRegion: function(e) {
        if (!e) return;
        if (!this._dirtyRegion) {
            this._dirtyRegion = cc.rect(e.origin.x, e.origin.y, e.size.width, e.size.height);
            return
        }
        this._dirtyRegion = cc.Rect.CCRectUnion(this._dirtyRegion, cc.rect(e.origin.x, e.origin.y, e.size.width, e.size.height))
    },
    rectIsInDirtyRegion: function(e) {
        return ! e || !this._fullRect ? !1 : cc.Rect.CCRectIntersectsRect(this._fullRect, e)
    },
    enableRetinaDisplay: function(e) {
        if (e && this._contentScaleFactor == 2) return ! 0;
        if (!e && this._contentScaleFactor == 1) return ! 1;
        if (!this._openGLView.canSetContentScaleFactor()) return ! 1;
        if (this._openGLView.getMainScreenScale() == 1) return ! 1;
        var t = e ? 2 : 1;
        return this.setContentScaleFactor(t),
        this._createStatsLabel(),
        !0
    },
    end: function() {
        this._purgeDirecotorInNextLoop = !0
    },
    getContentScaleFactor: function() {
        return this._contentScaleFactor
    },
    getNotificationNode: function() {
        return this._notificationNode
    },
    getWinSize: function() {
        return this._winSizeInPoints
    },
    getWinSizeInPixels: function() {
        return this._winSizeInPixels
    },
    getZEye: function() {
        return this._winSizeInPixels.height / 1.1566 / cc.CONTENT_SCALE_FACTOR()
    },
    pause: function() {
        if (this._paused) return;
        this._oldAnimationInterval = this._animationInterval,
        this.setAnimationInterval(.25),
        this._paused = !0
    },
    popScene: function() {
        cc.Assert(this._runningScene != null, "running scene should not null"),
        this._scenesStack.pop();
        var e = this._scenesStack.length;
        e == 0 ? this.end() : (this._sendCleanupToScene = !0, this._nextScene = this._scenesStack[e - 1])
    },
    purgeCachedData: function() {
        cc.LabelBMFont.purgeCachedData()
    },
    purgeDirector: function() {
        this._touchDispatcher.removeAllDelegates(),
        this._runningScene && (this._runningScene.onExit(), this._runningScene.cleanup()),
        this._runningScene = null,
        this._nextScene = null,
        this._scenesStack = [],
        this.stopAnimation(),
        cc.LabelBMFont.purgeCachedData(),
        cc.AnimationCache.purgeSharedAnimationCache(),
        cc.SpriteFrameCache.purgeSharedSpriteFrameCache(),
        cc.TextureCache.purgeSharedTextureCache(),
        this._openGLView.end(),
        this._openGLView = null
    },
    pushScene: function(e) {
        cc.Assert(e, "the scene should not null"),
        this._sendCleanupToScene = !1,
        this._scenesStack.push(e),
        this._nextScene = e
    },
    replaceScene: function(e) {
        cc.Assert(e != null, "the scene should not be null");
        var t = this._scenesStack.length;
        this._sendCleanupToScene = !0,
        this._scenesStack[t - 1] = e,
        this._nextScene = e
    },
    reshapeProjection: function(e) {
        this._openGLView && (this._winSizeInPoints = this._openGLView.getSize(), this._winSizeInPixels = cc.size(this._winSizeInPoints.width * this._contentScaleFactor, this._winSizeInPoints.height * this._contentScaleFactor), this.setProjection(this._projection))
    },
    resume: function() {
        if (!this._paused) return;
        this.setAnimationInterval(this._oldAnimationInterval),
        this._lastUpdate = Date.now(),
        this._lastUpdate || cc.log("cocos2d: Director: Error in gettimeofday"),
        this._paused = !1,
        this._deltaTime = 0
    },
    runWithScene: function(e) {
        cc.Assert(e != null, "running scene should not be null"),
        cc.Assert(this._runningScene == null, "_runningScene should be null"),
        this.pushScene(e),
        this.startAnimation()
    },
    setAlphaBlending: function(e) {
        e
    },
    setContentScaleFactor: function(e) {
        e != this._contentScaleFactor && (this._contentScaleFactor = e, this._winSizeInPixels = cc.size(this._winSizeInPoints.width * e, this._winSizeInPoints.height * e), this._openGLView && this.updateContentScaleFactor(), this.setProjection(this._projection))
    },
    setDepthTest: function(e) {
        e
    },
    setGLDefaultValues: function() {
        cc.Assert(this._openGLView, "opengl view should not be null"),
        this.setAlphaBlending(!0),
        this.setDepthTest(!0),
        this.setProjection(this._projection)
    },
    setNextDeltaTimeZero: function(e) {
        this._nextDeltaTimeZero = e
    },
    setNextScene: function() {
        var e = this._runningScene ? this._runningScene instanceof cc.TransitionScene: !1,
        t = this._nextScene ? this._nextScene instanceof cc.TransitionScene: !1;
        t || (this._runningScene && this._runningScene.onExit(), this._sendCleanupToScene && this._runningScene && this._runningScene.cleanup()),
        this._runningScene = this._nextScene,
        this._nextScene = null,
        !e && this._runningScene != null && (this._runningScene.onEnter(), this._runningScene.onEnterTransitionDidFinish())
    },
    setNotificationNode: function(e) {
        this._notificationNode = e
    },
    setOpenGLView: function(e) {
        cc.Assert(e, "opengl view should not be null"),
        this._openGLView != e && (delete this._openGLView, this._openGLView = e, this._winSizeInPoints = this._openGLView.getSize(), this._winSizeInPixels = cc.size(this._winSizeInPoints.width * this._contentScaleFactor, this._winSizeInPoints.height * this._contentScaleFactor), this._createStatsLabel(), this._openGLView && this.setGLDefaultValues(), this._contentScaleFactor != 1 && this.updateContentScaleFactor(), this._openGLView.setTouchDelegate(this._touchDispatcher), this._touchDispatcher.setDispatchEvents(!0))
    },
    setProjection: function(e) {
        var t = this._winSizeInPixels,
        n = this._winSizeInPoints;
        this._openGLView && this._openGLView.setViewPortInPoints(0, 0, n.width, n.height);
        switch (e) {
        case cc.DIRECTOR_PROJECTION_2D:
            break;
        case cc.DIRECTOR_PROJECTION_3D:
            break;
        case cc.DIRECTOR_PROJECTION_CUSTOM:
            this._projectionDelegate && this._projectionDelegate.updateProjection();
            break;
        default:
            cc.log("cocos2d: Director: unrecognized projection")
        }
        this._projection = e
    },
    _showStats: function() {
        this._frames++,
        this._accumDt += this._deltaTime,
        this._displayStats && (this._FPSLabel && this._SPFLabel && this._drawsLabel ? (this._accumDt > cc.DIRECTOR_FPS_INTERVAL && (this._SPFLabel.setString(this._secondsPerFrame.toFixed(3)), this._frameRate = this._frames / this._accumDt, this._frames = 0, this._accumDt = 0, this._FPSLabel.setString(this._frameRate.toFixed(1)), this._drawsLabel.setString((0 | cc.g_NumberOfDraws).toString())), this._FPSLabel.visit(), this._SPFLabel.visit(), this._drawsLabel.visit()) : this._createStatsLabel()),
        cc.g_NumberOfDraws = 0
    },
    updateContentScaleFactor: function() {
        this._openGLView.canSetContentScaleFactor() ? (this._openGLView.setContentScaleFactor(this._contentScaleFactor), this._isContentScaleSupported = !0) : cc.log("cocos2d: setContentScaleFactor:'is not supported on this device")
    },
    isSendCleanupToScene: function() {
        return this._sendCleanupToScene
    },
    getRunningScene: function() {
        return this._runningScene
    },
    getAnimationInterval: function() {
        return this._animationInterval
    },
    isDisplayStats: function() {
        return this._displayStats
    },
    setDisplayStats: function(e) {
        this._displayStats = e
    },
    getSecondsPerFrame: function() {
        return this._secondsPerFrame
    },
    getOpenGLView: function() {
        return this._openGLView
    },
    isNextDeltaTimeZero: function() {
        return this._nextDeltaTimeZero
    },
    isPaused: function() {
        return this._paused
    },
    getTotalFrames: function() {
        return this._totalFrames
    },
    getProjection: function() {
        return this._projection
    },
    popToRootScene: function() {
        cc.Assert(this._runningScene != null, "A running Scene is needed");
        var e = this._scenesStack.length;
        if (e == 1) this._scenesStack.pop(),
        this.end();
        else {
            while (e > 1) {
                var t = this._scenesStack.pop();
                t.isRunning() && t.onExit(),
                t.cleanup(),
                e--
            }
            this._nextScene = this._scenesStack[this._scenesStack.length - 1],
            this._sendCleanupToScene = !1
        }
    },
    setWatcherCallbackFun: function(e, t) {
        this._watcherFun = t,
        this._watcherSender = e
    },
    getScheduler: function() {
        return this._scheduler
    },
    setScheduler: function(e) {
        this._scheduler != e && (this._scheduler = e)
    },
    getActionManager: function() {
        return this._actionManager
    },
    setActionManager: function(e) {
        this._actionManager != e && (this._actionManager = e)
    },
    getTouchDispatcher: function() {
        return this._touchDispatcher
    },
    setTouchDispatcher: function(e) {
        this._touchDispatcher != e && (this._touchDispatcher = e)
    },
    getKeyboardDispatcher: function() {
        return this._keyboardDispatcher
    },
    setKeyboardDispatcher: function(e) {
        this._keyboardDispatcher = e
    },
    getAccelerometer: function() {
        return this._accelerometer
    },
    setAccelerometer: function(e) {
        this._accelerometer != e && (this._accelerometer = e)
    },
    getMouseDispatcher: function() {
        return this._mouseDispatcher
    },
    setMouseDispatcher: function(e) {
        this._mouseDispatcher != e && (this._mouseDispatcher = e)
    },
    _createStatsLabel: function() {
        this._FPSLabel = cc.LabelTTF.create("00.0", "Arial", 18, cc.size(60, 16), cc.TEXT_ALIGNMENT_RIGHT),
        this._SPFLabel = cc.LabelTTF.create("0.000", "Arial", 18, cc.size(60, 16), cc.TEXT_ALIGNMENT_RIGHT),
        this._drawsLabel = cc.LabelTTF.create("000", "Arial", 18, cc.size(60, 16), cc.TEXT_ALIGNMENT_RIGHT),
        this._drawsLabel.setPosition(cc.pAdd(cc.p(20, 48), cc.DIRECTOR_STATS_POSITION)),
        this._SPFLabel.setPosition(cc.pAdd(cc.p(20, 30), cc.DIRECTOR_STATS_POSITION)),
        this._FPSLabel.setPosition(cc.pAdd(cc.p(20, 10), cc.DIRECTOR_STATS_POSITION))
    },
    _calculateMPF: function() {
        var e = Date.now();
        this._secondsPerFrame = (e - this._lastUpdate) / 1e3
    }
}),
cc.DisplayLinkDirector = cc.Director.extend({
    invalid: !1,
    startAnimation: function() {
        this._nextDeltaTimeZero = !0,
        this.invalid = !1,
        cc.Application.sharedApplication().setAnimationInterval(this._animationInterval)
    },
    mainLoop: function() {
        this._purgeDirecotorInNextLoop ? (this._purgeDirecotorInNextLoop = !1, this.purgeDirector()) : this.invalid || this.drawScene()
    },
    stopAnimation: function() {
        this.invalid = !0
    },
    setAnimationInterval: function(e) {
        this._animationInterval = e,
        this.invalid || (this.stopAnimation(), this.startAnimation())
    }
}),
cc.s_SharedDirector = null,
cc.firstUseDirector = !0,
cc.Director.getInstance = function() {
    return cc.firstUseDirector && (cc.firstUseDirector = !1, cc.s_SharedDirector = new cc.DisplayLinkDirector, cc.s_SharedDirector.init()),
    cc.s_SharedDirector
},
cc.firstRun = !0,
cc.defaultFPS = 60;
cc.PRIORITY_SYSTEM = -2147483648,
cc.PRIORITY_NON_SYSTEM = cc.PRIORITY_SYSTEM + 1,
cc.ArrayVerifyType = function(e, t) {
    if (e && e.length > 0) for (var n = 0; n < e.length; n++) if (! (e[n] instanceof t)) return cc.log("element type is wrong!"),
    !1;
    return ! 0
},
cc.ArrayRemoveObjectAtIndex = function(e, t) {
    e.splice(t, 1)
},
cc.ArrayRemoveObject = function(e, t) {
    for (var n = 0; n < e.length; n++) e[n] == t && e.splice(n, 1)
},
cc.ArrayRemoveArray = function(e, t) {
    for (var n = 0; n < t.length; n++) cc.ArrayRemoveObject(e, t[n])
},
cc.ArrayGetIndexOfValue = function(e, t) {
    for (var n = 0; n < e.length; n++) if (e[n] == t) return n;
    return - 1
},
cc.ArrayAppendObject = function(e, t) {
    e.push(t)
},
cc.ArrayAppendObjectToIndex = function(e, t, n) {
    var r = e.slice(0, n),
    i = e.slice(n);
    return r.push(t),
    e = r.concat(i),
    e
},
cc.ArrayGetIndexOfObject = function(e, t) {
    for (var n = 0; n < e.length; n++) if (e[n] == t) return n;
    return - 1
},
cc.ArrayContainsObject = function(e, t) {
    return cc.ArrayGetIndexOfObject(e, t) != -1
},
cc.HASH_FIND_INT = function(e, t) {
    if (e == null) return null;
    for (var n = 0; n < e.length; n++) if (e[n].target === t) return e[n];
    return null
},
cc.ListEntry = function(e, t, n, r, i, s) {
    this.prev = e,
    this.next = t,
    this.target = n,
    this.priority = r,
    this.paused = i,
    this.markedForDeletion = s
},
cc.HashUpdateEntry = function(e, t, n, r) {
    this.list = e,
    this.entry = t,
    this.target = n,
    this.hh = r
},
cc.HashSelectorEntry = function(e, t, n, r, i, s, o) {
    this.timers = e,
    this.target = t,
    this.timerIndex = n,
    this.currentTimer = r,
    this.currentTimerSalvaged = i,
    this.paused = s,
    this.hh = o
},
cc.Timer = cc.Class.extend({
    _interval: 0,
    _selector: "",
    _target: null,
    _elapsed: 0,
    _runForever: !1,
    _useDelay: !1,
    _timesExecuted: 0,
    _repeat: 0,
    _delay: 0,
    ctor: function() {},
    getInterval: function() {
        return this._interval
    },
    initWithTarget: function(e, t, n, r, i) {
        try {
            return this._target = e,
            this._selector = t,
            this._elapsed = -1,
            this._interval = n || 0,
            this._delay = i || 0,
            this._useDelay = this._delay > 0,
            this._repeat = r == null ? cc.REPEAT_FOREVER: r,
            this._runForever = this._repeat == cc.REPEAT_FOREVER,
            !0
        } catch(s) {
            return ! 1
        }
    },
    update: function(e) {
        this._elapsed == -1 ? (this._elapsed = 0, this._timesExecuted = 0) : this._runForever && !this._useDelay ? (this._elapsed += e, this._elapsed >= this._interval && (this._selector && (typeof this._selector == "string" ? this._target[this._selector](this._elapsed) : this._selector.call(this._target, this._elapsed)), this._elapsed = 0)) : (this._elapsed += e, this._useDelay ? this._elapsed >= this._delay && (this._target && this._selector && (typeof this._selector == "string" ? this._target[this._selector](this._elapsed) : this._selector.call(this._target, this._elapsed)), this._elapsed = this._elapsed - this._delay, this._timesExecuted += 1, this._useDelay = !1) : this._elapsed >= this._interval && (this._target && this._selector && (typeof this._selector == "string" ? this._target[this._selector](this._elapsed) : this._selector.call(this._target, this._elapsed)), this._elapsed = 0, this._timesExecuted += 1), this._timesExecuted > this._repeat && cc.Director.getInstance().getScheduler().unscheduleCallbackForTarget(this._target, this._selector))
    }
}),
cc.Timer.timerWithTarget = function(e, t, n) {
    if (arguments < 2) throw new Error("timerWithTarget'argument can't is null");
    var r = new cc.Timer;
    return arguments.length == 2 ? r.initWithTarget(e, t, 0, cc.REPEAT_FOREVER, 0) : r.initWithTarget(e, t, n, cc.REPEAT_FOREVER, 0),
    r
},
cc._sharedScheduler = null,
cc.Scheduler = cc.Class.extend({
    _timeScale: 1,
    _updatesNegList: null,
    _updates0List: null,
    _updatesPosList: null,
    _hashForUpdates: null,
    _hashForSelectors: null,
    _currentTarget: null,
    _currentTargetSalvaged: !1,
    _updateHashLocked: !1,
    ctor: function() {
        this._timeScale = 1,
        this._updatesNegList = [],
        this._updates0List = [],
        this._updatesPosList = [],
        this._hashForUpdates = [],
        this._hashForSelectors = [],
        this._currentTarget = null,
        this._currentTargetSalvaged = !1,
        this._updateHashLocked = !1
    },
    _removeHashElement: function(e) {
        e.Timer = null,
        e.target = null,
        cc.ArrayRemoveObject(this._hashForSelectors, e),
        e = null
    },
    _findElementFromArray: function(e, t) {
        for (var n = 0; n < e.length; n++) if (e[n].target == t) return e[n];
        return null
    },
    _removeUpdateFromHash: function(e) {
        var t = this._findElementFromArray(this._hashForUpdates, e.target);
        t && (cc.ArrayRemoveObject(t.list, t.entry), t.entry = null, t.target = null, cc.ArrayRemoveObject(this._hashForUpdates, t))
    },
    _priorityIn: function(e, t, n, r) {
        var i = new cc.ListEntry(null, null, t, n, r, !1);
        if (!e) e = [],
        e.push(i);
        else {
            var s = !1;
            for (var o = 0; o < e.length; o++) if (n < e[o].priority) {
                e = cc.ArrayAppendObjectToIndex(e, i, o),
                s = !0;
                break
            }
            s || e.push(i)
        }
        var u = new cc.HashUpdateEntry(e, i, t, null);
        return this._hashForUpdates.push(u),
        e
    },
    _appendIn: function(e, t, n) {
        var r = new cc.ListEntry(null, null, t, 0, n, !1);
        e.push(r);
        var i = new cc.HashUpdateEntry(e, r, t, null);
        this._hashForUpdates.push(i)
    },
    setTimeScale: function(e) {
        this._timeScale = e
    },
    getTimeScale: function() {
        return this._timeScale
    },
    update: function(e) {
        this._updateHashLocked = !0,
        this._timeScale != 1 && (e *= this._timeScale);
        var t, n;
        for (n = 0; n < this._updatesNegList.length; n++) t = this._updatesNegList[n],
        !t.paused && !t.markedForDeletion && t.target.update(e);
        for (n = 0; n < this._updates0List.length; n++) t = this._updates0List[n],
        !t.paused && !t.markedForDeletion && t.target.update(e);
        for (n = 0; n < this._updatesPosList.length; n++) t = this._updatesPosList[n],
        !t.paused && !t.markedForDeletion && t.target.update(e);
        var r;
        for (n = 0; n < this._hashForSelectors.length; n++) {
            this._currentTarget = this._hashForSelectors[n],
            r = this._currentTarget,
            this._currentTargetSalvaged = !1;
            if (!this._currentTarget.paused) for (r.timerIndex = 0; r.timerIndex < r.timers.length; r.timerIndex++) r.currentTimer = r.timers[r.timerIndex],
            r.currentTimerSalvaged = !1,
            r.currentTimer.update(e),
            r.currentTimer = null;
            this._currentTargetSalvaged && this._currentTarget.timers.length == 0 && this._removeHashElement(this._currentTarget)
        }
        for (n = 0; n < this._updatesNegList.length; n++) this._updatesNegList[n].markedForDeletion && this._removeUpdateFromHash(this._updatesNegList[n]);
        for (n = 0; n < this._updates0List.length; n++) this._updates0List[n].markedForDeletion && this._removeUpdateFromHash(this._updates0List[n]);
        for (n = 0; n < this._updatesPosList.length; n++) this._updatesPosList[n].markedForDeletion && this._removeUpdateFromHash(this._updatesPosList[n]);
        this._updateHashLocked = !1,
        this._currentTarget = null
    },
    scheduleCallbackForTarget: function(e, t, n, r, i, s) {
        cc.Assert(t, "scheduler.scheduleCallbackForTarget() Argument callback_fn must be non-NULL"),
        cc.Assert(e, "scheduler.scheduleCallbackForTarget() Argument target must be non-NULL"),
        n = n || 0,
        r = r == null ? cc.REPEAT_FOREVER: r,
        i = i || 0,
        s = s || !1;
        var o = cc.HASH_FIND_INT(this._hashForSelectors, e);
        o ? cc.Assert(o.paused == s, "Sheduler.scheduleCallbackForTarget()") : (o = new cc.HashSelectorEntry(null, e, 0, null, null, s, null), this._hashForSelectors.push(o));
        var u;
        if (o.timers == null) o.timers = [];
        else for (var a = 0; a < o.timers.length; a++) {
            u = o.timers[a];
            if (t == u._selector) {
                cc.log("CCSheduler#scheduleCallback. Callback already scheduled. Updating interval from:" + u.getInterval().toFixed(4) + " to " + n.toFixed(4)),
                u._interval = n;
                return
            }
        }
        u = new cc.Timer,
        u.initWithTarget(e, t, n, r, i),
        o.timers.push(u)
    },
    scheduleUpdateForTarget: function(e, t, n) {
        var r = cc.HASH_FIND_INT(this._hashForUpdates, e);
        if (r) {
            cc.COCOS2D_DEBUG >= 1 && cc.Assert(r.entry.markedForDeletion, ""),
            r.entry.markedForDeletion = !1;
            return
        }
        t == 0 ? this._appendIn(this._updates0List, e, n) : t < 0 ? this._updatesNegList = this._priorityIn(this._updatesNegList, e, t, n) : this._updatesPosList = this._priorityIn(this._updatesPosList, e, t, n)
    },
    unscheduleCallbackForTarget: function(e, t) {
        if (e == null || t == null) return;
        var n = cc.HASH_FIND_INT(this._hashForSelectors, e);
        if (n != null) for (var r = 0; r < n.timers.length; r++) {
            var i = n.timers[r];
            if (t == i._selector) {
                i == n.currentTimer && !n.currentTimerSalvaged && (n.currentTimerSalvaged = !0),
                cc.ArrayRemoveObjectAtIndex(n.timers, r),
                n.timerIndex >= r && n.timerIndex--,
                n.timers.length == 0 && (this._currentTarget == n ? this._currentTargetSalvaged = !0 : this._removeHashElement(n));
                return
            }
        }
    },
    unscheduleUpdateForTarget: function(e) {
        if (e == null) return;
        var t = cc.HASH_FIND_INT(this._hashForUpdates, e);
        t != null && (this._updateHashLocked ? t.entry.markedForDeletion = !0 : this._removeUpdateFromHash(t.entry))
    },
    unscheduleAllCallbacksForTarget: function(e) {
        if (e == null) return;
        var t = cc.HASH_FIND_INT(this._hashForSelectors, e);
        t && (!t.currentTimerSalvaged && cc.ArrayContainsObject(t.timers, t.currentTimer) && (t.currentTimerSalvaged = !0), t.timers.length = 0, this._currentTarget == t ? this._currentTargetSalvaged = !0 : this._removeHashElement(t)),
        this.unscheduleUpdateForTarget(e)
    },
    unscheduleAllCallbacks: function() {
        this.unscheduleAllCallbacksWithMinPriority(cc.PRIORITY_SYSTEM)
    },
    unscheduleAllCallbacksWithMinPriority: function(e) {
        var t;
        for (t = 0; t < this._hashForSelectors.length; t++) this.unscheduleAllCallbacksForTarget(this._hashForSelectors[t].target);
        if (e < 0) for (t = 0; t < this._updatesNegList.length; t++) this.unscheduleUpdateForTarget(this._updatesNegList[t].target);
        if (e <= 0) for (t = 0; t < this._updates0List.length; t++) this.unscheduleUpdateForTarget(this._updates0List[t].target);
        for (t = 0; t < this._updatesPosList.length; t++) this._updatesPosList[t].priority >= e && this.unscheduleUpdateForTarget(this._updatesPosList[t].target)
    },
    pauseAllTargets: function() {
        return this.pauseAllTargetsWithMinPriority(cc.PRIORITY_SYSTEM)
    },
    pauseAllTargetsWithMinPriority: function(e) {
        var t = [],
        n,
        r;
        for (n = 0; n < this._hashForSelectors.length; n++) r = this._hashForSelectors[n],
        r && (r.paused = !0, t.push(r.target));
        if (e < 0) for (n = 0; n < this._updatesNegList.length; n++) r = this._updatesNegList[n],
        r && (r.paused = !0, t.push(r.target));
        if (e <= 0) for (n = 0; n < this._updates0List.length; n++) r = this._updates0List[n],
        r && (r.paused = !0, t.push(r.target));
        for (n = 0; n < this._updatesPosList.length; n++) r = this._updatesPosList[n],
        r && (r.paused = !0, t.push(r.target));
        return t
    },
    resumeTargets: function(e) {
        if (!e) return;
        for (var t = 0; t < e.length; t++) this.resumeTarget(e[t])
    },
    pauseTarget: function(e) {
        cc.Assert(e != null, "Scheduler.pauseTarget():entry must be non nil");
        var t = cc.HASH_FIND_INT(this._hashForSelectors, e);
        t && (t.paused = !0);
        var n = cc.HASH_FIND_INT(this._hashForUpdates, e);
        n && (cc.Assert(n.entry != null, "Scheduler.pauseTarget():entry must be non nil"), n.entry.paused = !0)
    },
    resumeTarget: function(e) {
        cc.Assert(e != null, "");
        var t = cc.HASH_FIND_INT(this._hashForSelectors, e);
        t && (t.paused = !1);
        var n = cc.HASH_FIND_INT(this._hashForUpdates, e);
        n && (cc.Assert(n.entry != null, "Scheduler.resumeTarget():entry must be non nil"), n.entry.paused = !1)
    },
    isTargetPaused: function(e) {
        cc.Assert(e != null, "Scheduler.isTargetPaused():target must be non nil");
        var t = cc.HASH_FIND_INT(this._hashForSelectors, e);
        return t ? t.paused: !1
    }
});
cc.Loader = cc.Class.extend({
    resourceCount: 0,
    loadedResourceCount: 0,
    timer: 0,
    isLoadedComplete: function() {
        var e = cc.Loader.getInstance();
        e.loadedResourceCount == e.resourceCount ? e.onload ? e.timer = setTimeout(e.onload, 16) : cc.Assert(0, "cocos2d:no load callback defined") : (e.onloading ? e.timer = setTimeout(e.onloading, 16) : cc.LoaderScene.getInstance().draw(), e.timer = setTimeout(e.isLoadedComplete, 16))
    },
    onResLoadingErr: function(e) {
        cc.log("cocos2d:Failed loading resource: " + e)
    },
    onResLoaded: function() {
        this.loadedResourceCount++
    },
    getProgressBar: function() {
        var e = this.loadedResourceCount / this.resourceCount;
        return e = 0 | e * 100,
        e
    },
    onload: undefined,
    onerror: undefined,
    onloading: undefined,
    _registerFaceFont: function(e) {
        var t = e.srcArr;
        if (e.srcArr && t.length > 0) {
            var n = document.createElement("style");
            n.type = "text/css",
            document.body.appendChild(n);
            var r = "@font-face { font-family:" + e.fontName + "; src:";
            for (var i = 0; i < t.length; i++) r += "url('" + encodeURI(t[i].src) + "') format('" + t[i].type + "')",
            r += i == t.length - 1 ? ";": ",";
            n.textContent += r + "};"
        }
        cc.Loader.getInstance().onResLoaded()
    },
    preload: function(e) {
        var t = cc.TextureCache.getInstance(),
        n = cc.AudioEngine.getInstance(),
        r = cc.SAXParser.getInstance(),
        i = cc.FileUtils.getInstance();
        this.loadedResourceCount = 0,
        this.resourceCount = e.length;
        for (var s = 0; s < e.length; s++) switch (e[s].type) {
        case "image":
            t.addImage(e[s].src);
            break;
        case "sound":
            n.preloadSound(e[s].src);
            break;
        case "plist":
        case "tmx":
        case "fnt":
            r.preloadPlist(e[s].src);
            break;
        case "tga":
            break;
        case "ccbi":
        case "binary":
            i.preloadBinaryFileData(e[s].src);
            break;
        case "face-font":
            this._registerFaceFont(e[s]);
            break;
        default:
            throw "cocos2d:unknow type : " + e[s].type
        }
        this.isLoadedComplete()
    }
}),
cc.Loader.getInstance = function() {
    return this._instance || (this._instance = new cc.Loader),
    this._instance
},
cc.Loader._instance = null,
cc.LoaderScene = cc.Class.extend({
    _logo: new Image,
    ctor: function() {
        this._logo.src = "",
        this._logo.width = 160,
        this._logo.height = 200
    },
    draw: function() {
        var e = (cc.canvas.width - this._logo.width) / 2,
        t = (cc.canvas.height - this._logo.height) / 2;
        cc.renderContext.clearRect(0, -cc.canvas.height, cc.canvas.width, cc.canvas.height),
        cc.renderContext.fillStyle = "#202020",
        cc.renderContext.fillRect(0, -cc.canvas.height, cc.canvas.width, cc.canvas.height),
        cc.drawingUtil.drawImage(this._logo, cc.p(e, t)),
        cc.renderContext.fillStyle = "#b2b4b3",
        cc.renderContext.font = "Bold 12px Verdana",
        cc.renderContext.textAlign = "left",
        cc.drawingUtil.fillText("Loading " + cc.Loader.getInstance().getProgressBar() + "%", e + 30, t - 15)
    }
}),
cc.LoaderScene.getInstance = function() {
    return this._instance || (this._instance = new cc.LoaderScene),
    this._instance
},
cc.LoaderScene._instance = null;
cc.DrawingPrimitive = cc.Class.extend({
    _renderContext: null,
    setRenderContext: function(e) {
        this._renderContext = e
    },
    getRenderContext: function() {
        return this._renderContext
    },
    ctor: function(e) {
        this._renderContext = e
    },
    drawPoint: function(e) {
        cc.log("DrawingPrimitive.drawPoint() not implement!")
    },
    drawPoints: function(e, t) {
        cc.log("DrawingPrimitive.drawPoints() not implement!")
    },
    drawLine: function(e, t) {
        cc.log("DrawingPrimitive.drawLine() not implement!")
    },
    drawPoly: function(e, t, n, r) {
        cc.log("DrawingPrimitive.drawPoly() not implement!")
    },
    drawCircle: function(e, t, n, r, i) {
        if (r == "undefined" || r == 0) return;
        var s = 1;
        i && ++s;
        var o = 2 * Math.PI / r,
        u = [];
        for (var a = 0; a <= r; a++) {
            var f = a * o,
            l = t * Math.cos(f + n) + e.x,
            c = t * Math.sin(f + n) + e.y,
            h = cc.p(l * cc.CONTENT_SCALE_FACTOR(), c * cc.CONTENT_SCALE_FACTOR());
            u.push(h)
        }
        if (i) {
            var p = cc.p(e.x * cc.CONTENT_SCALE_FACTOR(), e.y * cc.CONTENT_SCALE_FACTOR());
            u.push(p)
        }
        this.drawPoly(u, r + 2, !0, !1)
    },
    drawQuadBezier: function(e, t, n, r) {
        cc.log("DrawingPrimitive.drawQuadBezier() not implement!")
    },
    drawCubicBezier: function(e, t, n, r, i) {
        cc.log("DrawingPrimitive.drawCubicBezier() not implement!")
    },
    drawCatmullRom: function(e, t) {
        cc.log("DrawingPrimitive.drawCardinalSpline() not implement!")
    },
    drawCardinalSpline: function(e, t, n) {
        cc.log("DrawingPrimitive.drawCardinalSpline() not implement!")
    }
}),
cc.DrawingPrimitiveCanvas = cc.DrawingPrimitive.extend({
    drawPoint: function(e, t) {
        t || (t = 1);
        var n = cc.p(e.x * cc.CONTENT_SCALE_FACTOR(), e.y * cc.CONTENT_SCALE_FACTOR());
        this._renderContext.beginPath(),
        this._renderContext.arc(n.x, -n.y, t * cc.CONTENT_SCALE_FACTOR(), 0, Math.PI * 2, !1),
        this._renderContext.closePath(),
        this._renderContext.fill()
    },
    drawPoints: function(e, t, n) {
        if (e == null) return;
        n || (n = 1),
        this._renderContext.beginPath();
        for (var r = 0; r < e.length; r++) this._renderContext.arc(e[r].x * cc.CONTENT_SCALE_FACTOR(), -e[r].y * cc.CONTENT_SCALE_FACTOR(), n * cc.CONTENT_SCALE_FACTOR(), 0, Math.PI * 2, !1);
        this._renderContext.closePath(),
        this._renderContext.fill()
    },
    drawLine: function(e, t) {
        this._renderContext.beginPath(),
        this._renderContext.moveTo(e.x * cc.CONTENT_SCALE_FACTOR(), -e.y * cc.CONTENT_SCALE_FACTOR()),
        this._renderContext.lineTo(t.x * cc.CONTENT_SCALE_FACTOR(), -t.y * cc.CONTENT_SCALE_FACTOR()),
        this._renderContext.closePath(),
        this._renderContext.stroke()
    },
    drawPoly: function(e, t, n, r) {
        r == "undefined" && (r = !1);
        if (e == null) return;
        if (e.length < 3) throw new Error("Polygon's point must greater than 2");
        var i = e[0];
        this._renderContext.beginPath(),
        this._renderContext.moveTo(i.x * cc.CONTENT_SCALE_FACTOR(), -i.y * cc.CONTENT_SCALE_FACTOR());
        for (var s = 1; s < e.length; s++) this._renderContext.lineTo(e[s].x * cc.CONTENT_SCALE_FACTOR(), -e[s].y * cc.CONTENT_SCALE_FACTOR());
        n && this._renderContext.closePath(),
        r ? this._renderContext.fill() : this._renderContext.stroke()
    },
    drawCircle: function(e, t, n, r, i) {
        this._renderContext.beginPath();
        var s = n - Math.PI * 2;
        this._renderContext.arc(0 | e.x, 0 | -e.y, t, -n, -s, !1),
        i && this._renderContext.lineTo(0 | e.x, 0 | -e.y),
        this._renderContext.stroke()
    },
    drawQuadBezier: function(e, t, n, r) {
        var i = [],
        s = 0;
        for (var o = 0; o < r; o++) {
            var u = Math.pow(1 - s, 2) * e.x + 2 * (1 - s) * s * t.x + s * s * n.x,
            a = Math.pow(1 - s, 2) * e.y + 2 * (1 - s) * s * t.y + s * s * n.y;
            i.push(cc.p(u * cc.CONTENT_SCALE_FACTOR(), a * cc.CONTENT_SCALE_FACTOR())),
            s += 1 / r
        }
        i.push(cc.p(n.x * cc.CONTENT_SCALE_FACTOR(), n.y * cc.CONTENT_SCALE_FACTOR())),
        this.drawPoly(i, r + 1, !1, !1)
    },
    drawCubicBezier: function(e, t, n, r, i) {
        var s = [],
        o = 0;
        for (var u = 0; u < i; u++) {
            var a = Math.pow(1 - o, 3) * e.x + 3 * Math.pow(1 - o, 2) * o * t.x + 3 * (1 - o) * o * o * n.x + o * o * o * r.x,
            f = Math.pow(1 - o, 3) * e.y + 3 * Math.pow(1 - o, 2) * o * t.y + 3 * (1 - o) * o * o * n.y + o * o * o * r.y;
            s.push(cc.p(a * cc.CONTENT_SCALE_FACTOR(), f * cc.CONTENT_SCALE_FACTOR())),
            o += 1 / i
        }
        s.push(cc.p(r.x * cc.CONTENT_SCALE_FACTOR(), r.y * cc.CONTENT_SCALE_FACTOR())),
        this.drawPoly(s, i + 1, !1, !1)
    },
    drawCatmullRom: function(e, t) {
        this.drawCardinalSpline(e, .5, t)
    },
    drawCardinalSpline: function(e, t, n) {
        cc.renderContext.strokeStyle = "rgba(255,255,255,1)";
        var r = [],
        i,
        s,
        o = 1 / e.length;
        for (var u = 0; u < n + 1; u++) {
            var a = u / n;
            a == 1 ? (i = e.length - 1, s = 1) : (i = 0 | a / o, s = (a - o * i) / o);
            var f = cc.CardinalSplineAt(cc.getControlPointAt(e, i - 1), cc.getControlPointAt(e, i - 0), cc.getControlPointAt(e, i + 1), cc.getControlPointAt(e, i + 2), t, s);
            r.push(f)
        }
        this.drawPoly(r, n + 1, !1, !1)
    },
    drawImage: function(e, t, n, r, i) {
        var s = arguments.length;
        switch (s) {
        case 2:
            var o = e.height;
            this._renderContext.drawImage(e, t.x, -(t.y + o));
            break;
        case 3:
            this._renderContext.drawImage(e, t.x, -(t.y + n.height), n.width, n.height);
            break;
        case 5:
            this._renderContext.drawImage(e, t.x, t.y, n.width, n.height, r.x, -(r.y + i.height), i.width, i.height);
            break;
        default:
            throw new Error("Argument must be non-nil")
        }
    },
    drawStar: function(e, t, n) {
        var r = e || this._renderContext;
        n instanceof cc.Color4F && (n = new cc.Color3B(0 | n.r * 255, 0 | n.g * 255, 0 | n.b * 255));
        var i = "rgba(" + n.r + "," + n.g + "," + n.b;
        r.fillStyle = i + ",1)";
        var s = t / 10;
        r.beginPath(),
        r.moveTo( - t, t),
        r.lineTo(0, s),
        r.lineTo(t, t),
        r.lineTo(s, 0),
        r.lineTo(t, -t),
        r.lineTo(0, -s),
        r.lineTo( - t, -t),
        r.lineTo( - s, 0),
        r.lineTo( - t, t),
        r.closePath(),
        r.fill();
        var o = r.createRadialGradient(0, 0, s, 0, 0, t);
        o.addColorStop(0, i + ", 1)"),
        o.addColorStop(.3, i + ", 0.8)"),
        o.addColorStop(1, i + ", 0.0)"),
        r.fillStyle = o,
        r.beginPath();
        var u = 0,
        a = cc.PI2;
        r.arc(0, 0, t - s, u, a, !1),
        r.closePath(),
        r.fill()
    },
    drawColorBall: function(e, t, n) {
        var r = e || this._renderContext;
        n instanceof cc.Color4F && (n = new cc.Color3B(0 | n.r * 255, 0 | n.g * 255, 0 | n.b * 255));
        var i = "rgba(" + n.r + "," + n.g + "," + n.b,
        s = t / 10,
        o = r.createRadialGradient(0, 0, s, 0, 0, t);
        o.addColorStop(0, i + ", 1)"),
        o.addColorStop(.3, i + ", 0.8)"),
        o.addColorStop(.6, i + ", 0.4)"),
        o.addColorStop(1, i + ", 0.0)"),
        r.fillStyle = o,
        r.beginPath();
        var u = 0,
        a = cc.PI2;
        r.arc(0, 0, t, u, a, !1),
        r.closePath(),
        r.fill()
    },
    fillText: function(e, t, n) {
        this._renderContext.fillText(e, t, -n)
    }
}),
cc.PI2 = Math.PI * 2;
cc.ORIENTATION_PORTRAIT = 0,
cc.ORIENTATION_PORTRAIT_UPSIDE_DOWN = 1,
cc.ORIENTATION_LANDSCAPE_LEFT = 2,
cc.ORIENTATION_LANDSCAPE_RIGHT = 3,
cc.CANVAS = 0,
cc.WEBGL = 1,
cc.drawingUtil = null,
cc.renderContext = null,
cc.canvas = null,
cc.gameDiv = null,
cc.renderContextType = cc.CANVAS,
cc.originalCanvasSize = cc.size(0, 0),
window.requestAnimFrame = function() {
    return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame
} (),
window.console || (window.console = {},
window.console.log = function() {},
window.console.assert = function() {}),
cc.isAddedHiddenEvent = !1,
cc.setup = function(e, t, n) {
    function o() {
        document[i] || cc.Director.getInstance()._resetLastUpdate()
    }
    var r = cc.$(e) || cc.$("#" + e);
    r.tagName == "CANVAS" ? (t = t || r.width, n = n || r.height, cc.container = cc.$new("DIV"), cc.canvas = r, cc.canvas.parentNode.insertBefore(cc.container, cc.canvas), cc.canvas.appendTo(cc.container), cc.container.style.width = (t || 480) + "px", cc.container.style.height = (n || 320) + "px",cc.container.style.margin="auto", cc.container.setAttribute("id", "Cocos2dGameContainer"), cc.canvas.setAttribute("width", t || 480), cc.canvas.setAttribute("height", n || 320)) : (r.tagName != "DIV" && cc.log("Warning: target element is not a DIV or CANVAS"), t = t || parseInt(r.style.width), n = n || parseInt(r.style.height), cc.canvas = cc.$new("CANVAS"), cc.canvas.addClass("gameCanvas"), cc.canvas.setAttribute("width", t || 480), cc.canvas.setAttribute("height", n || 320), cc.container = r, r.appendChild(cc.canvas), cc.container.style.width = (t || 480) + "px", cc.container.style.height = (n || 320) + "px"),
    cc.container.style.position = "relative",
    cc.container.style.overflow = "hidden",
    cc.container.top = "100%",
    cc.renderContext = cc.canvas.getContext("2d"),
    cc.renderContextType = cc.CANVAS,
    cc.renderContextType == cc.CANVAS && (cc.renderContext.translate(0, cc.canvas.height), cc.drawingUtil = new cc.DrawingPrimitiveCanvas(cc.renderContext)),
    cc.originalCanvasSize = cc.size(cc.canvas.width, cc.canvas.height),
    cc.gameDiv = cc.container,
    cc.log(cc.ENGINE_VERSION),
    cc.setContextMenuEnable(!1),
    cc.Browser.isMobile && cc._addUserSelectStatus();
    var i, s;
    typeof document.hidden != "undefined" ? (i = "hidden", s = "visibilitychange") : typeof document.mozHidden != "undefined" ? (i = "mozHidden", s = "mozvisibilitychange") : typeof document.msHidden != "undefined" ? (i = "msHidden", s = "msvisibilitychange") : typeof document.webkitHidden != "undefined" && (i = "webkitHidden", s = "webkitvisibilitychange"),
    typeof document.addEventListener == "undefined" || typeof i == "undefined" ? cc.isAddedHiddenEvent = !1 : (cc.isAddedHiddenEvent = !0, document.addEventListener(s, o, !1))
},
cc._addUserSelectStatus = function() {
    var e = document.createElement("style");
    e.type = "text/css",
    document.body.appendChild(e),
    e.textContent = "body,canvas,div{ -moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;-khtml-user-select: none;-webkit-tap-highlight-color:rgba(0,0,0,0);}"
},
cc._isContextMenuEnable = !1,
cc.setContextMenuEnable = function(e) {
    cc._isContextMenuEnable = e,
    cc._isContextMenuEnable ? cc.canvas.oncontextmenu = function() {}: cc.canvas.oncontextmenu = function() {
        event.returnValue = !1
    }
},
cc.Application = cc.Class.extend({
    ctor: function() {
        this._animationInterval = 0,
        cc.Assert(!cc._sharedApplication, "CCApplication ctor"),
        cc._sharedApplication = this
    },
    setAnimationInterval: function(e) {
        this._animationInterval = e
    },
    statusBarFrame: function(e) {
        e && (e = cc.rect(0, 0, 0, 0))
    },
    run: function() {
        if (!this.applicationDidFinishLaunching()) return 0;
        if (window.requestAnimFrame && this._animationInterval == 1 / 60) {
            var e = function() {
                cc.Director.getInstance().mainLoop(),
                window.requestAnimFrame(e)
            };
            cc.log(window.requestAnimFrame),
            window.requestAnimFrame(e)
        } else {
            var e = function() {
                cc.Director.getInstance().mainLoop()
            };
            setInterval(e, this._animationInterval * 1e3)
        }
    },
    _animationInterval: null
}),
cc.Application.sharedApplication = function() {
    return cc.Assert(cc._sharedApplication, "sharedApplication"),
    cc._sharedApplication
},
cc.Application.getCurrentLanguage = function() {
    var e = cc.LANGUAGE_ENGLISH,
    t = navigator.language;
    t = t.toLowerCase();
    switch (t) {
    case "zh-cn":
        e = cc.LANGUAGE_CHINESE;
        break;
    case "fr":
        e = cc.LANGUAGE_FRENCH;
        break;
    case "it":
        e = cc.LANGUAGE_ITALIAN;
        break;
    case "de":
        e = cc.LANGUAGE_GERMAN;
        break;
    case "es":
        e = cc.LANGUAGE_SPANISH;
        break;
    case "ru":
        e = cc.LANGUAGE_RUSSIAN
    }
    return e
},
cc._sharedApplication = null;
cc.SAXParser = cc.Class.extend({
    xmlDoc: null,
    parser: null,
    xmlList: [],
    plist: [],
    parse: function(e) {
        var e = this.getList(e);
        window.DOMParser ? (this.parser = new DOMParser, this.xmlDoc = this.parser.parseFromString(e, "text/xml")) : (this.xmlDoc = new ActiveXObject("Microsoft.XMLDOM"), this.xmlDoc.async = "false", this.xmlDoc.loadXML(e)),
        this.xmlDoc == null && cc.log("cocos2d:xml " + this.xmlDoc + " not found!");
        var t = this.xmlDoc.documentElement;
        if (t.tagName != "plist") throw "cocos2d:Not a plist file";
        var n = null;
        for (var r = 0,
        i = t.childNodes.length; r < i; r++) {
            n = t.childNodes[r];
            if (n.nodeType == 1) break
        }
        return this.plist = this._parseNode(n),
        this.plist
    },
    tmxParse: function(e) {
        var e = this.getList(e);
        return window.DOMParser ? (this.parser = new DOMParser, this.xmlDoc = this.parser.parseFromString(e, "text/xml")) : (this.xmlDoc = new ActiveXObject("Microsoft.XMLDOM"), this.xmlDoc.async = "false", this.xmlDoc.loadXML(e)),
        this.xmlDoc == null && cc.log("cocos2d:xml " + this.xmlDoc + " not found!"),
        this.xmlDoc
    },
    _parseNode: function(e) {
        var t = null;
        switch (e.tagName) {
        case "dict":
            t = this._parseDict(e);
            break;
        case "array":
            t = this._parseArray(e);
            break;
        case "string":
            if (e.childNodes.length == 1) t = e.firstChild.nodeValue;
            else {
                t = "";
                for (var n = 0; n < e.childNodes.length; n++) t += e.childNodes[n].nodeValue
            }
            break;
        case "false":
            t = !1;
            break;
        case "true":
            t = !0;
            break;
        case "real":
            t = parseFloat(e.firstChild.nodeValue);
            break;
        case "integer":
            t = parseInt(e.firstChild.nodeValue, 10)
        }
        return t
    },
    _parseArray: function(e) {
        var t = [];
        for (var n = 0,
        r = e.childNodes.length; n < r; n++) {
            var i = e.childNodes[n];
            if (i.nodeType != 1) continue;
            t.push(this._parseNode(i))
        }
        return t
    },
    _parseDict: function(e) {
        var t = {},
        n = null;
        for (var r = 0,
        i = e.childNodes.length; r < i; r++) {
            var s = e.childNodes[r];
            if (s.nodeType != 1) continue;
            s.tagName == "key" ? n = s.firstChild.nodeValue: t[n] = this._parseNode(s)
        }
        return t
    },
    preloadPlist: function(e) {
        e = cc.FileUtils.getInstance().fullPathFromRelativePath(e);
        if (window.XMLHttpRequest) {
            var t = new XMLHttpRequest;
            t.overrideMimeType && t.overrideMimeType("text/xml")
        } else t = new ActiveXObject("Microsoft.XMLHTTP");
        if (t != null) {
            var n = this;
            t.onreadystatechange = function() {
                t.readyState == 4 && (t.responseText ? (cc.Loader.getInstance().onResLoaded(), n.xmlList[e] = t.responseText, t = null) : cc.Assert("cocos2d:There was a problem retrieving the xml data:" + t.statusText))
            },
            t.open("GET", e, !0),
            t.send(null)
        } else cc.Assert("cocos2d:Your browser does not support XMLHTTP.")
    },
    getName: function(e) {
        var t = e.lastIndexOf("/", e.length) + 1,
        n = e.lastIndexOf(".", e.length);
        return e.substring(t, n)
    },
    getExt: function(e) {
        var t = e.lastIndexOf(".", e.length) + 1;
        return e.substring(t, e.length)
    },
    getList: function(e) {
        return this.xmlList != null ? this.xmlList[e] : null
    }
}),
cc.SAXParser.getInstance = function() {
    return this._instance || (this._instance = new cc.SAXParser),
    this._instance
},
cc.SAXParser._instance = null;
cc.AppController = cc.Class.extend({
    didFinishLaunchingWithOptions: function() {
        return cc.Application.sharedApplication().run(),
        !0
    },
    applicationWillResignActive: function() {
        cc.Director.getInstance().pause()
    },
    applicationDidBecomeActive: function() {
        cc.Director.getInstance().resume()
    },
    applicationDidEnterBackground: function() {
        cc.Application.sharedApplication().applicationDidEnterBackground()
    },
    applicationWillEnterForeground: function() {
        cc.Application.sharedApplication().applicationWillEnterForeground()
    },
    applicationWillTerminate: function() {}
}),
cc.AppController.shareAppController = function() {
    return cc.sharedAppController == null && (cc.sharedAppController = new cc.AppController),
    cc.Assert(cc.sharedAppController, "shareAppController"),
    cc.sharedAppController
},
cc.sharedAppController = null;
cc.DOM = {},
cc.DOMEditMode = !0,
cc.DOM.addMethods = function(e) {
    for (funcs in cc.DOM.methods) e[funcs] = cc.DOM.methods[funcs]
},
cc.DOM.methods = {
    setPosition: function(e, t) {
        arguments.length == 2 ? (this._position.x = e, this._position.y = t) : this._position = e,
        this.dom.translates(this._position.x, -this._position.y)
    },
    setPositionY: function(e) {
        this._position.y = e,
        this.dom.translates(this._position.x, -this._position.y)
    },
    setPositionX: function(e) {
        this._position.x = e,
        this.dom.translates(this._position.x, -this._position.y)
    },
    setScale: function(e, t) {
        this._scaleX = e,
        this._scaleY = t || e,
        this.dom.resize(this._scaleX, this._scaleY)
    },
    setScaleX: function(e) {
        this._scaleX = e,
        this.dom.resize(this._scaleX, this._scaleY)
    },
    setScaleY: function(e) {
        this._scaleY = e,
        this.dom.resize(this._scaleX, this._scaleY)
    },
    setAnchorPoint: function(e) {
        this._anchorPoint = e,
        this._anchorPointInPoints = cc.p(this._contentSize.width * this._anchorPoint.x, this._contentSize.height * this._anchorPoint.y),
        this.dom.style[cc.$.pfx + "TransformOrigin"] = "" + this._anchorPointInPoints.x + "px " + this._anchorPointInPoints.y + "px",
        this.isIgnoreAnchorPointForPosition() ? (this.dom.style.marginLeft = 0, this.dom.style.marginBottom = 0) : (this.dom.style.marginLeft = this.isToggler ? 0 : -this._anchorPointInPoints.x + "px", this.dom.style.marginBottom = -this._anchorPointInPoints.y + "px")
    },
    setContentSize: function(e) {
        cc.Size.CCSizeEqualToSize(e, this._contentSize) || (this._contentSize = e, this._anchorPointInPoints = cc.p(this._contentSize.width * this._anchorPoint.x, this._contentSize.height * this._anchorPoint.y), this.dom.width = e.width, this.dom.height = e.height, this.setAnchorPoint(this.getAnchorPoint())),
        this.canvas && (this.canvas.width = this._contentSize.width, this.canvas.height = this._contentSize.height),
        cc.DOMEditMode && !this.placeholder && (this.dom.style.width = this._contentSize.width + "px", this.dom.style.height = this._contentSize.height + "px", this.dom.addClass("CCDOMEdit")),
        this.redraw()
    },
    setRotation: function(e) {
        if (this._rotation == e) return;
        this._rotation = e,
        this._rotationRadians = this._rotation * (Math.PI / 180),
        this.dom.rotate(e)
    },
    setSkewX: function(e) {
        this._skewX = e,
        this.dom.setSkew(this._skewX, this._skewY)
    },
    setSkewY: function(e) {
        this._skewY = e,
        this.dom.setSkew(this._skewX, this._skewY)
    },
    setVisible: function(e) {
        this._visible = e,
        this.dom && (this.dom.style.visibility = e ? "visible": "hidden")
    },
    _setZOrder: function(e) {
        this._zOrder = e,
        this.dom && (this.dom.zIndex = e)
    },
    setParent: function(e) {
        this._parent = e,
        cc.DOM.parentDOM(this)
    },
    resumeSchedulerAndActions: function() {
        this.getScheduler().resumeTarget(this),
        this.getActionManager().resumeTarget(this),
        this.dom && !this.dom.parentNode && (this.getParent() ? cc.DOM.parentDOM(this) : this.dom.appendTo(cc.container)),
        this.dom && (this.dom.style.visibility = "visible")
    },
    pauseSchedulerAndActions: function() {
        this.getScheduler().pauseTarget(this),
        this.getActionManager().pauseTarget(this),
        this.dom && (this.dom.style.visibility = "hidden")
    },
    cleanup: function() {
        this.stopAllActions(),
        this.unscheduleAllCallbacks(),
        this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.cleanup),
        this.dom && this.dom.remove()
    },
    removeFromParentAndCleanup: function() {
        this.dom.remove()
    },
    setOpacity: function(e) {
        this._opacity = e,
        this.dom.style.opacity = e / 255
    },
    redraw: function() {
        if (this.isSprite) {
            var e = this._children;
            this._children = null,
            cc.Sprite.prototype.visit.call(this, this.ctx),
            this._children = e
        } else cc.Sprite.prototype.visit.call(this, this.ctx)
    }
},
cc.DOM.parentDOM = function(e) {
    var t = e.getParent();
    if (!t || !e.dom) return ! 1;
    t.dom || (cc.DOM.placeHolder(t), t.setParent = cc.DOM.methods.setParent),
    e.dom.appendTo(t.dom);
    var n;
    return (n = t.getParent()) ? cc.DOM.parentDOM(t) : t.isRunning() && t.dom.appendTo(cc.container),
    !0
},
cc.DOM.setTransform = function(e) {
    if (e.ctx) {
        e.ctx.translate(e.getAnchorPointInPoints().x, e.getAnchorPointInPoints().y);
        if (e.isSprite) {
            var t = e._children;
            e._children = null,
            cc.Sprite.prototype.visit.call(e, e.ctx),
            e._children = t
        } else cc.Sprite.prototype.visit.call(e, e.ctx)
    }
    e.dom && (e.dom.position.x = e.getPosition().x, e.dom.position.y = -e.getPosition().y, e.dom.rotation = e.getRotation(), e.dom.scale = {
        x: e.getScaleX(),
        y: e.getScaleY()
    },
    e.dom.skew = {
        x: e.getSkewX(),
        y: e.getSkewY()
    },
    e.setAnchorPoint && e.setAnchorPoint(e.getAnchorPoint()), e.dom.transforms(), e.dom.position.y = -e.getPosition().y, e.dom.rotation = e.getRotation(), e.dom.scale = {
        x: e.getScaleX(),
        y: e.getScaleY()
    },
    e.dom.skew = {
        x: e.getSkewX(),
        y: e.getSkewY()
    },
    e.setAnchorPoint && e.setAnchorPoint(e.getAnchorPoint()), e.dom.transforms())
},
cc.DOM.forSprite = function(e) {
    e.dom = cc.$new("div"),
    e.canvas = cc.$new("canvas"),
    e.canvas.width = e.getContentSize().width,
    e.canvas.height = e.getContentSize().height,
    cc.DOMEditMode && (e.dom.style.width = e.getContentSize().width + "px", e.dom.style.height = e.getContentSize().height + "px", e.dom.addClass("CCDOMEdit")),
    e.dom.style.position = "absolute",
    e.dom.style.bottom = 0,
    e.ctx = e.canvas.getContext("2d"),
    e.dom.appendChild(e.canvas),
    e.getParent() && cc.DOM.parentDOM(e),
    e.isSprite = !0
},
cc.DOM.forMenuToggler = function(e) {
    e.dom = cc.$new("div"),
    e.dom2 = cc.$new("div"),
    e.dom.appendChild(e.dom2);
    for (var t = 0; t < e._subItems.length; t++) cc.DOM.convert(e._subItems[t]),
    e.dom2.appendChild(e._subItems[t].dom),
    e._subItems[t].setPosition(cc.p(0, 0));
    e.dom.style.marginLeft = 0,
    e.setSelectedIndex = function(e) {
        this._selectedIndex = e;
        for (var t = 0; t < this._subItems.length; t++) this._subItems[t].setVisible(!1);
        this._subItems[e].setVisible(!0)
    },
    e.setSelectedIndex(e.getSelectedIndex()),
    e.dom2.addEventListener("click",
    function() {
        e.activate()
    }),
    e.dom2.addEventListener("mousedown",
    function() {
        for (var t = 0; t < e._subItems.length; t++) e._subItems[t]._isEnabled = !0,
        e._subItems[t]._running = !0,
        e._subItems[t].selected(),
        e._subItems[t]._isEnabled = !1,
        e._subItems[t]._running = !1;
        e._subItems[e.getSelectedIndex()]._isEnabled = !0,
        e._subItems[e.getSelectedIndex()]._running = !0
    }),
    e.dom2.addEventListener("mouseup",
    function() {
        for (var t = 0; t < e._subItems.length; t++) e._subItems[t]._isEnabled = !0,
        e._subItems[t].unselected(),
        e._subItems[t]._isEnabled = !1;
        e._subItems[e.getSelectedIndex()]._isEnabled = !0
    }),
    e.dom2.addEventListener("mouseout",
    function() {
        if (e.mouseDown) {
            for (var t = 0; t < e._subItems.length; t++) e._subItems[t]._isEnabled = !0,
            e._subItems[t].unselected(),
            e._subItems[t]._isEnabled = !1;
            e._subItems[e.getSelectedIndex()]._isEnabled = !0,
            e.mouseDown = !1
        }
    }),
    e.dom.style.position = "absolute",
    e.isToggler = !0
},
cc.DOM.forMenuItem = function(e) {
    e.dom = cc.$new("div"),
    e.canvas = cc.$new("canvas"),
    e.canvas.width = e.getContentSize().width,
    e.canvas.height = e.getContentSize().height,
    cc.DOMEditMode && (e.dom.style.width = e.getContentSize().width + "px", e.dom.style.height = e.getContentSize().height + "px", e.dom.addClass("CCDOMEdit")),
    e.dom.style.position = "absolute",
    e.dom.style.bottom = 0,
    e.ctx = e.canvas.getContext("2d"),
    e.dom.appendChild(e.canvas),
    e.getParent() && cc.DOM.parentDOM(e),
    e._selector && (e.canvas.addEventListener("click",
    function() {
        e.activate()
    }), e.canvas.addEventListener("mousedown",
    function() {
        e.selected(),
        e.ctx.save(),
        e.ctx.setTransform(1, 0, 0, 1, 0, 0),
        e.ctx.clearRect(0, 0, e.canvas.width, e.canvas.height),
        e.ctx.restore(),
        e.mouseDown = !0,
        cc.Sprite.prototype.visit.call(e, e.ctx)
    }), e.canvas.addEventListener("mouseup",
    function() {
        e.unselected(),
        e.ctx.save(),
        e.ctx.setTransform(1, 0, 0, 1, 0, 0),
        e.ctx.clearRect(0, 0, e.canvas.width, e.canvas.height),
        e.ctx.restore(),
        cc.Sprite.prototype.visit.call(e, e.ctx)
    }), e.canvas.addEventListener("mouseout",
    function() {
        e.mouseDown && (e.unselected(), e.ctx.save(), e.ctx.setTransform(1, 0, 0, 1, 0, 0), e.ctx.clearRect(0, 0, e.canvas.width, e.canvas.height), e.ctx.restore(), cc.Sprite.prototype.visit.call(e, e.ctx), e.mouseDown = !1)
    }))
},
cc.DOM.placeHolder = function(e) {
    e.dom = cc.$new("div"),
    e.placeholder = !0,
    e.dom.style.position = "absolute",
    e.dom.style.bottom = 0,
    e.dom.style.width = (e.getContentSize().width || cc.Director.getInstance().getWinSize().width) + "px",
    e.dom.style.maxHeight = (e.getContentSize().height || cc.Director.getInstance().getWinSize().height) + "px",
    e.dom.style.margin = 0,
    cc.DOM.setTransform(e),
    e.dom.transforms(),
    cc.DOM.addMethods(e)
},
cc.DOM.convert = function() {
    if (arguments.length > 1) return cc.DOM.convert(arguments);
    if (arguments.length == 1 && !arguments[0].length) return cc.DOM.convert([arguments[0]]);
    var e = arguments[0];
    for (var t = 0; t < e.length; t++) {
        e[t] instanceof cc.Sprite ? e[t].dom || cc.DOM.forSprite(e[t]) : e[t] instanceof cc.MenuItemToggle ? e[t].dom || cc.DOM.forMenuToggler(e[t]) : e[t] instanceof cc.MenuItem ? e[t].dom || cc.DOM.forMenuItem(e[t]) : cc.log("DOM converter only supports sprite and menuitems yet"),
        cc.DOM.addMethods(e[t]),
        e[t].visit = function() {},
        e[t].transform = function() {},
        cc.DOM.setTransform(e[t]),
        e[t].setVisible(e[t].isVisible());
        if (cc.DOMEditMode) {
            if (!cc.DOM.tooltip) {
                var n = cc.$new("style");
                n.textContent = ".CCDOMEdit:hover{border: rgba(255,0,0,0.5) 2px dashed;left: -2px;} .CCDOMEdit  #CCCloseButton{width:80px;height:15px;background: rgba(0,0,0,0.4);border:1px solid #aaaaaa;font-size: 9px;line-height:9px;color:#bbbbbb;}  .CCTipWindow .CCTipMove{cursor:move;} .CCTipWindow .CCTipRotate{cursor:w-resize;} .CCTipWindow .CCTipScale{cursor:ne-resize;} .CCTipWindow .CCTipSkew{cursor:se-resize;} .CCTipWindow input{width:40px;background: rgba(0,0,0,0.5);color:white;border:none;border-bottom: 1px solid #fff;} div.CCTipWindow:hover{color:rgb(50,50,255);}",
                document.body.appendChild(n),
                cc.container.style.overflow = "visible";
                var r = cc.DOM.tooltip = cc.$new("div");
                r.mouseDown = !1,
                document.body.appendChild(r),
                r.addClass("CCTipWindow"),
                r.style.width = "140px",
                r.style.height = "134px",
                r.style.background = "rgba(50,50,50,0.5)",
                r.style.border = "1px rgba(255,255,255,0.5) solid",
                r.style.borderRadius = "5px",
                r.style.color = "rgb(255,255,255)",
                r.style.boxShadow = "0 0 10px 1px rgba(0,0,0,0.5)",
                r.style.position = "absolute",
                r.style.display = "none",
                r.style.top = 0,
                r.style.left = "-150px",
                r.style[cc.$.pfx + "Transform"] = "translate3d(0,0,100px)",
                r.style[cc.$.pfx + "UserSelect"] = "none",
                r.innerHTML = '<table><tr><td><label class="CCTipMove">Move</label></td><td><input type="text" value="12" id="posx"/></td><td><input type="text" value="12" id="posy"/></td></tr><tr><td><label class="CCTipRotate">Rotate</label></td><td><input type="text" value="12" id="rot"/></td></tr><tr><td><label class="CCTipScale">Scale</label></td><td><input type="text" value="12" id="scalex"/></td><td><input type="text" value="12" id="scaley"/></td></tr><tr><td><label class="CCTipSkew">Skew</label></td><td><input type="text" value="12" id="skewx"/></td><td><input type="text" value="12" id="skewy"/></td></tr></table><button id="CCCloseButton">Close</button>',
                r.updateNumbers = function() {
                    var e = cc.DOM.tooltip;
                    e.target && (e.find("#posx").value = e.target._position.x, e.find("#posy").value = e.target._position.y, e.find("#rot").value = e.target._rotation, e.find("#scalex").value = e.target._scaleX, e.find("#scaley").value = e.target._scaleY, e.find("#skewx").value = e.target._skewX, e.find("#skewy").value = e.target._skewY)
                },
                r.find(".CCTipMove").addEventListener("mousedown",
                function(e) {
                    r.mode = "move",
                    r.initialpos = {
                        x: e.clientX,
                        y: e.clientY
                    },
                    r.mouseDown = !0
                }),
                r.find(".CCTipRotate").addEventListener("mousedown",
                function(e) {
                    var t = cc.$.findpos(cc.canvas),
                    t = {
                        x: t.x,
                        y: t.y + cc.canvas.height
                    };
                    r.nodepos = r.target.getPosition(),
                    r.nodepos = {
                        x: t.x + r.nodepos.x,
                        y: t.y - r.nodepos.y
                    },
                    r.startPos = {
                        x: e.x,
                        y: e.y
                    },
                    r.mode = "rot",
                    r.initialpos = {
                        x: e.clientX,
                        y: e.clientY
                    },
                    r.mouseDown = !0;
                    var n = {
                        x: r.startPos.x,
                        y: r.nodepos.y
                    },
                    i = r.startPos,
                    s = r.nodepos,
                    o = Math.sqrt(Math.pow(s.x - n.x, 2) + Math.pow(s.y - n.y, 2)),
                    u = Math.sqrt(Math.pow(i.x - n.x, 2) + Math.pow(i.y - n.y, 2)),
                    a = Math.sqrt(Math.pow(i.x - s.x, 2) + Math.pow(i.y - s.y, 2)),
                    f = (o * o + a * a - u * u) / (2 * o * a),
                    f = Math.acos(f) * (180 / cc.PI);
                    r.startAngle = f,
                    r.startRot = r.target.getRotation()
                }),
                r.find(".CCTipScale").addEventListener("mousedown",
                function(e) {
                    r.mode = "scale",
                    r.initialpos = {
                        x: e.clientX,
                        y: e.clientY
                    },
                    r.mouseDown = !0
                }),
                r.find(".CCTipSkew").addEventListener("mousedown",
                function(e) {
                    r.mode = "skew",
                    r.initialpos = {
                        x: e.clientX,
                        y: e.clientY
                    },
                    r.mouseDown = !0
                }),
                document.body.addEventListener("mousemove",
                function(e) {
                    if (r.mode == "move") {
                        var t = e.clientX - r.initialpos.x,
                        n = e.clientY - r.initialpos.y,
                        i = r.target.getPosition();
                        r.target.setPosition(t + i.x, -n + i.y),
                        r.initialpos = {
                            x: e.clientX,
                            y: e.clientY
                        },
                        r.updateNumbers()
                    } else if (r.mode == "rot") {
                        var s = {
                            x: e.x,
                            y: e.y
                        },
                        o = r.startPos,
                        u = r.nodepos,
                        a = Math.sqrt(Math.pow(u.x - s.x, 2) + Math.pow(u.y - s.y, 2)),
                        f = Math.sqrt(Math.pow(o.x - s.x, 2) + Math.pow(o.y - s.y, 2)),
                        l = Math.sqrt(Math.pow(o.x - u.x, 2) + Math.pow(o.y - u.y, 2)),
                        c = (a * a + l * l - f * f) / (2 * a * l),
                        c = Math.acos(c) * (180 / cc.PI),
                        n = e.clientY - r.initialpos.y,
                        t = e.clientX - r.initialpos.x;
                        e.y > r.startPos.y ? r.target.setRotation( - c + r.startRot) : r.target.setRotation(c + r.startRot),
                        r.updateNumbers()
                    } else if (r.mode == "scale") {
                        var n = e.clientY - r.initialpos.y,
                        t = e.clientX - r.initialpos.x,
                        h = r.target.getScaleX(),
                        p = r.target.getScaleY();
                        r.target.setScale(h - t / 150, p + n / 150),
                        r.initialpos = {
                            x: e.clientX,
                            y: e.clientY
                        },
                        r.updateNumbers()
                    } else if (r.mode == "skew") {
                        var n = e.clientY - r.initialpos.y,
                        t = e.clientX - r.initialpos.x,
                        d = r.target.getSkewX(),
                        v = r.target.getSkewY();
                        r.target.setSkewX(d - t / 4),
                        r.target.setSkewY(v + n / 4),
                        r.initialpos = {
                            x: e.clientX,
                            y: e.clientY
                        },
                        r.updateNumbers()
                    }
                }),
                r.find("#CCCloseButton").addEventListener("click",
                function() {
                    r.mode = null,
                    r.style.display = "none",
                    r.mouseDown = !1
                }),
                document.addEventListener("mouseup",
                function() {
                    r.mode = null,
                    r.mouseDown = !1
                })
            }
            e[t].dom.ccnode = e[t];
            var i = e[t];
            e[t].dom.addEventListener("mouseover",
            function() {
                this.style.zIndex = 999999;
                if (this.showTooltipDiv !== undefined && this.showTooltipDiv === !1) return;
                if (!cc.DOM.tooltip.mouseDown) {
                    var e = cc.$.findpos(this);
                    cc.DOM.tooltip.style.display = "block",
                    cc.DOM.tooltip.prependTo(this),
                    cc.DOM.tooltip.target = i,
                    this.style.zIndex = 999999,
                    cc.DOM.tooltip.updateNumbers()
                }
            }),
            e[t].dom.addEventListener("mouseout",
            function() {
                this.style.zIndex = this.ccnode._zOrder
            })
        }
    }
}
var cc = cc || {};
cc.AudioEngine = cc.Class.extend({
    _supportedFormat: [],
    _soundEnable: !1,
    _effectList: {},
    _muiscList: {},
    _soundList: {},
    _isMusicPlaying: !1,
    _playingMusic: null,
    _effectsVolume: 1,
    _maxAudioInstance: 10,
    _capabilities: {
        mp3: !1,
        ogg: !1,
        wav: !1
    },
    ctor: function() {
        this._supportedFormat = [];
        var e = document.createElement("audio");
        e.canPlayType && (this._capabilities.mp3 = "no" != e.canPlayType("audio/mpeg") && "" != e.canPlayType("audio/mpeg"), this._capabilities.ogg = "no" != e.canPlayType('audio/ogg; codecs="vorbis"') && "" != e.canPlayType('audio/ogg; codecs="vorbis"'), this._capabilities.wav = "no" != e.canPlayType('audio/wav; codecs="1"') && "" != e.canPlayType('audio/wav; codecs="1"'), this._soundEnable = this._capabilities.mp3 || this._capabilities.ogg || this._capabilities.wav)
    },
    init: function() {
        return this._getSupportedAudioFormat(), this._soundEnable
    },
    preloadSound: function(e) {
        if (this._soundEnable) {
            var t = this._getExtFromFullPath(e),
                n = this._getPathWithoutExt(e);
            if (this._checkAudioFormatSupported(t) && !this._soundList.hasOwnProperty(n)) {
                var r = new Audio(e);
                r.preload = "auto", r.addEventListener("canplaythrough", function(e) {
                    this.removeEventListener("canplaythrough", arguments.callee, !1)
                }, !1), r.addEventListener("error", function(e) {
                    this.removeEventListener("error", arguments.callee, !1), cc.Loader.getInstance().onResLoadingErr()
                }, !1), this._soundList[n] = !0, r.load()
            }
        };
        cc.Loader.getInstance().onResLoaded()
    },
    playMusic: function(e, t) {
        var n = this._getPathWithoutExt(e),
            r = this._supportedFormat[0],
            i;
        this._muiscList.hasOwnProperty(this._playingMusic) && this._muiscList[this._playingMusic].pause(), this._playingMusic = n, this._muiscList.hasOwnProperty(this._playingMusic) ? i = this._muiscList[this._playingMusic] : (i = new Audio(n + "." + r), i.preload = "auto", this._muiscList[this._playingMusic] = i, i.addEventListener("playing", function(e) {
            cc.AudioEngine._instance._isMusicPlaying = !0
        }, !1), i.addEventListener("pause", function(e) {
            cc.AudioEngine._instance._isMusicPlaying = !1
        }, !1)), i.loop = t || !1, i.play()
    },
    stopMusic: function(e) {
        if (this._muiscList.hasOwnProperty(this._playingMusic)) {
            var t = this._muiscList[this._playingMusic];
            t.pause(), t.currentTime = t.duration, e && delete this._muiscList[this._playingMusic]
        }
    },
    pauseMusic: function() {
        this._muiscList.hasOwnProperty(this._playingMusic) && this._muiscList[this._playingMusic].pause()
    },
    resumeMusic: function() {
        this._muiscList.hasOwnProperty(this._playingMusic) && this._muiscList[this._playingMusic].play()
    },
    rewindMusic: function() {
        this._muiscList.hasOwnProperty(this._playingMusic) && (this._muiscList[this._playingMusic].currentTime = 0, this._muiscList[this._playingMusic].play())
    },
    willPlayMusic: function() {
        return !1
    },
    isMusicPlaying: function() {
        return this._isMusicPlaying
    },
    getMusicVolume: function() {
        return this._muiscList.hasOwnProperty(this._playingMusic) ? this._muiscList[this._playingMusic].volume : 0
    },
    setMusicVolume: function(e) {
        if (this._muiscList.hasOwnProperty(this._playingMusic)) {
            var t = this._muiscList[this._playingMusic];
            e > 1 ? t.volume = 1 : e < 0 ? t.volume = 0 : t.volume = e
        }
    },
    playEffect: function(e, t) {
        var n = this._getPathWithoutExt(e),
            r = this._supportedFormat[0],
            i = this._getEffectList(n),
            s;
        if (i.length > 0) for (var o = 0; o < i.length; o++) if (i[o].ended) {
            s = i[o], s.currentTime = 0;
            break
        };
        if (!s) {
            if (i.length >= this._maxAudioInstance) return cc.log("Error: " + e + " greater than " + this._maxAudioInstance), n;
            s = new Audio(n + "." + r), i.push(s)
        };
        return t && (s.loop = t), s.play(), n
    },
    getEffectsVolume: function() {
        return this._effectsVolume
    },
    setEffectsVolume: function(e) {
        e > 1 ? this._effectsVolume = 1 : e < 0 ? this._effectsVolume = 0 : this._effectsVolume = e;
        var t, n;
        for (var r in this._effectList) {
            t = this._effectList[r];
            if (t.length > 0) for (var i = 0; i < t.length; i++) n = t[i], n.volume = this._effectsVolume
        }
    },
    pauseEffect: function(e) {
        var t = this._getPathWithoutExt(e);
        if (this._effectList.hasOwnProperty(t)) {
            var n = this._effectList[t],
                r;
            for (var i = n.length - 1; i >= 0; i--) r = n[i], r.ended || r.pause()
        }
    },
    pauseAllEffects: function() {
        var e, t;
        for (var n in this._effectList) {
            e = this._effectList[n];
            for (var r = 0; r < e.length; r++) t = e[r], t.ended || t.pause()
        }
    },
    resumeEffect: function(e) {
        var t, n, r = this._getPathWithoutExt(e);
        if (this._effectList.hasOwnProperty(r)) {
            t = this._effectList[r];
            if (t.length > 0) for (var i = 0; i < t.length; i++) n = t[i], n.ended || n.play()
        }
    },
    resumeAllEffects: function() {
        var e, t;
        for (var n in this._effectList) {
            e = this._effectList[n];
            if (e.length > 0) for (var r = 0; r < e.length; r++) t = e[r], t.ended || t.play()
        }
    },
    stopEffect: function(e) {
        var t, n, r = this._getPathWithoutExt(e);
        if (this._effectList.hasOwnProperty(r)) {
            t = this._effectList[r];
            if (t.length > 0) for (var i = 0; i < t.length; i++) n = t[i], n.ended || (n.loop = !1, n.currentTime = n.duration)
        }
    },
    stopAllEffects: function() {
        var e, t;
        for (var n in this._effectList) {
            e = this._effectList[n];
            for (var r = 0; r < e.length; r++) t = e[r], t.ended || (t.loop = !1, t.currentTime = t.duration)
        }
    },
    unloadEffect: function(e) {
        var t = this._getPathWithoutExt(e);
        this._effectList.hasOwnProperty(t) && (this.stopEffect(e), delete this._effectList[t])
    },
    end: function() {
        this.stopMusic(), this.stopAllEffects()
    },
    _getEffectList: function(e) {
        return this._effectList.hasOwnProperty(e) ? this._effectList[e] : (this._effectList[e] = [], this._effectList[e])
    },
    _getPathWithoutExt: function(e) {
        var t = e.lastIndexOf(".");
        return t != -1 ? e.substring(0, t) : e
    },
    _getExtFromFullPath: function(e) {
        var t = e.lastIndexOf(".");
        return t != -1 ? e.substring(t + 1, e.length) : -1
    },
    _checkAudioFormatSupported: function(e) {
        var t;
        for (var n = 0; n < this._supportedFormat.length; n++) {
            t = this._supportedFormat[n];
            if (t == e) return !0
        };
        return !1
    },
    _getSupportedAudioFormat: function() {
        if (!this._soundEnable) return;
        this._capabilities.mp3 && this._supportedFormat.push("mp3"), this._capabilities.ogg && this._supportedFormat.push("ogg"), this._capabilities.wav && this._supportedFormat.push("wav")
    }
}), cc.AudioEngine._instance = null, cc.AudioEngine.getInstance = function() {
    return this._instance || (this._instance = new cc.AudioEngine, this._instance.init()), this._instance
}