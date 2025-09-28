window.Lottery || (window.Lottery = new Object),
window.Game || (window.Game = new Object);
var img_path = "/public/main/lhc/play/";
function setCookie(t, e) {
    var n = new Date;
    n.setTime(n.getTime() + 2592e6),
    document.cookie = t + "=" + escape(e) + ";expires=" + n.toGMTString()
}
function getCookie(t) {
    var e, n = new RegExp("(^| )" + t + "=([^;]*)(;|$)");
    return (e = document.cookie.match(n)) ? unescape(e[2]) : null
}
function delCookie(t) {
    var e = new Date;
    e.setTime(e.getTime() - 1);
    var n = getCookie(t);
    null != n && (document.cookie = t + "=" + n + ";expires=" + e.toGMTString())
}
function objlen(t) {
    if (t.constructor == Array) return t.length;
    if ("object" == typeof t) {
        var e = 0;
        for (var n in t) e++;
        return e
    }
    return t.length
} !
function(t) {
     t.Six = new Class({
        Implements: [Events, Options],
        options: {
            stop: 60
        },
        dom: {
            element: null,
            countdown: null,
            countdownObj: new Object,
            lastnumber: new Object,
            betindex: null,
            number: new Object,
            numbertype: new Object,
            nextindex: new Object
        },
        sound: function(t, e) {
            if (0 != getCookie("xs_sound")) {
                var n = this;
                if (!t) return UI.Sound(),
                void(n.data.sound = null);
                if (n.data.sound != t) {
                    var a = img_path + "images/" + media_path + "/" + t + ".mp3";
                    UI.Sound(a, {
                        loop: e
                    }),
                    n.data.sound = t
                }
            }
        },
        data: {
            status: null,
            STATUS: null,
            countdown: 0,
            width: null,
            result: null,
            countdown: 0,
            betindex: null,
            index: null,
            sound: null
        },
        initialize: function(t, e) {
            var n = this;
            n.dom.element = t = $(t),
            n.dom.countdown = n.dom.element.getElement(".countdown"),
            n.dom.countdown.getElements("em").each(function(t, e) {
                t.set("data-index", e),
                n.dom.countdownObj[e] = t
            }),
            n.dom.element.getElements(".top .number em").each(function(t, e) {
                n.dom.lastnumber[e] = t
            }),
            n.dom.element.getElement(".sound").set("html", "<img src='" + img_path + "images/" + media_path + "/v14.png'><img class='m2' src='" + img_path + "images/" + media_path + "/v15.png'>").addEvent("click",
            function() {
                this.toggleClass("off");
                try {
                    this.hasClass("off") ? (UI.Sound(), setCookie("xs_sound", 0)) : setCookie("xs_sound", 1)
                } catch(t) {}
            }),
            0 == getCookie("xs_sound") && n.dom.element.getElement(".sound").addClass("off"),
            n.dom.betindex = n.dom.element.getElement("[data-dom=betindex]"),
            n.dom.lottery = n.dom.element.getElement("[data-dom=lottery]"),
            n.dom.element.getElements(".content em").each(function(t, e) {
                n.dom.number[e] = t
            }),
            n.dom.element.getElements(".content label").each(function(t, e) {
                n.dom.numbertype[e] = t
            }),
            n.dom.countdown.getElements("i").each(function(t, e) {
                n.dom.nextindex[e] = t
            }),
            function() {
                n.data.width = t.getSize().x,
                n.data.height = t.getSize().y;
                var e = t.getParent();
                if ("false" != e.get("data-scale")) {
                    var a = document.body.offsetWidth / n.data.width,
                    s = "scale(" + a + ")";
                    t.setStyles({
                        "-webkit-transform": s
                    }),
                    e.setStyle("height", n.data.height * a)
                } else t.getElements(".money, .online, .bet").each(function(t) {
                    t.setStyle("display", "none")
                })
            } (),
            n.data.STATUS = {
                loading: "status-loading",
                show: "status-show"
            },
            n.timer.apply(n)
        },
        setStatus: function(t, e) {
            var n = this;
            n.data.status = t,
            e && Object.forEach(e,
            function(t, e) {
                n.data[e] = t
            })
        },
        setNumber: function() {
            this.data.result
        },
        timer: function() {
            var t = this;
            Object.forEach(t.data.STATUS,
            function(e, n) {
                n != t.data.status ? t.dom.element.removeClass(e) : t.dom.element.addClass(e)
            }),
            t.apply[t.data.status] && t.apply[t.data.status].apply(t),
            t.timer.delay(1e3, t)
        },
        apply: {
            loading: function() {
                this.dom.element.set("data-show", null),
                this.sound("loading", !0)
            },
            show: function() {
                var t = this,
                e = Math.round(t.data.countdown).toString(),
                n = Math.floor(e / 3600),
                a = Math.floor(e % 3600 / 60).toString(),
                s = (e % 60).toString();
                n > 0 ? n += "t": n = "",
                1 == a.length && (a = "0" + a),
                1 == s.length && (s = "0" + s),
                countdown = n + a + "t" + s;
                var o = objlen(t.dom.countdownObj),
                l = countdown.length;
                t.dom.countdown.set("data-length", l);
                for (var d = 0; d < o; d++) d < l ? (t.dom.countdownObj[d].hasClass("hide") && t.dom.countdownObj[d].removeClass("hide"), t.dom.countdownObj[d].set("class", "t" + countdown[d])) : t.dom.countdownObj[d].addClass("hide");
                //if (!t.dom.element.get("data-show")) {
				if (true) {
                    l = objlen(t.dom.number);
                    var i = {
                        "鼠": 1,
                        "牛": 2,
                        "虎": 3,
                        "兔": 4,
                        "龙": 5,
                        "蛇": 6,
                        "马": 7,
                        "羊": 8,
                        "猴": 9,
                        "鸡": 10,
                        "狗": 11,
                        "猪": 12
                    };
                    t.data.result.each(function(e, n) {
                        if (n != l) {
                            t.dom.number[n].set("class", "n" + e.toInt());
                            try {
                                t.dom.lastnumber[n].set("class", "n" + e.toInt())
                            } catch(t) {}
                            "six" == type ? (t.dom.numbertype[2 * n].set("class", "c" + e.toInt()), t.dom.numbertype[2 * n + 1].set("class", "r" + i[t.data.lhs[n]])) : (t.dom.numbertype[2 * n].set("class", e.toInt() >= 5 ? "r0": "r1"), t.dom.numbertype[2 * n + 1].set("class", e.toInt() % 2 ? "r2": "r3"))
                        }
                    });
                    var u = t.data.index;
                    switch (type) {
                    case "cqssc":
                    case "chungking":
                        u = t.data.index.substring(6);
                        break;
                    case "txffc":
                        u = t.data.index.substring(4)
                    }
                    t.dom.element.getElements(".r_data .i0").set("html", "<i>" + t.data.otime + "</i><i>" + u + "</i>"),
                    t.data.tema && t.dom.element.getElements(".r_data .i1").set("html", "<i>" + t.data.tema.join("</i><i>") + "</i>"),
                    t.data.lhs.each(function(e, n) {
                        t.dom.element.getElements(".r_data .i" + (n + 2)).set("text", e)
                    }),
					console.log(t.data.index);
                    t.dom.betindex.set("text", t.data.index),
                    t.dom.lottery.set("text", t.data.lottery);
                    var m = t.data.betindex;
                    for (d = 0; d < m.length; d++) t.dom.nextindex[d].set("class", "i" + m[d]);
                    t.sound("show"),
                    t.dom.element.set("data-show", !0)
                }
            }
        }
    })
} (Game),
Lottery.Time = new Class({
    Implements: [Events, Options],
    options: {
        type: null,
        callback: function() {}
    },
    data: {
        result: null,
        time: {
            bet: null,
            open: null
        }
    },
    request: null,
    initialize: function(t) {
        var e = this;
        e.setOptions(t),
        e.request = new Request.JSON({
            url: "/Home/Get/getLhc",
            onRequest: function() {
                e.data.result && (e.data.result.OpenNumber = "")
            },
            onSuccess: function(t) {
                t.success && (e.data.result = t.info, $$("[data-dom=lottery]").set("text", t.info.Name), (new Date).AddSecond(t.info.ServerTime), e.data.time.bet = (new Date).AddSecond(t.info.BetTime), e.data.time.open = (new Date).AddSecond(t.info.OpenTime), e.show())
            }
        }),
        e.gettime()
    },
    gettime: function() {
        this.request.post({
            Game: this.options.type
        })
    },
    localtime: function() {
        var t = this;
        t.data.result.BetTime = t.data.time.bet.getDateDiff(new Date).TotalSecond,
        t.data.result.OpenTime = t.data.time.open.getDateDiff(new Date).TotalSecond,
        t.show()
    },
    show: function() {
        var t = this;
        t.options.callback.apply(t),
        t.data.result.BetTime <= 0 || t.data.result.OpenTime <= 0 || !t.data.result.OpenNumber ? t.gettime.delay(1e3, t) : t.localtime.delay(1e3, t)
    },
    dispose: function() {
        this.request.cancel(),
        this.running = !1
    }
}),
BW.callback["lottery-six"] = function() {
    var t = new Game.Six(this.dom.element.getElement(".lottery"));
    new Lottery.Time({
        type: type,
        callback: function() {
            var e = this,
            n = e.data.result;
            n.OpenNumber ? t.setStatus("show", {
                lottery: e.data.result.Name,
                betindex: e.data.result.BetIndex,
                otime: e.data.result.OpenDateTime,
                index: e.data.result.OpenIndex,
                result: e.data.result.OpenNumber.split(","),
                tema: e.data.result.OpenTm.split(","),
                lhs: e.data.result.OpenLh.split(","),
                countdown: e.data.result.OpenTime
            }) : (t.setStatus("loading", {
                index: n.OpenIndex
            }), !0)
        }
    })
};