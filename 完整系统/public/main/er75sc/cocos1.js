function ClassManager() {
	return arguments.callee.name || arguments.callee.toString().match(/^function ([^(]+)/)[1];
}
var resourceDomain = 'http://'+document.domain;
var cc = cc = cc || {};
ClassManager.id = 0 | Math.random() * 998, ClassManager.compileSuper = function(e, t, n) {
	var r = e.toString(),
		i = r.indexOf("("),
		s = r.indexOf(")"),
		o = r.substring(i + 1, s);
	o = o.trim();
	var u = r.indexOf("{"),
		a = r.lastIndexOf("}"),
		r = r.substring(u + 1, a);
	while (r.indexOf("this._super") != -1) {
		var f = r.indexOf("this._super"),
			l = r.indexOf("(", f),
			c = r.indexOf(")", l),
			h = r.substring(l + 1, c);
		h = h.trim();
		var p = h ? "," : "",
			d = arguments.callee.ClassManager();
		r = r.substring(0, f) + d + "[" + n + "]." + t + ".call(this" + p + r.substring(l + 1);
	}
	return Function(o, r);
}, ClassManager.compileSuper.ClassManager = ClassManager, ClassManager.getNewID = function() {
	return this.id++;
}, function() {
	var e = !1,
		t = /\b_super\b/,
		n = document.ccConfig && document.ccConfig.CLASS_RELEASE_MODE ? document.ccConfig.CLASS_RELEASE_MODE : null;
	n && console.log("release Mode"), cc.Class = function() {}, cc.Class.extend = function(r) {
		function o() {
			!e && this.ctor && this.ctor.apply(this, arguments);
		}
		var i = this.prototype;
		e = !0;
		var s = new this();
		e = !1, o.id = ClassManager.getNewID(), ClassManager[o.id] = i;
		for (var u in r) n && typeof r[u] == "function" && typeof i[u] == "function" && t.test(r[u]) ? s[u] = ClassManager.compileSuper(r[u], u, o.id) : typeof r[u] == "function" && typeof i[u] == "function" && t.test(r[u]) ? s[u] = function(e, t) {
			return function() {
				var n = this._super;
				this._super = i[e];
				var r = t.apply(this, arguments);
				return this._super = n, r;
			};
		}(u, r[u]) : s[u] = r[u];
		return s.__pid = o.id, o.prototype = s, o.prototype.constructor = o, o.extend = arguments.callee, o.implement = function(e) {
			for (var t in e) s[t] = e[t];
		}, o;
	};
}(), cc.inherits = function(e, t) {
	function n() {}
	n.prototype = t.prototype, e.superClass_ = t.prototype, e.prototype = new n(), e.prototype.constructor = e;
}, cc.base = function(e, t, n) {
	var r = arguments.callee.caller;
	if (r.superClass_) return ret = r.superClass_.constructor.apply(e, Array.prototype.slice.call(arguments, 1)), ret;
	var i = Array.prototype.slice.call(arguments, 2),
		s = !1;
	for (var o = e.constructor; o; o = o.superClass_ && o.superClass_.constructor) if (o.prototype[t] === r) s = !0;
	else if (s) return o.prototype[t].apply(e, i);
	if (e[t] === r) return e.constructor.prototype[t].apply(e, i);
	throw Error("cc.base called from a method of one name to a method of a different name");
};
cc.Browser = {}, function() {
	cc.Browser.ua = navigator.userAgent.toLowerCase(), cc.Browser.platform = navigator.platform.toLowerCase(), cc.Browser.UA = cc.Browser.ua.match(/(opera|ie|firefox|chrome|version)[\s\/:]([\w\d\.]+)?.*?(safari|version[\s\/:]([\w\d\.]+)|$)/) || [null, "unknown", 0], cc.Browser.mode = cc.Browser.UA[1] == "ie" && document.documentMode, cc.Browser.type = cc.Browser.UA[1] == "version" ? cc.Browser.UA[3] : cc.Browser.UA[1], cc.Browser.isMobile = cc.Browser.ua.indexOf("mobile") != -1 || cc.Browser.ua.indexOf("android") != -1;
}(), cc.$ = function(e) {
	var t = this == cc ? document : this,
		n = e instanceof HTMLElement ? e : t.querySelector(e);
	return n && (n.find = n.find || cc.$, n.hasClass = n.hasClass ||
	function(e) {
		return this.className.match(new RegExp("(\\s|^)" + e + "(\\s|$)"));
	}, n.addClass = n.addClass ||
	function(e) {
		return this.hasClass(e) || (this.className && (this.className += " "), this.className += e), this;
	}, n.removeClass = n.removeClass ||
	function(e) {
		return this.hasClass(e) && (this.className = this.className.replace(e, "")), this;
	}, n.remove = n.remove ||
	function() {
		return this.parentNode && this.parentNode.removeChild(this), this;
	}, n.appendTo = n.appendTo ||
	function(e) {
		return e.appendChild(this), this;
	}, n.prependTo = n.prependTo ||
	function(e) {
		return e.childNodes[0] ? e.insertBefore(this, e.childNodes[0]) : e.appendChild(this), this;
	}, n.transforms = n.transforms ||
	function() {
		return this.style[cc.$.trans] = cc.$.translate(this.position) + cc.$.rotate(this.rotation) + cc.$.scale(this.scale) + cc.$.skew(this.skew), this;
	}, n.position = n.position || {
		x: 0,
		y: 0
	}, n.rotation = n.rotation || 0, n.scale = n.scale || {
		x: 1,
		y: 1
	}, n.skew = n.skew || {
		x: 0,
		y: 0
	}, n.translates = function(e, t) {
		return this.position.x = e, this.position.y = t, this.transforms(), this;
	}, n.rotate = function(e) {
		return this.rotation = e, this.transforms(), this;
	}, n.resize = function(e, t) {
		return this.scale.x = e, this.scale.y = t, this.transforms(), this;
	}, n.setSkew = function(e, t) {
		return this.skew.x = e, this.skew.y = t, this.transforms(), this;
	}), n;
};
switch (cc.Browser.type) {
case "firefox":
	cc.$.pfx = "Moz", cc.$.hd = !0;
	break;
case "chrome":
case "safari":
	cc.$.pfx = "webkit", cc.$.hd = !0;
	break;
case "opera":
	cc.$.pfx = "O", cc.$.hd = !1;
	break;
case "ie":
	cc.$.pfx = "ms", cc.$.hd = !1;
}
cc.$.trans = cc.$.pfx + "Transform", cc.$.translate = cc.$.hd ?
function(e) {
	return "translate3d(" + e.x + "px, " + e.y + "px, 0) ";
} : function(e) {
	return "translate(" + e.x + "px, " + e.y + "px) ";
}, cc.$.rotate = cc.$.hd ?
function(e) {
	return "rotateZ(" + e + "deg) ";
} : function(e) {
	return "rotate(" + e + "deg) ";
}, cc.$.scale = function(e) {
	return "scale(" + e.x + ", " + e.y + ") ";
}, cc.$.skew = function(e) {
	return "skewX(" + -e.x + "deg) skewY(" + e.y + "deg)";
}, cc.$new = function(e) {
	return cc.$(document.createElement(e));
}, cc.$.findpos = function(e) {
	var t = 0,
		n = 0;
	do t += e.offsetLeft, n += e.offsetTop;
	while (e = e.offsetParent);
	return {
		x: t,
		y: n
	};
};
cc.clone = function(e) {
	var t = e instanceof Array ? [] : {};
	for (var n in e) {
		var r = e[n];
		r instanceof Array ? t[n] = cc.clone(r) : typeof r != "object" || r instanceof cc.Node || r instanceof HTMLElement ? t[n] = r : t[n] = cc.clone(r);
	}
	return t;
}, cc.associateWithNative = function(e, t) {}, cc.IS_SHOW_DEBUG_ON_PAGE = cc.IS_SHOW_DEBUG_ON_PAGE || !1, cc._logToWebPage = function(e) {
	var t = document.getElementById("logInfoList");
	if (!t) {
		var n = document.createElement("Div");
		n.setAttribute("id", "logInfoDiv"), cc.canvas.parentNode.appendChild(n), n.setAttribute("width", "300"), n.setAttribute("height", cc.canvas.height), n.style.zIndex = "99999", n.style.position = "absolute", n.style.top = "0", n.style.left = "0", t = document.createElement("ul"), n.appendChild(t), t.setAttribute("id", "logInfoList"), t.style.height = "450px", t.style.color = "#fff", t.style.textAlign = "left", t.style.listStyle = "disc outside", t.style.fontSize = "12px", t.style.fontFamily = "arial", t.style.padding = "0 0 0 20px", t.style.margin = "0", t.style.textShadow = "0 0 3px #000", t.style.zIndex = "99998", t.style.position = "absolute", t.style.top = "0", t.style.left = "0", t.style.overflowY = "hidden";
		var r = document.createElement("Div");
		n.appendChild(r), r.style.width = "300px", r.style.height = cc.canvas.height + "px", r.style.opacity = "0.1", r.style.background = "#fff", r.style.border = "1px solid #dfdfdf", r.style.borderRadius = "8px";
	}
	var i = document.createElement("li");
	i.innerHTML = e, t.childNodes.length == 0 ? t.appendChild(i) : t.insertBefore(i, t.childNodes[0]);
}, cc.log = function(e) {
	cc.IS_SHOW_DEBUG_ON_PAGE ? cc._logToWebPage(e) : console.log(e);
}, cc.MessageBox = function(e) {
	console.log(e);
}, cc.Assert = function(e, t) {
	typeof console.assert == "function" ? console.assert(e, t) : e || t && alert(t);
}, cc.initDebugSetting = function() {
	cc.COCOS2D_DEBUG == 0 ? (cc.log = function() {}, cc.logINFO = function() {}, cc.logERROR = function() {}, cc.Assert = function() {}) : cc.COCOS2D_DEBUG == 1 ? (cc.logINFO = cc.log, cc.logERROR = function() {}) : cc.COCOS2D_DEBUG > 1 && (cc.logINFO = cc.log, cc.logERROR = cc.log);
}, cc.LANGUAGE_ENGLISH = 0, cc.LANGUAGE_CHINESE = 1, cc.LANGUAGE_FRENCH = 2, cc.LANGUAGE_ITALIAN = 3, cc.LANGUAGE_GERMAN = 4, cc.LANGUAGE_SPANISH = 5, cc.LANGUAGE_RUSSIAN = 6;
cc.Codec = {
	name: "Jacob__Codec"
}, cc.unzip = function() {
	return cc.Codec.GZip.gunzip.apply(cc.Codec.GZip, arguments);
}, cc.unzipBase64 = function() {
	var e = cc.Codec.Base64.decode.apply(cc.Codec.Base64, arguments);
	return cc.Codec.GZip.gunzip.apply(cc.Codec.GZip, [e]);
}, cc.unzipBase64AsArray = function(e, t) {
	t = t || 1;
	var n = this.unzipBase64(e),
		r = [],
		i, s, o;
	for (i = 0, o = n.length / t; i < o; i++) {
		r[i] = 0;
		for (s = t - 1; s >= 0; --s) r[i] += n.charCodeAt(i * t + s) << s * 8;
	}
	return r;
}, cc.unzipAsArray = function(e, t) {
	t = t || 1;
	var n = this.unzip(e),
		r = [],
		i, s, o;
	for (i = 0, o = n.length / t; i < o; i++) {
		r[i] = 0;
		for (s = t - 1; s >= 0; --s) r[i] += n.charCodeAt(i * t + s) << s * 8;
	}
	return r;
}, cc.StringToArray = function(e) {
	var t = e.split(","),
		n = [],
		r;
	for (r = 0; r < t.length; r++) n.push(parseInt(t[r]));
	return n;
};
cc.Codec.Base64 = {
	name: "Jacob__Codec__Base64"
}, cc.Codec.Base64._keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", cc.Codec.Base64.decode = function(t) {
	var n = [],
		r, i, s, o, u, a, f, l = 0;
	t = t.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	while (l < t.length) o = this._keyStr.indexOf(t.charAt(l++)), u = this._keyStr.indexOf(t.charAt(l++)), a = this._keyStr.indexOf(t.charAt(l++)), f = this._keyStr.indexOf(t.charAt(l++)), r = o << 2 | u >> 4, i = (u & 15) << 4 | a >> 2, s = (a & 3) << 6 | f, n.push(String.fromCharCode(r)), a != 64 && n.push(String.fromCharCode(i)), f != 64 && n.push(String.fromCharCode(s));
	return n = n.join(""), n;
}, cc.Codec.Base64.decodeAsArray = function(t, n) {
	n = n || 4;
	var r = this.decode(t),
		i = [],
		s, o, u;
	for (s = 0, u = r.length / n; s < u; s++) {
		i[s] = 0;
		for (o = n - 1; o >= 0; --o) i[s] += r.charCodeAt(s * n + o) << o * 8;
	}
	return i;
}, cc.Codec.Base64.encode = function(t) {
	t = encodeURIComponent(t);
	var n = "",
		r, i, s = "",
		o, u, a, f = "",
		l = 0;
	do r = t.charCodeAt(l++), i = t.charCodeAt(l++), s = t.charCodeAt(l++), o = r >> 2, u = (r & 3) << 4 | i >> 4, a = (i & 15) << 2 | s >> 6, f = s & 63, isNaN(i) ? a = f = 64 : isNaN(s) && (f = 64), n = n + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a) + this._keyStr.charAt(f), r = i = s = "", o = u = a = f = "";
	while (l < t.length);
	return n;
}, cc.uint8ArrayToUint32Array = function(e) {
	if (e.length % 4 != 0) return null;
	var t = e.length / 4,
		n = window.Uint8Array ? new Uint32Array(t) : [];
	for (var r = 0; r < t; r++) {
		var i = r * 4;
		n[r] = e[i] + e[i + 1] * 256 + e[i + 2] * 65536 + e[i + 3] * (1 << 24);
	}
	return n;
};
cc.Codec.GZip = function(t) {
	this.data = t, this.debug = !1, this.gpflags = undefined, this.files = 0, this.unzipped = [], this.buf32k = new Array(32768), this.bIdx = 0, this.modeZIP = !1, this.bytepos = 0, this.bb = 1, this.bits = 0, this.nameBuf = [], this.fileout = undefined, this.literalTree = new Array(cc.Codec.GZip.LITERALS), this.distanceTree = new Array(32), this.treepos = 0, this.Places = null, this.len = 0, this.fpos = new Array(17), this.fpos[0] = 0, this.flens = undefined, this.fmax = undefined;
}, cc.Codec.GZip.gunzip = function(e) {
	e.constructor !== Array && e.constructor === String;
	var t = new cc.Codec.GZip(e);
	return t.gunzip()[0][0];
}, cc.Codec.GZip.HufNode = function() {
	this.b0 = 0, this.b1 = 0, this.jump = null, this.jumppos = -1;
}, cc.Codec.GZip.LITERALS = 288, cc.Codec.GZip.NAMEMAX = 256, cc.Codec.GZip.bitReverse = [0, 128, 64, 192, 32, 160, 96, 224, 16, 144, 80, 208, 48, 176, 112, 240, 8, 136, 72, 200, 40, 168, 104, 232, 24, 152, 88, 216, 56, 184, 120, 248, 4, 132, 68, 196, 36, 164, 100, 228, 20, 148, 84, 212, 52, 180, 116, 244, 12, 140, 76, 204, 44, 172, 108, 236, 28, 156, 92, 220, 60, 188, 124, 252, 2, 130, 66, 194, 34, 162, 98, 226, 18, 146, 82, 210, 50, 178, 114, 242, 10, 138, 74, 202, 42, 170, 106, 234, 26, 154, 90, 218, 58, 186, 122, 250, 6, 134, 70, 198, 38, 166, 102, 230, 22, 150, 86, 214, 54, 182, 118, 246, 14, 142, 78, 206, 46, 174, 110, 238, 30, 158, 94, 222, 62, 190, 126, 254, 1, 129, 65, 193, 33, 161, 97, 225, 17, 145, 81, 209, 49, 177, 113, 241, 9, 137, 73, 201, 41, 169, 105, 233, 25, 153, 89, 217, 57, 185, 121, 249, 5, 133, 69, 197, 37, 165, 101, 229, 21, 149, 85, 213, 53, 181, 117, 245, 13, 141, 77, 205, 45, 173, 109, 237, 29, 157, 93, 221, 61, 189, 125, 253, 3, 131, 67, 195, 35, 163, 99, 227, 19, 147, 83, 211, 51, 179, 115, 243, 11, 139, 75, 203, 43, 171, 107, 235, 27, 155, 91, 219, 59, 187, 123, 251, 7, 135, 71, 199, 39, 167, 103, 231, 23, 151, 87, 215, 55, 183, 119, 247, 15, 143, 79, 207, 47, 175, 111, 239, 31, 159, 95, 223, 63, 191, 127, 255], cc.Codec.GZip.cplens = [3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15, 17, 19, 23, 27, 31, 35, 43, 51, 59, 67, 83, 99, 115, 131, 163, 195, 227, 258, 0, 0], cc.Codec.GZip.cplext = [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0, 99, 99], cc.Codec.GZip.cpdist = [1, 2, 3, 4, 5, 7, 9, 13, 17, 25, 33, 49, 65, 97, 129, 193, 257, 385, 513, 769, 1025, 1537, 2049, 3073, 4097, 6145, 8193, 12289, 16385, 24577], cc.Codec.GZip.cpdext = [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13], cc.Codec.GZip.border = [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15], cc.Codec.GZip.prototype.gunzip = function() {
	return this.outputArr = [], this.nextFile(), this.unzipped;
}, cc.Codec.GZip.prototype.readByte = function() {
	return this.bits += 8, this.bytepos < this.data.length ? this.data.charCodeAt(this.bytepos++) : -1;
}, cc.Codec.GZip.prototype.byteAlign = function() {
	this.bb = 1;
}, cc.Codec.GZip.prototype.readBit = function() {
	var e;
	return this.bits++, e = this.bb & 1, this.bb >>= 1, this.bb == 0 && (this.bb = this.readByte(), e = this.bb & 1, this.bb = this.bb >> 1 | 128), e;
}, cc.Codec.GZip.prototype.readBits = function(e) {
	var t = 0,
		n = e;
	while (n--) t = t << 1 | this.readBit();
	return e && (t = cc.Codec.GZip.bitReverse[t] >> 8 - e), t;
}, cc.Codec.GZip.prototype.flushBuffer = function() {
	this.bIdx = 0;
}, cc.Codec.GZip.prototype.addBuffer = function(e) {
	this.buf32k[this.bIdx++] = e, this.outputArr.push(String.fromCharCode(e)), this.bIdx == 32768 && (this.bIdx = 0);
}, cc.Codec.GZip.prototype.IsPat = function() {
	for (;;) {
		if (this.fpos[this.len] >= this.fmax) return -1;
		if (this.flens[this.fpos[this.len]] == this.len) return this.fpos[this.len]++;
		this.fpos[this.len]++;
	}
}, cc.Codec.GZip.prototype.Rec = function() {
	var e = this.Places[this.treepos],
		t;
	if (this.len == 17) return -1;
	this.treepos++, this.len++, t = this.IsPat();
	if (t >= 0) e.b0 = t;
	else {
		e.b0 = 32768;
		if (this.Rec()) return -1;
	}
	t = this.IsPat();
	if (t >= 0) e.b1 = t, e.jump = null;
	else {
		e.b1 = 32768, e.jump = this.Places[this.treepos], e.jumppos = this.treepos;
		if (this.Rec()) return -1;
	}
	return this.len--, 0;
}, cc.Codec.GZip.prototype.CreateTree = function(e, t, n, r) {
	var i;
	this.Places = e, this.treepos = 0, this.flens = n, this.fmax = t;
	for (i = 0; i < 17; i++) this.fpos[i] = 0;
	return this.len = 0, this.Rec() ? -1 : 0;
}, cc.Codec.GZip.prototype.DecodeValue = function(e) {
	var t, n, r = 0,
		i = e[r],
		s;
	for (;;) {
		s = this.readBit();
		if (s) {
			if (!(i.b1 & 32768)) return i.b1;
			i = i.jump, t = e.length;
			for (n = 0; n < t; n++) if (e[n] === i) {
				r = n;
				break;
			}
		} else {
			if (!(i.b0 & 32768)) return i.b0;
			r++, i = e[r];
		}
	}
	return -1;
}, cc.Codec.GZip.prototype.DeflateLoop = function() {
	var e, t, n, r, i;
	do {
		e = this.readBit(), n = this.readBits(2);
		if (n == 0) {
			var s, o;
			this.byteAlign(), s = this.readByte(), s |= this.readByte() << 8, o = this.readByte(), o |= this.readByte() << 8, (s ^ ~o) & 65535 && document.write("BlockLen checksum mismatch\n");
			while (s--) t = this.readByte(), this.addBuffer(t);
		} else if (n == 1) {
			var u;
			for (;;) {
				u = cc.Codec.GZip.bitReverse[this.readBits(7)] >> 1, u > 23 ? (u = u << 1 | this.readBit(), u > 199 ? (u -= 128, u = u << 1 | this.readBit()) : (u -= 48, u > 143 && (u += 136))) : u += 256;
				if (u < 256) this.addBuffer(u);
				else {
					if (u == 256) break;
					var i, a;
					u -= 257, i = this.readBits(cc.Codec.GZip.cplext[u]) + cc.Codec.GZip.cplens[u], u = cc.Codec.GZip.bitReverse[this.readBits(5)] >> 3, cc.Codec.GZip.cpdext[u] > 8 ? (a = this.readBits(8), a |= this.readBits(cc.Codec.GZip.cpdext[u] - 8) << 8) : a = this.readBits(cc.Codec.GZip.cpdext[u]), a += cc.Codec.GZip.cpdist[u];
					for (u = 0; u < i; u++) {
						var t = this.buf32k[this.bIdx - a & 32767];
						this.addBuffer(t);
					}
				}
			}
		} else if (n == 2) {
			var u, f, l, c, h, p = new Array(320);
			l = 257 + this.readBits(5), c = 1 + this.readBits(5), h = 4 + this.readBits(4);
			for (u = 0; u < 19; u++) p[u] = 0;
			for (u = 0; u < h; u++) p[cc.Codec.GZip.border[u]] = this.readBits(3);
			i = this.distanceTree.length;
			for (r = 0; r < i; r++) this.distanceTree[r] = new cc.Codec.GZip.HufNode();
			if (this.CreateTree(this.distanceTree, 19, p, 0)) return this.flushBuffer(), 1;
			f = l + c, r = 0;
			var d = -1;
			while (r < f) {
				d++, u = this.DecodeValue(this.distanceTree);
				if (u < 16) p[r++] = u;
				else if (u == 16) {
					var v;
					u = 3 + this.readBits(2);
					if (r + u > f) return this.flushBuffer(), 1;
					v = r ? p[r - 1] : 0;
					while (u--) p[r++] = v;
				} else {
					u == 17 ? u = 3 + this.readBits(3) : u = 11 + this.readBits(7);
					if (r + u > f) return this.flushBuffer(), 1;
					while (u--) p[r++] = 0;
				}
			}
			i = this.literalTree.length;
			for (r = 0; r < i; r++) this.literalTree[r] = new cc.Codec.GZip.HufNode();
			if (this.CreateTree(this.literalTree, l, p, 0)) return this.flushBuffer(), 1;
			i = this.literalTree.length;
			for (r = 0; r < i; r++) this.distanceTree[r] = new cc.Codec.GZip.HufNode();
			var m = new Array();
			for (r = l; r < p.length; r++) m[r - l] = p[r];
			if (this.CreateTree(this.distanceTree, c, m, 0)) return this.flushBuffer(), 1;
			for (;;) {
				u = this.DecodeValue(this.literalTree);
				if (u >= 256) {
					var i, a;
					u -= 256;
					if (u == 0) break;
					u--, i = this.readBits(cc.Codec.GZip.cplext[u]) + cc.Codec.GZip.cplens[u], u = this.DecodeValue(this.distanceTree), cc.Codec.GZip.cpdext[u] > 8 ? (a = this.readBits(8), a |= this.readBits(cc.Codec.GZip.cpdext[u] - 8) << 8) : a = this.readBits(cc.Codec.GZip.cpdext[u]), a += cc.Codec.GZip.cpdist[u];
					while (i--) {
						var t = this.buf32k[this.bIdx - a & 32767];
						this.addBuffer(t);
					}
				} else this.addBuffer(u);
			}
		}
	} while (!e);
	return this.flushBuffer(), this.byteAlign(), 0;
}, cc.Codec.GZip.prototype.unzipFile = function(e) {
	var t;
	this.gunzip();
	for (t = 0; t < this.unzipped.length; t++) if (this.unzipped[t][1] == e) return this.unzipped[t][0];
}, cc.Codec.GZip.prototype.nextFile = function() {
	this.outputArr = [], this.modeZIP = !1;
	var e = [];
	e[0] = this.readByte(), e[1] = this.readByte(), e[0] == 120 && e[1] == 218 && (this.DeflateLoop(), this.unzipped[this.files] = [this.outputArr.join(""), "geonext.gxt"], this.files++), e[0] == 31 && e[1] == 139 && (this.skipdir(), this.unzipped[this.files] = [this.outputArr.join(""), "file"], this.files++);
	if (e[0] == 80 && e[1] == 75) {
		this.modeZIP = !0, e[2] = this.readByte(), e[3] = this.readByte();
		if (e[2] == 3 && e[3] == 4) {
			e[0] = this.readByte(), e[1] = this.readByte(), this.gpflags = this.readByte(), this.gpflags |= this.readByte() << 8;
			var t = this.readByte();
			t |= this.readByte() << 8, this.readByte(), this.readByte(), this.readByte(), this.readByte();
			var n = this.readByte();
			n |= this.readByte() << 8, n |= this.readByte() << 16, n |= this.readByte() << 24;
			var r = this.readByte();
			r |= this.readByte() << 8, r |= this.readByte() << 16, r |= this.readByte() << 24;
			var i = this.readByte();
			i |= this.readByte() << 8;
			var s = this.readByte();
			s |= this.readByte() << 8, u = 0, this.nameBuf = [];
			while (i--) {
				var o = this.readByte();
				o == "/" | o == ":" ? u = 0 : u < cc.Codec.GZip.NAMEMAX - 1 && (this.nameBuf[u++] = String.fromCharCode(o));
			}
			this.fileout || (this.fileout = this.nameBuf);
			var u = 0;
			while (u < s) o = this.readByte(), u++;
			t == 8 && (this.DeflateLoop(), this.unzipped[this.files] = [this.outputArr.join(""), this.nameBuf.join("")], this.files++), this.skipdir();
		}
	}
}, cc.Codec.GZip.prototype.skipdir = function() {
	var e = [],
		t, n, r, i, s;
	this.gpflags & 8 && (e[0] = this.readByte(), e[1] = this.readByte(), e[2] = this.readByte(), e[3] = this.readByte(), t = this.readByte(), t |= this.readByte() << 8, t |= this.readByte() << 16, t |= this.readByte() << 24, n = this.readByte(), n |= this.readByte() << 8, n |= this.readByte() << 16, n |= this.readByte() << 24), this.modeZIP && this.nextFile(), e[0] = this.readByte();
	if (e[0] != 8) return 0;
	this.gpflags = this.readByte(), this.readByte(), this.readByte(), this.readByte(), this.readByte(), this.readByte(), r = this.readByte();
	if (this.gpflags & 4) {
		e[0] = this.readByte(), e[2] = this.readByte(), this.len = e[0] + 256 * e[1];
		for (i = 0; i < this.len; i++) this.readByte();
	}
	if (this.gpflags & 8) {
		i = 0, this.nameBuf = [];
		while (s = this.readByte()) {
			if (s == "7" || s == ":") i = 0;
			i < cc.Codec.GZip.NAMEMAX - 1 && (this.nameBuf[i++] = s);
		}
	}
	if (this.gpflags & 16) while (s = this.readByte());
	this.gpflags & 2 && (this.readByte(), this.readByte()), this.DeflateLoop(), n = this.readByte(), n |= this.readByte() << 8, n |= this.readByte() << 16, n |= this.readByte() << 24, this.modeZIP && this.nextFile();
};
cc.INVALID_INDEX = -1, cc.PI = Math.PI, cc.FLT_MAX = parseFloat("3.402823466e+38F"), cc.RAD = cc.PI / 180, cc.DEG = 180 / cc.PI, cc.UINT_MAX = 4294967295, cc.SWAP = function(e, t, n) {
	if (typeof n == "object" && typeof n.x != "undefined" && typeof n.y != "undefined") {
		var r = n[e];
		n[e] = n[t], n[t] = r;
	} else cc.Assert(!1, "CC_SWAP is being modified from original macro, please check usage");
}, cc.lerp = function(e, t, n) {
	return e + (t - e) * n;
}, cc.RANDOM_MINUS1_1 = function() {
	return (Math.random() - .5) * 2;
}, cc.RANDOM_0_1 = function() {
	return Math.random();
}, cc.DEGREES_TO_RADIANS = function(e) {
	return e * cc.RAD;
}, cc.RADIANS_TO_DEGREES = function(e) {
	return e * cc.DEG;
}, cc.REPEAT_FOREVER = Number.MAX_VALUE - 1, cc.BLEND_SRC = cc.OPTIMIZE_BLEND_FUNC_FOR_PREMULTIPLIED_ALPHA ? 1 : 770, cc.BLEND_DST = 771, cc.NODE_DRAW_SETUP = function(e) {
	ccGLEnable(e._glServerState), cc.Assert(e.getShaderProgram(), "No shader program set for this node"), e.getShaderProgram().use(), e.getShaderProgram().setUniformForModelViewProjectionMatrix();
}, cc.ENABLE_DEFAULT_GL_STATES = function() {}, cc.DISABLE_DEFAULT_GL_STATES = function() {}, cc.INCREMENT_GL_DRAWS = function(e) {
	cc.g_NumberOfDraws += e;
}, cc.FLT_EPSILON = 1.192092896e-7, cc.CONTENT_SCALE_FACTOR = cc.IS_RETINA_DISPLAY_SUPPORTED ?
function() {
	return cc.Director.getInstance().getContentScaleFactor();
} : function() {
	return 1;
}, cc.POINT_POINTS_TO_PIXELS = function(e) {
	return cc.p(e.x * cc.CONTENT_SCALE_FACTOR(), e.y * cc.CONTENT_SCALE_FACTOR());
}, cc.SIZE_POINTS_TO_PIXELS = function(e) {
	return cc.size(e.width * cc.CONTENT_SCALE_FACTOR(), e.height * cc.CONTENT_SCALE_FACTOR());
}, cc.SIZE_PIXELS_TO_POINTS = function(e) {
	return cc.size(e.width / cc.CONTENT_SCALE_FACTOR(), e.height / cc.CONTENT_SCALE_FACTOR());
}, cc.POINT_PIXELS_TO_POINTS = function(e) {
	return cc.p(e.x / cc.CONTENT_SCALE_FACTOR(), e.y / cc.CONTENT_SCALE_FACTOR());
}, cc.RECT_PIXELS_TO_POINTS = cc.IS_RETINA_DISPLAY_SUPPORTED ?
function(e) {
	return cc.rect(e.origin.x / cc.CONTENT_SCALE_FACTOR(), e.origin.y / cc.CONTENT_SCALE_FACTOR(), e.size.width / cc.CONTENT_SCALE_FACTOR(), e.size.height / cc.CONTENT_SCALE_FACTOR());
} : function(e) {
	return e;
}, cc.RECT_POINTS_TO_PIXELS = cc.IS_RETINA_DISPLAY_SUPPORTED ?
function(e) {
	return cc.rect(e.origin.x * cc.CONTENT_SCALE_FACTOR(), e.origin.y * cc.CONTENT_SCALE_FACTOR(), e.size.width * cc.CONTENT_SCALE_FACTOR(), e.size.height * cc.CONTENT_SCALE_FACTOR());
} : function(e) {
	return e;
};
var gl = gl || {};
gl.NEAREST = 9728, gl.LINEAR = 9729, gl.REPEAT = 10497, gl.CLAMP_TO_EDGE = 33071, gl.CLAMP_TO_BORDER = 33069, gl.LINEAR_MIPMAP_NEAREST = 9985, gl.NEAREST_MIPMAP_NEAREST = 9984, gl.ZERO = 0, gl.ONE = 1, gl.SRC_COLOR = 768, gl.ONE_MINUS_SRC_COLOR = 769, gl.SRC_ALPHA = 770, gl.ONE_MINUS_SRC_ALPHA = 771, gl.DST_ALPHA = 772, gl.ONE_MINUS_DST_ALPHA = 773, gl.DST_COLOR = 774, gl.ONE_MINUS_DST_COLOR = 775, gl.SRC_ALPHA_SATURATE = 776;
cc.SAX_NONE = 0, cc.SAX_KEY = 1, cc.SAX_DICT = 2, cc.SAX_INT = 3, cc.SAX_REAL = 4, cc.SAX_STRING = 5, cc.SAX_ARRAY = 6;
var Uint8Array = Uint8Array || Array;
if (/msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent)) {
	var IEBinaryToArray_ByteStr_Script = '<!-- IEBinaryToArray_ByteStr -->\r\nFunction IEBinaryToArray_ByteStr(Binary)\r\n   IEBinaryToArray_ByteStr = CStr(Binary)\r\nEnd Function\r\nFunction IEBinaryToArray_ByteStr_Last(Binary)\r\n   Dim lastIndex\r\n   lastIndex = LenB(Binary)\r\n   if lastIndex mod 2 Then\r\n       IEBinaryToArray_ByteStr_Last = Chr( AscB( MidB( Binary, lastIndex, 1 ) ) )\r\n   Else\r\n       IEBinaryToArray_ByteStr_Last = ""\r\n   End If\r\nEnd Function\r\n',
		myVBScript = document.createElement("script");
	myVBScript.type = "text/vbscript", myVBScript.textContent = IEBinaryToArray_ByteStr_Script, document.body.appendChild(myVBScript), cc._convertResponseBodyToText = function(e) {
		var t = {};
		for (var n = 0; n < 256; n++) for (var r = 0; r < 256; r++) t[String.fromCharCode(n + r * 256)] = String.fromCharCode(n) + String.fromCharCode(r);
		var i = IEBinaryToArray_ByteStr(e),
			s = IEBinaryToArray_ByteStr_Last(e);
		return i.replace(/[\s\S]/g, function(e) {
			return t[e];
		}) + s;
	};
}
cc.FileUtils = cc.Class.extend({
	_fileDataCache: null,
	_directory: null,
	_filenameLookupDict: null,
	_searchResolutionsOrderArray: null,
	_searchPathArray: null,
	ctor: function() {
		this._fileDataCache = {}, this._searchPathArray = [], this._searchPathArray.push(""), this._searchResolutionsOrderArray = [], this._searchResolutionsOrderArray.push("");
	},
	getFileData: function(e, t, n) {
		return this._fileDataCache.hasOwnProperty(e) ? this._fileDataCache[e] : this._loadBinaryFileData(e);
	},
	_getXMLHttpRequest: function() {
		return window.XMLHttpRequest ? new window.XMLHttpRequest() : new ActiveXObject("MSXML2.XMLHTTP");
	},
	preloadBinaryFileData: function(e) {
		e = this.fullPathFromRelativePath(e);
		var t = this,
			n = this._getXMLHttpRequest();
		n.open("GET", e, !0), /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent) ? (n.setRequestHeader("Accept-Charset", "x-user-defined"), n.onreadystatechange = function(r) {
			if (n.readyState == 4) {
				if (n.status == 200) {
					var i = cc._convertResponseBodyToText(n.responseBody);
					i && (t._fileDataCache[e] = t._stringConvertToArray(i));
				}
				cc.Loader.getInstance().onResLoaded();
			}
		}) : (n.overrideMimeType && n.overrideMimeType("text/plain; charset=x-user-defined"), n.onload = function(r) {
			var i = n.responseText;
			i && (cc.Loader.getInstance().onResLoaded(), t._fileDataCache[e] = t._stringConvertToArray(i));
		}), n.send(null);
	},
	_loadBinaryFileData: function(e) {
		var t = this._getXMLHttpRequest();
		t.open("GET", e, !1);
		var n = null;
		if (/msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent)) {
			t.setRequestHeader("Accept-Charset", "x-user-defined"), t.send(null);
			if (t.status != 200) return null;
			var r = cc._convertResponseBodyToText(t.responseBody);
			r && (n = this._stringConvertToArray(t.responseText), this._fileDataCache[e] = n);
		} else {
			t.overrideMimeType && t.overrideMimeType("text/plain; charset=x-user-defined"), t.send(null);
			if (t.status != 200) return null;
			n = this._stringConvertToArray(t.responseText), this._fileDataCache[e] = n;
		}
		return n;
	},
	_stringConvertToArray: function(e) {
		if (!e) return null;
		var t = new Uint8Array(e.length);
		for (var n = 0; n < e.length; n++) t[n] = e.charCodeAt(n) & 255;
		return t;
	},
	getFileDataFromZip: function(e, t, n) {},
	removeSuffixFromFile: function(e) {},
	popupNotify: !0,
	fullPathFromRelativePath: function(e) {
		return e;
	},
	fullPathForFilename: function(e) {
		var t = !1,
			n = this._getNewFilename(e),
			r;
		if (n && n.length > 1 && n.indexOf(":") == 1) return n;
		for (var i = 0; i < this._searchPathArray.length; i++) {
			var s = this._searchPathArray[i];
			for (var o = 0; o < this._searchResolutionsOrderArray.length; o++) {
				var u = this._searchResolutionsOrderArray[o];
				r = this._getPathForFilename(n, u, s);
				if (r) {
					t = !0;
					break;
				}
			}
			if (t) break;
		}
		return t ? r : n;
	},
	loadFilenameLookup: function(e) {
		var t = this.fullPathForFilename(e);
		if (t.length > 0) {
			var n = cc.SAXParser.getInstance().parse(t),
				r = n.metadata,
				i = parseInt(r.version);
			if (i != 1) {
				cc.log("cocos2d: ERROR: Invalid filenameLookup dictionary version: " + i + ". Filename: " + e);
				return;
			}
			this.setFilenameLookupDictionary(n.filenames);
		}
	},
	setFilenameLookupDictionary: function(e) {
		this._filenameLookupDict = e;
	},
	fullPathFromRelativeFile: function(e, t) {
		var n;
		return e ? (n = t.substring(0, t.lastIndexOf("/") + 1), n + e) : (n = t.substring(0, t.lastIndexOf(".")), n += ".png", n);
	},
	setSearchResolutionsOrder: function(e) {
		this._searchResolutionsOrderArray = e;
	},
	getSearchResolutionsOrder: function() {
		return this._searchResolutionsOrderArray;
	},
	setSearchPath: function(e) {
		this._searchPathArray = e;
	},
	getSearchPath: function() {
		return this._searchPathArray;
	},
	getResourceDirectory: function() {
		return this._directory;
	},
	setResourcePath: function(e) {},
	dictionaryWithContentsOfFile: function(e) {
		var t = cc.SAXParser.getInstance();
		return this.rootDict = t.parse(e), this.rootDict;
	},
	dictionaryWithContentsOfFileThreadSafe: function(e) {
		var t = new cc.DictMaker();
		return t.dictionaryWithContentsOfFile(e);
	},
	getWriteablePath: function() {
		return "";
	},
	setPopupNotify: function(e) {
		cc.popupNotify = e;
	},
	isPopupNotify: function() {
		return cc.popupNotify;
	},
	_resourceRootPath: "",
	getResourceRootPath: function() {
		return this._resourceRootPath;
	},
	setResourceRootPath: function(e) {
		this._resourceRootPath = e;
	},
	_getNewFilename: function(e) {
		if (e == "res/ffff.png") {} else {
			e = resourceDomain + e;
		}
		var t = null,
			n = this._filenameLookupDict ? this._filenameLookupDict[e] : null;
		return !n || n.length === 0 ? t = e : (t = n, cc.log("FOUND NEW FILE NAME: %s", t)), t;
	},
	_getPathForFilename: function(e, t, n) {
		var r, i = this.getResourceRootPath();
		if (!(e && e.length > 0) || e.indexOf("/") !== 0 && e.indexOf("\\") !== 0) i.length > 0 ? (r = i, r[r.length - 1] != "\\" && r[r.length - 1] != "/" && (r += "/")) : r = i;
		if (e == null || e == "") return;
		var s = e,
			o = "",
			u = e.lastIndexOf("/");
		u != -1 && (o = e.substr(0, u + 1), s = e.substr(u + 1));
		var a = n;
		return a.length > 0 && a.indexOf("/") !== a.length - 1 && (a += "/"), a += o, a += t, a.length > 0 && a.indexOf("/") !== a.length - 1 && (a += "/"), a += s, r += a, r;
	}
}), cc.s_SharedFileUtils = null, cc.FileUtils.getInstance = function() {
	return cc.s_SharedFileUtils == null && (cc.s_SharedFileUtils = new cc.FileUtils()), cc.s_SharedFileUtils;
}, cc.DictMaker = cc.Class.extend({
	rootDict: [],
	dictionaryWithContentsOfFile: function(e) {
		var t = cc.SAXParser.getInstance();
		return this.rootDict = t.parse(e), this.rootDict;
	}
});
cc.Color3B = function(e, t, n) {
	switch (arguments.length) {
	case 0:
		this.r = 0, this.g = 0, this.b = 0;
		break;
	case 1:
		e && e instanceof cc.Color3B ? (this.r = 0 | e.r || 0, this.g = 0 | e.g || 0, this.b = 0 | e.b || 0) : (this.r = 0, this.g = 0, this.b = 0);
		break;
	case 3:
		this.r = 0 | e || 0, this.g = 0 | t || 0, this.b = 0 | n || 0;
		break;
	default:
		throw "unknown argument type";
	}
}, cc.c3b = function(e, t, n) {
	return new cc.Color3B(e, t, n);
}, cc.integerToColor3B = function(e) {
	e = e || 0;
	var t = 255,
		n = new cc.Color3B();
	return n.r = e & t, n.g = e >> 8 & t, n.b = e >> 16 & t, n;
}, cc.c3 = cc.c3b, Object.defineProperties(cc, {
	WHITE: {
		get: function() {
			return cc.c3b(255, 255, 255);
		}
	},
	YELLOW: {
		get: function() {
			return cc.c3b(255, 255, 0);
		}
	},
	BLUE: {
		get: function() {
			return cc.c3b(0, 0, 255);
		}
	},
	GREEN: {
		get: function() {
			return cc.c3b(0, 255, 0);
		}
	},
	RED: {
		get: function() {
			return cc.c3b(255, 0, 0);
		}
	},
	MAGENTA: {
		get: function() {
			return cc.c3b(255, 0, 255);
		}
	},
	BLACK: {
		get: function() {
			return cc.c3b(0, 0, 0);
		}
	},
	ORANGE: {
		get: function() {
			return cc.c3b(255, 127, 0);
		}
	},
	GRAY: {
		get: function() {
			return cc.c3b(166, 166, 166);
		}
	}
}), cc.white = function() {
	return new cc.Color3B(255, 255, 255);
}, cc.yellow = function() {
	return new cc.Color3B(255, 255, 0);
}, cc.blue = function() {
	return new cc.Color3B(0, 0, 255);
}, cc.green = function() {
	return new cc.Color3B(0, 255, 0);
}, cc.red = function() {
	return new cc.Color3B(255, 0, 0);
}, cc.magenta = function() {
	return new cc.Color3B(255, 0, 255);
}, cc.black = function() {
	return new cc.Color3B(0, 0, 0);
}, cc.orange = function() {
	return new cc.Color3B(255, 127, 0);
}, cc.gray = function() {
	return new cc.Color3B(166, 166, 166);
}, cc.Color4B = function(e, t, n, r) {
	this.r = 0 | e, this.g = 0 | t, this.b = 0 | n, this.a = 0 | r;
}, cc.c4b = function(e, t, n, r) {
	return new cc.Color4B(e, t, n, r);
}, cc.c4 = cc.c4b, cc.Color4F = function(e, t, n, r) {
	this.r = e, this.g = t, this.b = n, this.a = r;
}, cc.c4f = function(e, t, n, r) {
	return new cc.Color4F(e, t, n, r);
}, cc.c4FFromccc3B = function(e) {
	return new cc.Color4F(e.r / 255, e.g / 255, e.b / 255, 1);
}, cc.c4FFromccc4B = function(e) {
	return new cc.Color4F(e.r / 255, e.g / 255, e.b / 255, e.a / 255);
}, cc.c4FEqual = function(e, t) {
	return e.r == t.r && e.g == t.g && e.b == t.b && e.a == t.a;
}, cc.Vertex2F = function(e, t) {
	this.x = e || 0, this.y = t || 0;
}, cc.Vertex2 = function(e, t) {
	return new cc.Vertex2F(e, t);
}, cc.Vertex3F = function(e, t, n) {
	this.x = e || 0, this.y = t || 0, this.z = n || 0;
}, cc.vertex3 = function(e, t, n) {
	return new cc.Vertex3F(e, t, n);
}, cc.Tex2F = function(e, t) {
	this.u = e || 0, this.v = t || 0;
}, cc.tex2 = function(e, t) {
	return new cc.Tex2F(e, t);
}, cc.PointSprite = function(e, t, n) {
	this.pos = e || new cc.Vertex2F(0, 0), this.color = t || new cc.Color4B(0, 0, 0, 0), this.size = n || 0;
}, cc.Quad2 = function(e, t, n, r) {
	this.tl = e || new cc.Vertex2F(0, 0), this.tr = t || new cc.Vertex2F(0, 0), this.bl = n || new cc.Vertex2F(0, 0), this.br = r || new cc.Vertex2F(0, 0);
}, cc.Quad3 = function(e, t, n, r) {
	this.bl = e || new cc.Vertex3F(0, 0, 0), this.br = t || new cc.Vertex3F(0, 0, 0), this.tl = n || new cc.Vertex3F(0, 0, 0), this.tr = r || new cc.Vertex3F(0, 0, 0);
}, cc.GridSize = function(e, t) {
	this.x = e, this.y = t;
}, cc.g = function(e, t) {
	return new cc.GridSize(e, t);
}, cc.V2F_C4B_T2F = function(e, t, n) {
	this.vertices = e || new cc.Vertex2F(0, 0), this.colors = t || new cc.Color4B(0, 0, 0, 0), this.texCoords = n || new cc.Tex2F(0, 0);
}, cc.V2F_C4F_T2F = function(e, t, n) {
	this.vertices = e || new cc.Vertex2F(0, 0), this.colors = t || new cc.Color4F(0, 0, 0, 0), this.texCoords = n || new cc.Tex2F(0, 0);
}, cc.V3F_C4B_T2F = function(e, t, n) {
	this.vertices = e || new cc.Vertex3F(0, 0, 0), this.colors = t || new cc.Color4B(0, 0, 0, 0), this.texCoords = n || new cc.Tex2F(0, 0);
}, cc.V2F_C4B_T2F_Quad = function(e, t, n, r) {
	this.bl = e || new cc.V2F_C4B_T2F(), this.br = t || new cc.V2F_C4B_T2F(), this.tl = n || new cc.V2F_C4B_T2F(), this.tr = r || new cc.V2F_C4B_T2F();
}, cc.V2F_C4B_T2F_QuadZero = function() {
	return new cc.V2F_C4B_T2F_Quad(new cc.V2F_C4B_T2F(new cc.Vertex2F(0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)), new cc.V2F_C4B_T2F(new cc.Vertex2F(0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)), new cc.V2F_C4B_T2F(new cc.Vertex2F(0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)), new cc.V2F_C4B_T2F(new cc.Vertex2F(0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)));
}, cc.V3F_C4B_T2F_Quad = function(e, t, n, r) {
	this.tl = e || new cc.V3F_C4B_T2F(), this.bl = t || new cc.V3F_C4B_T2F(), this.tr = n || new cc.V3F_C4B_T2F(), this.br = r || new cc.V3F_C4B_T2F();
}, cc.V3F_C4B_T2F_QuadZero = function() {
	return new cc.V3F_C4B_T2F_Quad(new cc.V3F_C4B_T2F(new cc.Vertex3F(0, 0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)), new cc.V3F_C4B_T2F(new cc.Vertex3F(0, 0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)), new cc.V3F_C4B_T2F(new cc.Vertex3F(0, 0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)), new cc.V3F_C4B_T2F(new cc.Vertex3F(0, 0, 0), new cc.Color4B(0, 0, 0, 255), new cc.Tex2F(0, 0)));
}, cc.V2F_C4F_T2F_Quad = function(e, t, n, r) {
	this.bl = e || new cc.V2F_C4F_T2F(), this.br = t || new cc.V2F_C4F_T2F(), this.tl = n || new cc.V2F_C4F_T2F(), this.tr = r || new cc.V2F_C4F_T2F();
}, cc.BlendFunc = function(e, t) {
	this.src = e, this.dst = t;
}, cc.convertColor3BtoHexString = function(e) {
	var t = e.r.toString(16),
		n = e.g.toString(16),
		r = e.b.toString(16),
		i = "#" + (e.r < 16 ? "0" + t : t) + (e.g < 16 ? "0" + n : n) + (e.b < 16 ? "0" + r : r);
	return i;
}, cc.convertHexNumToColor3B = function(e) {
	var t = e.substr(1).split(""),
		n = parseInt("0x" + t[0] + t[1]),
		r = parseInt("0x" + t[2] + t[3]),
		i = parseInt("0x" + t[4] + t[5]);
	return new cc.Color3B(n, r, i);
}, cc.TEXT_ALIGNMENT_LEFT = 0, cc.TEXT_ALIGNMENT_CENTER = 1, cc.TEXT_ALIGNMENT_RIGHT = 2, cc.VERTICAL_TEXT_ALIGNMENT_TOP = 0, cc.VERTICAL_TEXT_ALIGNMENT_CENTER = 1, cc.VERTICAL_TEXT_ALIGNMENT_BOTTOM = 2;
(function() {
	"use strict";

	function i(a) {
		throw a;
	}
	var r = void 0,
		v = !0,
		aa = this;

	function y(a, c) {
		var b = a.split("."),
			e = aa;
		!(b[0] in e) && e.execScript && e.execScript("var " + b[0]);
		for (var f; b.length && (f = b.shift());)!b.length && c !== r ? e[f] = c : e = e[f] ? e[f] : e[f] = {};
	}
	var H = "undefined" !== typeof Uint8Array && "undefined" !== typeof Uint16Array && "undefined" !== typeof Uint32Array;

	function ba(a) {
		if ("string" === typeof a) {
			var c = a.split(""),
				b, e;
			b = 0;
			for (e = c.length; b < e; b++) c[b] = (c[b].charCodeAt(0) & 255) >>> 0;
			a = c;
		}
		for (var f = 1, d = 0, g = a.length, h, m = 0; 0 < g;) {
			h = 1024 < g ? 1024 : g;
			g -= h;
			do f += a[m++], d += f;
			while (--h);
			f %= 65521;
			d %= 65521;
		}
		return (d << 16 | f) >>> 0;
	}

	function J(a, c) {
		this.index = "number" === typeof c ? c : 0;
		this.i = 0;
		this.buffer = a instanceof(H ? Uint8Array : Array) ? a : new(H ? Uint8Array : Array)(32768);
		2 * this.buffer.length <= this.index && i(Error("invalid index"));
		this.buffer.length <= this.index && this.f();
	}
	J.prototype.f = function() {
		var a = this.buffer,
			c, b = a.length,
			e = new(H ? Uint8Array : Array)(b << 1);
		if (H) e.set(a);
		else for (c = 0; c < b; ++c) e[c] = a[c];
		return this.buffer = e;
	};
	J.prototype.d = function(a, c, b) {
		var e = this.buffer,
			f = this.index,
			d = this.i,
			g = e[f],
			h;
		b && 1 < c && (a = 8 < c ? (N[a & 255] << 24 | N[a >>> 8 & 255] << 16 | N[a >>> 16 & 255] << 8 | N[a >>> 24 & 255]) >> 32 - c : N[a] >> 8 - c);
		if (8 > c + d) g = g << c | a, d += c;
		else for (h = 0; h < c; ++h) g = g << 1 | a >> c - h - 1 & 1, 8 === ++d && (d = 0, e[f++] = N[g], g = 0, f === e.length && (e = this.f()));
		e[f] = g;
		this.buffer = e;
		this.i = d;
		this.index = f;
	};
	J.prototype.finish = function() {
		var a = this.buffer,
			c = this.index,
			b;
		0 < this.i && (a[c] <<= 8 - this.i, a[c] = N[a[c]], c++);
		H ? b = a.subarray(0, c) : (a.length = c, b = a);
		return b;
	};
	var ca = new(H ? Uint8Array : Array)(256),
		ha;
	for (ha = 0; 256 > ha; ++ha) {
		for (var R = ha, ia = R, ja = 7, R = R >>> 1; R; R >>>= 1) ia <<= 1, ia |= R & 1, --ja;
		ca[ha] = (ia << ja & 255) >>> 0;
	}
	var N = ca;
	var ka = [0, 1996959894, 3993919788, 2567524794, 124634137, 1886057615, 3915621685, 2657392035, 249268274, 2044508324, 3772115230, 2547177864, 162941995, 2125561021, 3887607047, 2428444049, 498536548, 1789927666, 4089016648, 2227061214, 450548861, 1843258603, 4107580753, 2211677639, 325883990, 1684777152, 4251122042, 2321926636, 335633487, 1661365465, 4195302755, 2366115317, 997073096, 1281953886, 3579855332, 2724688242, 1006888145, 1258607687, 3524101629, 2768942443, 901097722, 1119000684, 3686517206, 2898065728, 853044451, 1172266101, 3705015759, 2882616665, 651767980, 1373503546, 3369554304, 3218104598, 565507253, 1454621731, 3485111705, 3099436303, 671266974, 1594198024, 3322730930, 2970347812, 795835527, 1483230225, 3244367275, 3060149565, 1994146192, 31158534, 2563907772, 4023717930, 1907459465, 112637215, 2680153253, 3904427059, 2013776290, 251722036, 2517215374, 3775830040, 2137656763, 141376813, 2439277719, 3865271297, 1802195444, 476864866, 2238001368, 4066508878, 1812370925, 453092731, 2181625025, 4111451223, 1706088902, 314042704, 2344532202, 4240017532, 1658658271, 366619977, 2362670323, 4224994405, 1303535960, 984961486, 2747007092, 3569037538, 1256170817, 1037604311, 2765210733, 3554079995, 1131014506, 879679996, 2909243462, 3663771856, 1141124467, 855842277, 2852801631, 3708648649, 1342533948, 654459306, 3188396048, 3373015174, 1466479909, 544179635, 3110523913, 3462522015, 1591671054, 702138776, 2966460450, 3352799412, 1504918807, 783551873, 3082640443, 3233442989, 3988292384, 2596254646, 62317068, 1957810842, 3939845945, 2647816111, 81470997, 1943803523, 3814918930, 2489596804, 225274430, 2053790376, 3826175755, 2466906013, 167816743, 2097651377, 4027552580, 2265490386, 503444072, 1762050814, 4150417245, 2154129355, 426522225, 1852507879, 4275313526, 2312317920, 282753626, 1742555852, 4189708143, 2394877945, 397917763, 1622183637, 3604390888, 2714866558, 953729732, 1340076626, 3518719985, 2797360999, 1068828381, 1219638859, 3624741850, 2936675148, 906185462, 1090812512, 3747672003, 2825379669, 829329135, 1181335161, 3412177804, 3160834842, 628085408, 1382605366, 3423369109, 3138078467, 570562233, 1426400815, 3317316542, 2998733608, 733239954, 1555261956, 3268935591, 3050360625, 752459403, 1541320221, 2607071920, 3965973030, 1969922972, 40735498, 2617837225, 3943577151, 1913087877, 83908371, 2512341634, 3803740692, 2075208622, 213261112, 2463272603, 3855990285, 2094854071, 198958881, 2262029012, 4057260610, 1759359992, 534414190, 2176718541, 4139329115, 1873836001, 414664567, 2282248934, 4279200368, 1711684554, 285281116, 2405801727, 4167216745, 1634467795, 376229701, 2685067896, 3608007406, 1308918612, 956543938, 2808555105, 3495958263, 1231636301, 1047427035, 2932959818, 3654703836, 1088359270, 936918e3, 2847714899, 3736837829, 1202900863, 817233897, 3183342108, 3401237130, 1404277552, 615818150, 3134207493, 3453421203, 1423857449, 601450431, 3009837614, 3294710456, 1567103746, 711928724, 3020668471, 3272380065, 1510334235, 755167117];
	H && new Uint32Array(ka);

	function la(a) {
		this.buffer = new(H ? Uint16Array : Array)(2 * a);
		this.length = 0;
	}
	la.prototype.getParent = function(a) {
		return 2 * ((a - 2) / 4 | 0);
	};
	la.prototype.push = function(a, c) {
		var b, e, f = this.buffer,
			d;
		b = this.length;
		f[this.length++] = c;
		for (f[this.length++] = a; 0 < b;) if (e = this.getParent(b), f[b] > f[e]) d = f[b], f[b] = f[e], f[e] = d, d = f[b + 1], f[b + 1] = f[e + 1], f[e + 1] = d, b = e;
		else break;
		return this.length;
	};
	la.prototype.pop = function() {
		var a, c, b = this.buffer,
			e, f, d;
		c = b[0];
		a = b[1];
		this.length -= 2;
		b[0] = b[this.length];
		b[1] = b[this.length + 1];
		for (d = 0;;) {
			f = 2 * d + 2;
			if (f >= this.length) break;
			f + 2 < this.length && b[f + 2] > b[f] && (f += 2);
			if (b[f] > b[d]) e = b[d], b[d] = b[f], b[f] = e, e = b[d + 1], b[d + 1] = b[f + 1], b[f + 1] = e;
			else break;
			d = f;
		}
		return {
			index: a,
			value: c,
			length: this.length
		};
	};

	function S(a) {
		var c = a.length,
			b = 0,
			e = Number.POSITIVE_INFINITY,
			f, d, g, h, m, j, s, n, l;
		for (n = 0; n < c; ++n) a[n] > b && (b = a[n]), a[n] < e && (e = a[n]);
		f = 1 << b;
		d = new(H ? Uint32Array : Array)(f);
		g = 1;
		h = 0;
		for (m = 2; g <= b;) {
			for (n = 0; n < c; ++n) if (a[n] === g) {
				j = 0;
				s = h;
				for (l = 0; l < g; ++l) j = j << 1 | s & 1, s >>= 1;
				for (l = j; l < f; l += m) d[l] = g << 16 | n;
				++h;
			}++g;
			h <<= 1;
			m <<= 1;
		}
		return [d, b, e];
	}

	function ma(a, c) {
		this.h = pa;
		this.w = 0;
		this.input = a;
		this.b = 0;
		c && (c.lazy && (this.w = c.lazy), "number" === typeof c.compressionType && (this.h = c.compressionType), c.outputBuffer && (this.a = H && c.outputBuffer instanceof Array ? new Uint8Array(c.outputBuffer) : c.outputBuffer), "number" === typeof c.outputIndex && (this.b = c.outputIndex));
		this.a || (this.a = new(H ? Uint8Array : Array)(32768));
	}
	var pa = 2,
		qa = {
			NONE: 0,
			r: 1,
			j: pa,
			N: 3
		},
		ra = [],
		T;
	for (T = 0; 288 > T; T++) switch (v) {
	case 143 >= T:
		ra.push([T + 48, 8]);
		break;
	case 255 >= T:
		ra.push([T - 144 + 400, 9]);
		break;
	case 279 >= T:
		ra.push([T - 256 + 0, 7]);
		break;
	case 287 >= T:
		ra.push([T - 280 + 192, 8]);
		break;
	default:
		i("invalid literal: " + T);
	}
	ma.prototype.n = function() {
		var a, c, b, e, f = this.input;
		switch (this.h) {
		case 0:
			b = 0;
			for (e = f.length; b < e;) {
				c = H ? f.subarray(b, b + 65535) : f.slice(b, b + 65535);
				b += c.length;
				var d = c,
					g = b === e,
					h = r,
					m = r,
					j = r,
					s = r,
					n = r,
					l = this.a,
					q = this.b;
				if (H) {
					for (l = new Uint8Array(this.a.buffer); l.length <= q + d.length + 5;) l = new Uint8Array(l.length << 1);
					l.set(this.a);
				}
				h = g ? 1 : 0;
				l[q++] = h | 0;
				m = d.length;
				j = ~m + 65536 & 65535;
				l[q++] = m & 255;
				l[q++] = m >>> 8 & 255;
				l[q++] = j & 255;
				l[q++] = j >>> 8 & 255;
				if (H) l.set(d, q), q += d.length, l = l.subarray(0, q);
				else {
					s = 0;
					for (n = d.length; s < n; ++s) l[q++] = d[s];
					l.length = q;
				}
				this.b = q;
				this.a = l;
			}
			break;
		case 1:
			var E = new J(new Uint8Array(this.a.buffer), this.b);
			E.d(1, 1, v);
			E.d(1, 2, v);
			var t = sa(this, f),
				z, K, A;
			z = 0;
			for (K = t.length; z < K; z++) if (A = t[z], J.prototype.d.apply(E, ra[A]), 256 < A) E.d(t[++z], t[++z], v), E.d(t[++z], 5), E.d(t[++z], t[++z], v);
			else if (256 === A) break;
			this.a = E.finish();
			this.b = this.a.length;
			break;
		case pa:
			var x = new J(new Uint8Array(this.a), this.b),
				B, k, p, D, C, da = [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15],
				W, Ma, ea, Na, na, va = Array(19),
				Oa, $, oa, F, Pa;
			B = pa;
			x.d(1, 1, v);
			x.d(B, 2, v);
			k = sa(this, f);
			W = ta(this.L, 15);
			Ma = ua(W);
			ea = ta(this.K, 7);
			Na = ua(ea);
			for (p = 286; 257 < p && 0 === W[p - 1]; p--);
			for (D = 30; 1 < D && 0 === ea[D - 1]; D--);
			var Qa = p,
				Ra = D,
				M = new(H ? Uint32Array : Array)(Qa + Ra),
				u, O, w, fa, L = new(H ? Uint32Array : Array)(316),
				I, G, P = new(H ? Uint8Array : Array)(19);
			for (u = O = 0; u < Qa; u++) M[O++] = W[u];
			for (u = 0; u < Ra; u++) M[O++] = ea[u];
			if (!H) {
				u = 0;
				for (fa = P.length; u < fa; ++u) P[u] = 0;
			}
			u = I = 0;
			for (fa = M.length; u < fa; u += O) {
				for (O = 1; u + O < fa && M[u + O] === M[u]; ++O);
				w = O;
				if (0 === M[u]) if (3 > w) for (; 0 < w--;) L[I++] = 0, P[0]++;
				else for (; 0 < w;) G = 138 > w ? w : 138, G > w - 3 && G < w && (G = w - 3), 10 >= G ? (L[I++] = 17, L[I++] = G - 3, P[17]++) : (L[I++] = 18, L[I++] = G - 11, P[18]++), w -= G;
				else if (L[I++] = M[u], P[M[u]]++, w--, 3 > w) for (; 0 < w--;) L[I++] = M[u], P[M[u]]++;
				else for (; 0 < w;) G = 6 > w ? w : 6, G > w - 3 && G < w && (G = w - 3), L[I++] = 16, L[I++] = G - 3, P[16]++, w -= G;
			}
			a = H ? L.subarray(0, I) : L.slice(0, I);
			na = ta(P, 7);
			for (F = 0; 19 > F; F++) va[F] = na[da[F]];
			for (C = 19; 4 < C && 0 === va[C - 1]; C--);
			Oa = ua(na);
			x.d(p - 257, 5, v);
			x.d(D - 1, 5, v);
			x.d(C - 4, 4, v);
			for (F = 0; F < C; F++) x.d(va[F], 3, v);
			F = 0;
			for (Pa = a.length; F < Pa; F++) if ($ = a[F], x.d(Oa[$], na[$], v), 16 <= $) {
				F++;
				switch ($) {
				case 16:
					oa = 2;
					break;
				case 17:
					oa = 3;
					break;
				case 18:
					oa = 7;
					break;
				default:
					i("invalid code: " + $);
				}
				x.d(a[F], oa, v);
			}
			var Sa = [Ma, W],
				Ta = [Na, ea],
				Q, Ua, ga, ya, Va, Wa, Xa, Ya;
			Va = Sa[0];
			Wa = Sa[1];
			Xa = Ta[0];
			Ya = Ta[1];
			Q = 0;
			for (Ua = k.length; Q < Ua; ++Q) if (ga = k[Q], x.d(Va[ga], Wa[ga], v), 256 < ga) x.d(k[++Q], k[++Q], v), ya = k[++Q], x.d(Xa[ya], Ya[ya], v), x.d(k[++Q], k[++Q], v);
			else if (256 === ga) break;
			this.a = x.finish();
			this.b = this.a.length;
			break;
		default:
			i("invalid compression type");
		}
		return this.a;
	};

	function wa(a, c) {
		this.length = a;
		this.G = c;
	}

	function xa() {
		var a = za;
		switch (v) {
		case 3 === a:
			return [257, a - 3, 0];
		case 4 === a:
			return [258, a - 4, 0];
		case 5 === a:
			return [259, a - 5, 0];
		case 6 === a:
			return [260, a - 6, 0];
		case 7 === a:
			return [261, a - 7, 0];
		case 8 === a:
			return [262, a - 8, 0];
		case 9 === a:
			return [263, a - 9, 0];
		case 10 === a:
			return [264, a - 10, 0];
		case 12 >= a:
			return [265, a - 11, 1];
		case 14 >= a:
			return [266, a - 13, 1];
		case 16 >= a:
			return [267, a - 15, 1];
		case 18 >= a:
			return [268, a - 17, 1];
		case 22 >= a:
			return [269, a - 19, 2];
		case 26 >= a:
			return [270, a - 23, 2];
		case 30 >= a:
			return [271, a - 27, 2];
		case 34 >= a:
			return [272, a - 31, 2];
		case 42 >= a:
			return [273, a - 35, 3];
		case 50 >= a:
			return [274, a - 43, 3];
		case 58 >= a:
			return [275, a - 51, 3];
		case 66 >= a:
			return [276, a - 59, 3];
		case 82 >= a:
			return [277, a - 67, 4];
		case 98 >= a:
			return [278, a - 83, 4];
		case 114 >= a:
			return [279, a - 99, 4];
		case 130 >= a:
			return [280, a - 115, 4];
		case 162 >= a:
			return [281, a - 131, 5];
		case 194 >= a:
			return [282, a - 163, 5];
		case 226 >= a:
			return [283, a - 195, 5];
		case 257 >= a:
			return [284, a - 227, 5];
		case 258 === a:
			return [285, a - 258, 0];
		default:
			i("invalid length: " + a);
		}
	}
	var Aa = [],
		za, Ba;
	for (za = 3; 258 >= za; za++) Ba = xa(), Aa[za] = Ba[2] << 24 | Ba[1] << 16 | Ba[0];
	var Ca = H ? new Uint32Array(Aa) : Aa;

	function sa(a, c) {
		function b(a, c) {
			var b = a.G,
				d = [],
				e = 0,
				f;
			f = Ca[a.length];
			d[e++] = f & 65535;
			d[e++] = f >> 16 & 255;
			d[e++] = f >> 24;
			var g;
			switch (v) {
			case 1 === b:
				g = [0, b - 1, 0];
				break;
			case 2 === b:
				g = [1, b - 2, 0];
				break;
			case 3 === b:
				g = [2, b - 3, 0];
				break;
			case 4 === b:
				g = [3, b - 4, 0];
				break;
			case 6 >= b:
				g = [4, b - 5, 1];
				break;
			case 8 >= b:
				g = [5, b - 7, 1];
				break;
			case 12 >= b:
				g = [6, b - 9, 2];
				break;
			case 16 >= b:
				g = [7, b - 13, 2];
				break;
			case 24 >= b:
				g = [8, b - 17, 3];
				break;
			case 32 >= b:
				g = [9, b - 25, 3];
				break;
			case 48 >= b:
				g = [10, b - 33, 4];
				break;
			case 64 >= b:
				g = [11, b - 49, 4];
				break;
			case 96 >= b:
				g = [12, b - 65, 5];
				break;
			case 128 >= b:
				g = [13, b - 97, 5];
				break;
			case 192 >= b:
				g = [14, b - 129, 6];
				break;
			case 256 >= b:
				g = [15, b - 193, 6];
				break;
			case 384 >= b:
				g = [16, b - 257, 7];
				break;
			case 512 >= b:
				g = [17, b - 385, 7];
				break;
			case 768 >= b:
				g = [18, b - 513, 8];
				break;
			case 1024 >= b:
				g = [19, b - 769, 8];
				break;
			case 1536 >= b:
				g = [20, b - 1025, 9];
				break;
			case 2048 >= b:
				g = [21, b - 1537, 9];
				break;
			case 3072 >= b:
				g = [22, b - 2049, 10];
				break;
			case 4096 >= b:
				g = [23, b - 3073, 10];
				break;
			case 6144 >= b:
				g = [24, b - 4097, 11];
				break;
			case 8192 >= b:
				g = [25, b - 6145, 11];
				break;
			case 12288 >= b:
				g = [26, b - 8193, 12];
				break;
			case 16384 >= b:
				g = [27, b - 12289, 12];
				break;
			case 24576 >= b:
				g = [28, b - 16385, 13];
				break;
			case 32768 >= b:
				g = [29, b - 24577, 13];
				break;
			default:
				i("invalid distance");
			}
			f = g;
			d[e++] = f[0];
			d[e++] = f[1];
			d[e++] = f[2];
			var h, j;
			h = 0;
			for (j = d.length; h < j; ++h) l[q++] = d[h];
			t[d[0]]++;
			z[d[3]]++;
			E = a.length + c - 1;
			n = null;
		}
		var e, f, d, g, h, m = {},
			j, s, n, l = H ? new Uint16Array(2 * c.length) : [],
			q = 0,
			E = 0,
			t = new(H ? Uint32Array : Array)(286),
			z = new(H ? Uint32Array : Array)(30),
			K = a.w,
			A;
		if (!H) {
			for (d = 0; 285 >= d;) t[d++] = 0;
			for (d = 0; 29 >= d;) z[d++] = 0;
		}
		t[256] = 1;
		e = 0;
		for (f = c.length; e < f; ++e) {
			d = h = 0;
			for (g = 3; d < g && e + d !== f; ++d) h = h << 8 | c[e + d];
			m[h] === r && (m[h] = []);
			j = m[h];
			if (!(0 < E--)) {
				for (; 0 < j.length && 32768 < e - j[0];) j.shift();
				if (e + 3 >= f) {
					n && b(n, -1);
					d = 0;
					for (g = f - e; d < g; ++d) A = c[e + d], l[q++] = A, ++t[A];
					break;
				}
				if (0 < j.length) {
					var x = r,
						B = r,
						k = 0,
						p = r,
						D = r,
						C = r,
						da = r,
						W = c.length,
						D = 0,
						da = j.length;
					a: for (; D < da; D++) {
						x = j[da - D - 1];
						p = 3;
						if (3 < k) {
							for (C = k; 3 < C; C--) if (c[x + C - 1] !== c[e + C - 1]) continue a;
							p = k;
						}
						for (; 258 > p && e + p < W && c[x + p] === c[e + p];)++p;
						p > k && (B = x, k = p);
						if (258 === p) break;
					}
					s = new wa(k, e - B);
					n ? n.length < s.length ? (A = c[e - 1], l[q++] = A, ++t[A], b(s, 0)) : b(n, -1) : s.length < K ? n = s : b(s, 0);
				} else n ? b(n, -1) : (A = c[e], l[q++] = A, ++t[A]);
			}
			j.push(e);
		}
		l[q++] = 256;
		t[256]++;
		a.L = t;
		a.K = z;
		return H ? l.subarray(0, q) : l;
	}

	function ta(a, c) {
		function b(a) {
			var c = z[a][K[a]];
			c === n ? (b(a + 1), b(a + 1)) : --E[c];
			++K[a];
		}
		var e = a.length,
			f = new la(572),
			d = new(H ? Uint8Array : Array)(e),
			g, h, m, j, s;
		if (!H) for (j = 0; j < e; j++) d[j] = 0;
		for (j = 0; j < e; ++j) 0 < a[j] && f.push(j, a[j]);
		g = Array(f.length / 2);
		h = new(H ? Uint32Array : Array)(f.length / 2);
		if (1 === g.length) return d[f.pop().index] = 1, d;
		j = 0;
		for (s = f.length / 2; j < s; ++j) g[j] = f.pop(), h[j] = g[j].value;
		var n = h.length,
			l = new(H ? Uint16Array : Array)(c),
			q = new(H ? Uint8Array : Array)(c),
			E = new(H ? Uint8Array : Array)(n),
			t = Array(c),
			z = Array(c),
			K = Array(c),
			A = (1 << c) - n,
			x = 1 << c - 1,
			B, k, p, D, C;
		l[c - 1] = n;
		for (k = 0; k < c; ++k) A < x ? q[k] = 0 : (q[k] = 1, A -= x), A <<= 1, l[c - 2 - k] = (l[c - 1 - k] / 2 | 0) + n;
		l[0] = q[0];
		t[0] = Array(l[0]);
		z[0] = Array(l[0]);
		for (k = 1; k < c; ++k) l[k] > 2 * l[k - 1] + q[k] && (l[k] = 2 * l[k - 1] + q[k]), t[k] = Array(l[k]), z[k] = Array(l[k]);
		for (B = 0; B < n; ++B) E[B] = c;
		for (p = 0; p < l[c - 1]; ++p) t[c - 1][p] = h[p], z[c - 1][p] = p;
		for (B = 0; B < c; ++B) K[B] = 0;
		1 === q[c - 1] && (--E[0], ++K[c - 1]);
		for (k = c - 2; 0 <= k; --k) {
			D = B = 0;
			C = K[k + 1];
			for (p = 0; p < l[k]; p++) D = t[k + 1][C] + t[k + 1][C + 1], D > h[B] ? (t[k][p] = D, z[k][p] = n, C += 2) : (t[k][p] = h[B], z[k][p] = B, ++B);
			K[k] = 0;
			1 === q[k] && b(k);
		}
		m = E;
		j = 0;
		for (s = g.length; j < s; ++j) d[g[j].index] = m[j];
		return d;
	}

	function ua(a) {
		var c = new(H ? Uint16Array : Array)(a.length),
			b = [],
			e = [],
			f = 0,
			d, g, h, m;
		d = 0;
		for (g = a.length; d < g; d++) b[a[d]] = (b[a[d]] | 0) + 1;
		d = 1;
		for (g = 16; d <= g; d++) e[d] = f, f += b[d] | 0, f > 1 << d && i("overcommitted"), f <<= 1;
		65536 > f && i("undercommitted");
		d = 0;
		for (g = a.length; d < g; d++) {
			f = e[a[d]];
			e[a[d]] += 1;
			h = c[d] = 0;
			for (m = a[d]; h < m; h++) c[d] = c[d] << 1 | f & 1, f >>>= 1;
		}
		return c;
	}

	function Da(a, c) {
		this.input = a;
		this.a = new(H ? Uint8Array : Array)(32768);
		this.h = U.j;
		var b = {},
			e;
		if ((c || !(c = {})) && "number" === typeof c.compressionType) this.h = c.compressionType;
		for (e in c) b[e] = c[e];
		b.outputBuffer = this.a;
		this.z = new ma(this.input, b);
	}
	var U = qa;
	Da.prototype.n = function() {
		var a, c, b, e, f, d, g, h = 0;
		g = this.a;
		a = Ea;
		switch (a) {
		case Ea:
			c = Math.LOG2E * Math.log(32768) - 8;
			break;
		default:
			i(Error("invalid compression method"));
		}
		b = c << 4 | a;
		g[h++] = b;
		switch (a) {
		case Ea:
			switch (this.h) {
			case U.NONE:
				f = 0;
				break;
			case U.r:
				f = 1;
				break;
			case U.j:
				f = 2;
				break;
			default:
				i(Error("unsupported compression type"));
			}
			break;
		default:
			i(Error("invalid compression method"));
		}
		e = f << 6 | 0;
		g[h++] = e | 31 - (256 * b + e) % 31;
		d = ba(this.input);
		this.z.b = h;
		g = this.z.n();
		h = g.length;
		H && (g = new Uint8Array(g.buffer), g.length <= h + 4 && (this.a = new Uint8Array(g.length + 4), this.a.set(g), g = this.a), g = g.subarray(0, h + 4));
		g[h++] = d >> 24 & 255;
		g[h++] = d >> 16 & 255;
		g[h++] = d >> 8 & 255;
		g[h++] = d & 255;
		return g;
	};
	y("Zlib.Deflate", Da);
	y("Zlib.Deflate.compress", function(a, c) {
		return new Da(a, c).n();
	});
	y("Zlib.Deflate.CompressionType", U);
	y("Zlib.Deflate.CompressionType.NONE", U.NONE);
	y("Zlib.Deflate.CompressionType.FIXED", U.r);
	y("Zlib.Deflate.CompressionType.DYNAMIC", U.j);

	function V(a, c) {
		this.k = [];
		this.l = 32768;
		this.e = this.g = this.c = this.q = 0;
		this.input = H ? new Uint8Array(a) : a;
		this.s = !1;
		this.m = Fa;
		this.B = !1;
		if (c || !(c = {})) c.index && (this.c = c.index), c.bufferSize && (this.l = c.bufferSize), c.bufferType && (this.m = c.bufferType), c.resize && (this.B = c.resize);
		switch (this.m) {
		case Ga:
			this.b = 32768;
			this.a = new(H ? Uint8Array : Array)(32768 + this.l + 258);
			break;
		case Fa:
			this.b = 0;
			this.a = new(H ? Uint8Array : Array)(this.l);
			this.f = this.J;
			this.t = this.H;
			this.o = this.I;
			break;
		default:
			i(Error("invalid inflate mode"));
		}
	}
	var Ga = 0,
		Fa = 1,
		Ha = {
			D: Ga,
			C: Fa
		};
	V.prototype.p = function() {
		for (; !this.s;) {
			var a = X(this, 3);
			a & 1 && (this.s = v);
			a >>>= 1;
			switch (a) {
			case 0:
				var c = this.input,
					b = this.c,
					e = this.a,
					f = this.b,
					d = r,
					g = r,
					h = r,
					m = e.length,
					j = r;
				this.e = this.g = 0;
				d = c[b++];
				d === r && i(Error("invalid uncompressed block header: LEN (first byte)"));
				g = d;
				d = c[b++];
				d === r && i(Error("invalid uncompressed block header: LEN (second byte)"));
				g |= d << 8;
				d = c[b++];
				d === r && i(Error("invalid uncompressed block header: NLEN (first byte)"));
				h = d;
				d = c[b++];
				d === r && i(Error("invalid uncompressed block header: NLEN (second byte)"));
				h |= d << 8;
				g === ~h && i(Error("invalid uncompressed block header: length verify"));
				b + g > c.length && i(Error("input buffer is broken"));
				switch (this.m) {
				case Ga:
					for (; f + g > e.length;) {
						j = m - f;
						g -= j;
						if (H) e.set(c.subarray(b, b + j), f), f += j, b += j;
						else for (; j--;) e[f++] = c[b++];
						this.b = f;
						e = this.f();
						f = this.b;
					}
					break;
				case Fa:
					for (; f + g > e.length;) e = this.f({
						v: 2
					});
					break;
				default:
					i(Error("invalid inflate mode"));
				}
				if (H) e.set(c.subarray(b, b + g), f), f += g, b += g;
				else for (; g--;) e[f++] = c[b++];
				this.c = b;
				this.b = f;
				this.a = e;
				break;
			case 1:
				this.o(Ia, Ja);
				break;
			case 2:
				Ka(this);
				break;
			default:
				i(Error("unknown BTYPE: " + a));
			}
		}
		return this.t();
	};
	var La = [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15],
		Za = H ? new Uint16Array(La) : La,
		$a = [3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15, 17, 19, 23, 27, 31, 35, 43, 51, 59, 67, 83, 99, 115, 131, 163, 195, 227, 258, 258, 258],
		ab = H ? new Uint16Array($a) : $a,
		bb = [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0, 0, 0],
		cb = H ? new Uint8Array(bb) : bb,
		db = [1, 2, 3, 4, 5, 7, 9, 13, 17, 25, 33, 49, 65, 97, 129, 193, 257, 385, 513, 769, 1025, 1537, 2049, 3073, 4097, 6145, 8193, 12289, 16385, 24577],
		eb = H ? new Uint16Array(db) : db,
		fb = [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13],
		gb = H ? new Uint8Array(fb) : fb,
		hb = new(H ? Uint8Array : Array)(288),
		Y, ib;
	Y = 0;
	for (ib = hb.length; Y < ib; ++Y) hb[Y] = 143 >= Y ? 8 : 255 >= Y ? 9 : 279 >= Y ? 7 : 8;
	var Ia = S(hb),
		jb = new(H ? Uint8Array : Array)(30),
		kb, lb;
	kb = 0;
	for (lb = jb.length; kb < lb; ++kb) jb[kb] = 5;
	var Ja = S(jb);

	function X(a, c) {
		for (var b = a.g, e = a.e, f = a.input, d = a.c, g; e < c;) g = f[d++], g === r && i(Error("input buffer is broken")), b |= g << e, e += 8;
		g = b & (1 << c) - 1;
		a.g = b >>> c;
		a.e = e - c;
		a.c = d;
		return g;
	}

	function mb(a, c) {
		for (var b = a.g, e = a.e, f = a.input, d = a.c, g = c[0], h = c[1], m, j, s; e < h;) m = f[d++], m === r && i(Error("input buffer is broken")), b |= m << e, e += 8;
		j = g[b & (1 << h) - 1];
		s = j >>> 16;
		a.g = b >> s;
		a.e = e - s;
		a.c = d;
		return j & 65535;
	}

	function Ka(a) {
		function c(a, b, c) {
			var d, e, f, g;
			for (g = 0; g < a;) switch (d = mb(this, b), d) {
			case 16:
				for (f = 3 + X(this, 2); f--;) c[g++] = e;
				break;
			case 17:
				for (f = 3 + X(this, 3); f--;) c[g++] = 0;
				e = 0;
				break;
			case 18:
				for (f = 11 + X(this, 7); f--;) c[g++] = 0;
				e = 0;
				break;
			default:
				e = c[g++] = d;
			}
			return c;
		}
		var b = X(a, 5) + 257,
			e = X(a, 5) + 1,
			f = X(a, 4) + 4,
			d = new(H ? Uint8Array : Array)(Za.length),
			g, h, m, j;
		for (j = 0; j < f; ++j) d[Za[j]] = X(a, 3);
		g = S(d);
		h = new(H ? Uint8Array : Array)(b);
		m = new(H ? Uint8Array : Array)(e);
		a.o(S(c.call(a, b, g, h)), S(c.call(a, e, g, m)));
	}
	V.prototype.o = function(a, c) {
		var b = this.a,
			e = this.b;
		this.u = a;
		for (var f = b.length - 258, d, g, h, m; 256 !== (d = mb(this, a));) if (256 > d) e >= f && (this.b = e, b = this.f(), e = this.b), b[e++] = d;
		else {
			g = d - 257;
			m = ab[g];
			0 < cb[g] && (m += X(this, cb[g]));
			d = mb(this, c);
			h = eb[d];
			0 < gb[d] && (h += X(this, gb[d]));
			e >= f && (this.b = e, b = this.f(), e = this.b);
			for (; m--;) b[e] = b[e++-h];
		}
		for (; 8 <= this.e;) this.e -= 8, this.c--;
		this.b = e;
	};
	V.prototype.I = function(a, c) {
		var b = this.a,
			e = this.b;
		this.u = a;
		for (var f = b.length, d, g, h, m; 256 !== (d = mb(this, a));) if (256 > d) e >= f && (b = this.f(), f = b.length), b[e++] = d;
		else {
			g = d - 257;
			m = ab[g];
			0 < cb[g] && (m += X(this, cb[g]));
			d = mb(this, c);
			h = eb[d];
			0 < gb[d] && (h += X(this, gb[d]));
			e + m > f && (b = this.f(), f = b.length);
			for (; m--;) b[e] = b[e++-h];
		}
		for (; 8 <= this.e;) this.e -= 8, this.c--;
		this.b = e;
	};
	V.prototype.f = function() {
		var a = new(H ? Uint8Array : Array)(this.b - 32768),
			c = this.b - 32768,
			b, e, f = this.a;
		if (H) a.set(f.subarray(32768, a.length));
		else {
			b = 0;
			for (e = a.length; b < e; ++b) a[b] = f[b + 32768];
		}
		this.k.push(a);
		this.q += a.length;
		if (H) f.set(f.subarray(c, c + 32768));
		else for (b = 0; 32768 > b; ++b) f[b] = f[c + b];
		this.b = 32768;
		return f;
	};
	V.prototype.J = function(a) {
		var c, b = this.input.length / this.c + 1 | 0,
			e, f, d, g = this.input,
			h = this.a;
		a && ("number" === typeof a.v && (b = a.v), "number" === typeof a.F && (b += a.F));
		2 > b ? (e = (g.length - this.c) / this.u[2], d = 258 * (e / 2) | 0, f = d < h.length ? h.length + d : h.length << 1) : f = h.length * b;
		H ? (c = new Uint8Array(f), c.set(h)) : c = h;
		return this.a = c;
	};
	V.prototype.t = function() {
		var a = 0,
			c = this.a,
			b = this.k,
			e, f = new(H ? Uint8Array : Array)(this.q + (this.b - 32768)),
			d, g, h, m;
		if (0 === b.length) return H ? this.a.subarray(32768, this.b) : this.a.slice(32768, this.b);
		d = 0;
		for (g = b.length; d < g; ++d) {
			e = b[d];
			h = 0;
			for (m = e.length; h < m; ++h) f[a++] = e[h];
		}
		d = 32768;
		for (g = this.b; d < g; ++d) f[a++] = c[d];
		this.k = [];
		return this.buffer = f;
	};
	V.prototype.H = function() {
		var a, c = this.b;
		H ? this.B ? (a = new Uint8Array(c), a.set(this.a.subarray(0, c))) : a = this.a.subarray(0, c) : (this.a.length > c && (this.a.length = c), a = this.a);
		return this.buffer = a;
	};

	function nb(a, c) {
		var b, e;
		this.input = a;
		this.c = 0;
		if (c || !(c = {})) c.index && (this.c = c.index), c.verify && (this.M = c.verify);
		b = a[this.c++];
		e = a[this.c++];
		switch (b & 15) {
		case Ea:
			this.method = Ea;
			break;
		default:
			i(Error("unsupported compression method"));
		}
		0 !== ((b << 8) + e) % 31 && i(Error("invalid fcheck flag:" + ((b << 8) + e) % 31));
		e & 32 && i(Error("fdict flag is not supported"));
		this.A = new V(a, {
			index: this.c,
			bufferSize: c.bufferSize,
			bufferType: c.bufferType,
			resize: c.resize
		});
	}
	nb.prototype.p = function() {
		var a = this.input,
			c, b;
		c = this.A.p();
		this.c = this.A.c;
		this.M && (b = (a[this.c++] << 24 | a[this.c++] << 16 | a[this.c++] << 8 | a[this.c++]) >>> 0, b !== ba(c) && i(Error("invalid adler-32 checksum")));
		return c;
	};
	y("Zlib.Inflate", nb);
	y("Zlib.Inflate.BufferType", Ha);
	Ha.ADAPTIVE = Ha.C;
	Ha.BLOCK = Ha.D;
	y("Zlib.Inflate.prototype.decompress", nb.prototype.p);
	var ob = [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15];
	H && new Uint16Array(ob);
	var pb = [3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 15, 17, 19, 23, 27, 31, 35, 43, 51, 59, 67, 83, 99, 115, 131, 163, 195, 227, 258, 258, 258];
	H && new Uint16Array(pb);
	var qb = [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0, 0, 0];
	H && new Uint8Array(qb);
	var rb = [1, 2, 3, 4, 5, 7, 9, 13, 17, 25, 33, 49, 65, 97, 129, 193, 257, 385, 513, 769, 1025, 1537, 2049, 3073, 4097, 6145, 8193, 12289, 16385, 24577];
	H && new Uint16Array(rb);
	var sb = [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13];
	H && new Uint8Array(sb);
	var tb = new(H ? Uint8Array : Array)(288),
		Z, ub;
	Z = 0;
	for (ub = tb.length; Z < ub; ++Z) tb[Z] = 143 >= Z ? 8 : 255 >= Z ? 9 : 279 >= Z ? 7 : 8;
	S(tb);
	var vb = new(H ? Uint8Array : Array)(30),
		wb, xb;
	wb = 0;
	for (xb = vb.length; wb < xb; ++wb) vb[wb] = 5;
	S(vb);
	var Ea = 8;
}).call(this);
cc.Point = function(e, t) {
	this.x = e || 0, this.y = t || 0;
}, cc.PointMake = function(e, t) {
	return new cc.Point(e, t);
}, cc.p = function(e, t) {
	return {
		x: e,
		y: t
	};
}, cc._p = cc.p, cc.PointZero = function() {
	return cc.p(0, 0);
}, Object.defineProperties(cc, {
	POINT_ZERO: {
		get: function() {
			return cc.p(0, 0);
		}
	},
	SIZE_ZERO: {
		get: function() {
			return cc.size(0, 0);
		}
	},
	RECT_ZERO: {
		get: function() {
			return cc.rect(0, 0, 0, 0);
		}
	}
}), cc.pointEqualToPoint = function(e, t) {
	return !e || !t ? !1 : e.x == t.x && e.y == t.y;
}, cc.Point.CCPointEqualToPoint = cc.pointEqualToPoint, cc.Size = function(e, t) {
	this.width = e || 0, this.height = t || 0;
}, cc.SizeMake = function(e, t) {
	return cc.size(e, t);
}, cc.size = function(e, t) {
	return {
		width: e,
		height: t
	};
}, cc._size = cc.size, cc.SizeZero = function() {
	return cc.size(0, 0);
}, cc.sizeEqualToSize = function(e, t) {
	return !e || !t ? !1 : e.width == t.width && e.height == t.height;
}, cc.Size.CCSizeEqualToSize = cc.sizeEqualToSize, cc.Rect = function(e, t, n, r) {
	switch (arguments.length) {
	case 0:
		this.origin = cc.p(0, 0), this.size = cc.size(0, 0);
		break;
	case 1:
		var i = e;
		if (!i) this.origin = cc.p(0, 0), this.size = cc.size(0, 0);
		else {
			if (!(i instanceof cc.Rect)) throw "unknown argument type";
			this.origin = cc.p(i.origin.x, i.origin.y), this.size = cc.size(i.size.width, i.size.height);
		}
		break;
	case 2:
		this.origin = e ? cc.p(e.x, e.y) : cc.p(0, 0), this.size = t ? cc.size(t.width, t.height) : cc.size(0, 0);
		break;
	case 4:
		this.origin = cc.p(e || 0, t || 0), this.size = cc.size(n || 0, r || 0);
		break;
	default:
		throw "unknown argument type";
	}
}, cc.RectMake = function(e, t, n, r) {
	return cc.rect(e, t, n, r);
}, cc.rect = function(e, t, n, r) {
	return new cc.Rect(e, t, n, r);
}, cc._rect = cc.rect, cc.RectZero = function() {
	return cc.rect(0, 0, 0, 0);
}, cc.rectEqualToRect = function(e, t) {
	return !e || !t ? !1 : cc.Point.CCPointEqualToPoint(e.origin, t.origin) && cc.Size.CCSizeEqualToSize(e.size, t.size);
}, cc.rectContainsRect = function(e, t) {
	return !e || !t ? !1 : e.origin.x >= t.origin.x || e.origin.y >= t.origin.y || e.origin.x + e.size.width <= t.origin.x + t.size.width || e.origin.y + e.size.height <= t.origin.y + t.size.height ? !1 : !0;
}, cc.rectGetMaxX = function(e) {
	return e.origin.x + e.size.width;
}, cc.rectGetMidX = function(e) {
	return e.origin.x + e.size.width / 2;
}, cc.rectGetMinX = function(e) {
	return e.origin.x;
}, cc.rectGetMaxY = function(e) {
	return e.origin.y + e.size.height;
}, cc.rectGetMidY = function(e) {
	return e.origin.y + e.size.height / 2;
}, cc.rectGetMinY = function(e) {
	return e.origin.y;
}, cc.rectContainsPoint = function(e, t) {
	var n = !1;
	return t.x >= cc.Rect.CCRectGetMinX(e) && t.x <= cc.Rect.CCRectGetMaxX(e) && t.y >= cc.Rect.CCRectGetMinY(e) && t.y <= cc.Rect.CCRectGetMaxY(e) && (n = !0), n;
}, cc.rectIntersectsRect = function(e, t) {
	return !(cc.Rect.CCRectGetMaxX(e) < cc.Rect.CCRectGetMinX(t) || cc.Rect.CCRectGetMaxX(t) < cc.Rect.CCRectGetMinX(e) || cc.Rect.CCRectGetMaxY(e) < cc.Rect.CCRectGetMinY(t) || cc.Rect.CCRectGetMaxY(t) < cc.Rect.CCRectGetMinY(e));
}, cc.rectOverlapsRect = function(e, t) {
	return e.origin.x + e.size.width < t.origin.x ? !1 : t.origin.x + t.size.width < e.origin.x ? !1 : e.origin.y + e.size.height < t.origin.y ? !1 : t.origin.y + t.size.height < e.origin.y ? !1 : !0;
}, cc.rectUnion = function(e, t) {
	var n = cc.rect(0, 0, 0, 0);
	return n.origin.x = Math.min(e.origin.x, t.origin.x), n.origin.y = Math.min(e.origin.y, t.origin.y), n.size.width = Math.max(e.origin.x + e.size.width, t.origin.x + t.size.width) - n.origin.x, n.size.height = Math.max(e.origin.y + e.size.height, t.origin.y + t.size.height) - n.origin.y, n;
}, cc.rectIntersection = function(e, t) {
	var n = cc.rect(Math.max(cc.Rect.CCRectGetMinX(e), cc.Rect.CCRectGetMinX(t)), Math.max(cc.Rect.CCRectGetMinY(e), cc.Rect.CCRectGetMinY(t)), 0, 0);
	return n.size.width = Math.min(cc.Rect.CCRectGetMaxX(e), cc.Rect.CCRectGetMaxX(t)) - cc.Rect.CCRectGetMinX(n), n.size.height = Math.min(cc.Rect.CCRectGetMaxY(e), cc.Rect.CCRectGetMaxY(t)) - cc.Rect.CCRectGetMinY(n), n;
}, cc.Rect.prototype.getX = function() {
	return this.origin.x;
}, cc.Rect.prototype.setX = function(e) {
	this.origin.x = e;
}, cc.Rect.prototype.getY = function() {
	return this.origin.y;
}, cc.Rect.prototype.setY = function(e) {
	this.origin.y = e;
}, cc.Rect.prototype.getWidth = function() {
	return this.size.width;
}, cc.Rect.prototype.setWidth = function(e) {
	this.size.width = e;
}, cc.Rect.prototype.getHeight = function() {
	return this.size.height;
}, cc.Rect.prototype.setHeight = function(e) {
	this.size.height = e;
}, Object.defineProperties(cc.Rect.prototype, {
	x: {
		get: function() {
			return this.getX();
		},
		set: function(e) {
			this.setX(e);
		},
		enumerable: !0,
		configurable: !0
	},
	y: {
		get: function() {
			return this.getY();
		},
		set: function(e) {
			this.setY(e);
		},
		enumerable: !0,
		configurable: !0
	},
	width: {
		get: function() {
			return this.getWidth();
		},
		set: function(e) {
			this.setWidth(e);
		},
		enumerable: !0,
		configurable: !0
	},
	height: {
		get: function() {
			return this.getHeight();
		},
		set: function(e) {
			this.setHeight(e);
		},
		enumerable: !0,
		configurable: !0
	}
}), cc.Rect.CCRectEqualToRect = cc.rectEqualToRect, cc.Rect.CCRectContainsRect = cc.rectContainsRect, cc.Rect.CCRectGetMaxX = cc.rectGetMaxX, cc.Rect.CCRectGetMidX = cc.rectGetMidX, cc.Rect.CCRectGetMinX = cc.rectGetMinX, cc.Rect.CCRectGetMaxY = cc.rectGetMaxY, cc.Rect.CCRectGetMidY = cc.rectGetMidY, cc.Rect.CCRectGetMinY = cc.rectGetMinY, cc.Rect.CCRectContainsPoint = cc.rectContainsPoint, cc.Rect.CCRectIntersectsRect = cc.rectIntersectsRect, cc.Rect.CCRectUnion = cc.rectUnion, cc.Rect.CCRectIntersection = cc.rectIntersection;
var sys = sys || {};
sys.localStorage = window.localStorage, Object.defineProperties(sys, {
	capabilities: {
		get: function() {
			var e = {
				canvas: !0
			};
			return "ontouchstart" in document.documentElement ? e.touches = !0 : "onmouseup" in document.documentElement && (e.mouse = !0), "onkeyup" in document.documentElement && (e.keyboard = !0), e;
		},
		enumerable: !0,
		configurable: !0
	},
	os: {
		get: function() {
			var e = navigator.userAgent.match(/(iPad|iPhone|iPod)/i) ? !0 : !1,
				t = navigator.userAgent.match(/android/i) || navigator.platform.match(/android/i) ? !0 : !1,
				n = navigator.appVersion;
			navigator.appVersion.indexOf("Win") != -1 ? n = "Windows" : navigator.appVersion.indexOf("Mac") != -1 ? n = "OS X" : navigator.appVersion.indexOf("X11") != -1 ? n = "UNIX" : navigator.appVersion.indexOf("Linux") != -1 ? n = "Linux" : e ? n = "iOS" : t && (n = "Android");
		},
		enumerable: !0,
		configurable: !0
	},
	platform: {
		get: function() {
			return "browser";
		},
		enumerable: !0,
		configurable: !0
	},
	version: {
		get: function() {
			return cc.ENGINE_VERSION;
		},
		enumerable: !0,
		configurable: !0
	}
});
cc.ENGINE_VERSION = "Cocos2d-html5-v2.1.1", cc.FIX_ARTIFACTS_BY_STRECHING_TEXEL = 0, cc.DIRECTOR_STATS_POSITION = cc.p(0, 0), cc.DIRECTOR_FPS_INTERVAL = .5, cc.COCOSNODE_RENDER_SUBPIXEL = 1, cc.SPRITEBATCHNODE_RENDER_SUBPIXEL = 1, cc.NODE_TRANSFORM_USING_AFFINE_MATRIX = 1, cc.OPTIMIZE_BLEND_FUNC_FOR_PREMULTIPLIED_ALPHA = 1, cc.TEXTURE_ATLAS_USE_TRIANGLE_STRIP = 0, cc.TEXTURE_ATLAS_USE_VAO = 1, cc.TEXTURE_NPOT_SUPPORT = 0, cc.RETINA_DISPLAY_SUPPORT = 1, cc.RETINA_DISPLAY_FILENAME_SUFFIX = "-hd", cc.SPRITE_DEBUG_DRAW = 0, cc.SPRITEBATCHNODE_DEBUG_DRAW = 0, cc.LABELBMFONT_DEBUG_DRAW = 0, cc.LABELATLAS_DEBUG_DRAW = 0, cc.IS_RETINA_DISPLAY_SUPPORTED = 1, cc.DEFAULT_ENGINE = cc.ENGINE_VERSION + "-canvas", cc.config = {
	platform: sys.platform
}, cc.dumpConfig = function() {
	for (i in sys) cc.log(i + " = " + sys[i]);
};
cc.Set = cc.Class.extend({
	ctor: function(e) {
		e ? this._set = [].concat(e._set) : this._set = [];
	},
	copy: function() {
		return new cc.Set(this);
	},
	mutableCopy: function() {
		return this.copy();
	},
	count: function() {
		return this._set.length;
	},
	addObject: function(e) {
		if (cc.ArrayContainsObject(this._set, e)) return;
		this._set.push(e), this._set.sort(function(e, t) {
			return e - t;
		});
	},
	removeObject: function(e) {
		var t = 0;
		for (var n = 0, r = 0; n < this._set.length; n++) this._set[n] != e && (this._set[r++] = this._set[n], t++);
		this._set.length = t;
	},
	begin: function() {
		return this._set && this._set.length > 0 ? this._set[0] : null;
	},
	end: function() {
		return this._set && this._set.length > 0 ? this._set[this._set.length - 1] : null;
	},
	containsObject: function(e) {
		return cc.ArrayContainsObject(this._set, e);
	},
	anyObject: function() {
		return this._set && this._set.length > 0 ? this._set[0] : null;
	},
	_set: null
}), cc.NSMutableSet = cc.Set;
cc.splitWithForm = function(e, t) {
	do {
		if (!e) break;
		if (e.length == 0) break;
		var n = e.indexOf("{"),
			r = e.indexOf("}");
		if (n == -1 || r == -1) break;
		if (n > r) break;
		var i = e.substr(n + 1, r - n - 1);
		if (i.length == 0) break;
		var s = i.indexOf("{"),
			o = i.indexOf("}");
		if (s != -1 || o != -1) break;
		t = i.split(",");
		if (t.length != 2 || t[0] != null || t[1] != null) break;
	} while (0);
	return t;
}, cc.RectFromString = function(e) {
	var t = cc.RectZero();
	do {
		if (!e) break;
		var n = e.indexOf("{") + 1,
			r = e.lastIndexOf("}", e.length);
		if (n == -1 || r == -1) break;
		e = e.substring(n, r);
		var i = e.indexOf("}");
		if (i == -1) break;
		i = e.indexOf(",", i);
		if (i == -1) break;
		var s = e.substr(0, i),
			o = e.substr(i + 1, e.length - i),
			u = cc.splitWithForm(s),
			a = cc.splitWithForm(o),
			f = parseFloat(u[0]),
			l = parseFloat(u[1]),
			c = parseFloat(a[0]),
			h = parseFloat(a[1]);
		t = cc.rect(f, l, c, h);
	} while (0);
	return t;
}, cc.PointFromString = function(e) {
	var t = cc.PointZero();
	try {
		if (e == "") return t;
		var n = cc.splitWithForm(e),
			r = parseFloat(n[0]),
			i = parseFloat(n[1]);
		t = cc.p(r, i);
	} catch (s) {}
	return t;
}, cc.SizeFromString = function(e) {
	var t = cc.SizeZero();
	try {
		if (e == "") return t;
		var n = cc.splitWithForm(e),
			r = parseFloat(n[0]),
			i = parseFloat(n[1]);
		t = cc.size(r, i);
	} catch (s) {}
	return t;
};
cc.AffineTransform = function(e, t, n, r, i, s) {
	this.a = e, this.b = t, this.c = n, this.d = r, this.tx = i, this.ty = s;
}, cc.__AffineTransformMake = function(e, t, n, r, i, s) {
	return new cc.AffineTransform(e, t, n, r, i, s);
}, cc.AffineTransformMake = function(e, t, n, r, i, s) {
	return new cc.AffineTransform(e, t, n, r, i, s);
}, cc.__PointApplyAffineTransform = function(e, t) {
	var n = cc.p(0, 0);
	return n.x = t.a * e.x + t.c * e.y + t.tx, n.y = t.b * e.x + t.d * e.y + t.ty, n;
}, cc.PointApplyAffineTransform = function(e, t) {
	return cc.__PointApplyAffineTransform(e, t);
}, cc.__SizeApplyAffineTransform = function(e, t) {
	var n = cc.size(0, 0);
	return n.width = t.a * e.width + t.c * e.height, n.height = t.b * e.width + t.d * e.height, n;
}, cc.SizeApplyAffineTransform = function(e, t) {
	return cc.__SizeApplyAffineTransform(e, t);
}, cc.AffineTransformMakeIdentity = function() {
	return cc.__AffineTransformMake(1, 0, 0, 1, 0, 0);
}, cc.AffineTransformIdentity = function() {
	return cc.AffineTransformMakeIdentity();
}, cc.RectApplyAffineTransform = function(e, t) {
	var n = cc.Rect.CCRectGetMinY(e),
		r = cc.Rect.CCRectGetMinX(e),
		i = cc.Rect.CCRectGetMaxX(e),
		s = cc.Rect.CCRectGetMaxY(e),
		o = cc.PointApplyAffineTransform(cc.p(r, n), t),
		u = cc.PointApplyAffineTransform(cc.p(i, n), t),
		a = cc.PointApplyAffineTransform(cc.p(r, s), t),
		f = cc.PointApplyAffineTransform(cc.p(i, s), t),
		l = Math.min(Math.min(o.x, u.x), Math.min(a.x, f.x)),
		c = Math.max(Math.max(o.x, u.x), Math.max(a.x, f.x)),
		h = Math.min(Math.min(o.y, u.y), Math.min(a.y, f.y)),
		p = Math.max(Math.max(o.y, u.y), Math.max(a.y, f.y));
	return cc.rect(l, h, c - l, p - h);
}, cc.AffineTransformTranslate = function(e, t, n) {
	return cc.__AffineTransformMake(e.a, e.b, e.c, e.d, e.tx + e.a * t + e.c * n, e.ty + e.b * t + e.d * n);
}, cc.AffineTransformScale = function(e, t, n) {
	return cc.__AffineTransformMake(e.a * t, e.b * t, e.c * n, e.d * n, e.tx, e.ty);
}, cc.AffineTransformRotate = function(e, t) {
	var n = Math.sin(t),
		r = Math.cos(t);
	return cc.__AffineTransformMake(e.a * r + e.c * n, e.b * r + e.d * n, e.c * r - e.a * n, e.d * r - e.b * n, e.tx, e.ty);
}, cc.AffineTransformConcat = function(e, t) {
	return cc.__AffineTransformMake(e.a * t.a + e.b * t.c, e.a * t.b + e.b * t.d, e.c * t.a + e.d * t.c, e.c * t.b + e.d * t.d, e.tx * t.a + e.ty * t.c + t.tx, e.tx * t.b + e.ty * t.d + t.ty);
}, cc.AffineTransformEqualToTransform = function(e, t) {
	return e.a == t.a && e.b == t.b && e.c == t.c && e.d == t.d && e.tx == t.tx && e.ty == t.ty;
}, cc.AffineTransformInvert = function(e) {
	var t = 1 / (e.a * e.d - e.b * e.c);
	return cc.__AffineTransformMake(t * e.d, -t * e.b, -t * e.c, t * e.a, t * (e.c * e.ty - e.d * e.tx), t * (e.b * e.tx - e.a * e.ty));
};
cc.POINT_EPSILON = parseFloat("1.192092896e-07F"), cc.pNeg = function(e) {
	return cc.p(-e.x, -e.y);
}, cc.pAdd = function(e, t) {
	return cc.p(e.x + t.x, e.y + t.y);
}, cc.pSub = function(e, t) {
	return cc.p(e.x - t.x, e.y - t.y);
}, cc.pMult = function(e, t) {
	return cc.p(e.x * t, e.y * t);
}, cc.pMidpoint = function(e, t) {
	return cc.pMult(cc.pAdd(e, t), .5);
}, cc.pDot = function(e, t) {
	return e.x * t.x + e.y * t.y;
}, cc.pCross = function(e, t) {
	return e.x * t.y - e.y * t.x;
}, cc.pPerp = function(e) {
	return cc.p(-e.y, e.x);
}, cc.pRPerp = function(e) {
	return cc.p(e.y, -e.x);
}, cc.pProject = function(e, t) {
	return cc.pMult(t, cc.pDot(e, t) / cc.pDot(t, t));
}, cc.pRotate = function(e, t) {
	return cc.p(e.x * t.x - e.y * t.y, e.x * t.y + e.y * t.x);
}, cc.pUnrotate = function(e, t) {
	return cc.p(e.x * t.x + e.y * t.y, e.y * t.x - e.x * t.y);
}, cc.pLengthSQ = function(e) {
	return cc.pDot(e, e);
}, cc.pLength = function(e) {
	return Math.sqrt(cc.pLengthSQ(e));
}, cc.pDistance = function(e, t) {
	return cc.pLength(cc.pSub(e, t));
}, cc.pNormalize = function(e) {
	return cc.pMult(e, 1 / cc.pLength(e));
}, cc.pForAngle = function(e) {
	return cc.p(Math.cos(e), Math.sin(e));
}, cc.pToAngle = function(e) {
	return Math.atan2(e.y, e.x);
}, cc.clampf = function(e, t, n) {
	if (t > n) {
		var r = t;
		t = n, n = r;
	}
	return e < t ? t : e < n ? e : n;
}, cc.pClamp = function(e, t, n) {
	return cc.p(cc.clampf(e.x, t.x, n.x), cc.clampf(e.y, t.y, n.y));
}, cc.pFromSize = function(e) {
	return cc.p(e.width, e.height);
}, cc.pCompOp = function(e, t) {
	return cc.p(t(e.x), t(e.y));
}, cc.pLerp = function(e, t, n) {
	return cc.pAdd(cc.pMult(e, 1 - n), cc.pMult(t, n));
}, cc.pFuzzyEqual = function(e, t, n) {
	return e.x - n <= t.x && t.x <= e.x + n && e.y - n <= t.y && t.y <= e.y + n ? !0 : !1;
}, cc.pCompMult = function(e, t) {
	return cc.p(e.x * t.x, e.y * t.y);
}, cc.pAngleSigned = function(e, t) {
	var n = cc.pNormalize(e),
		r = cc.pNormalize(t),
		i = Math.atan2(n.x * r.y - n.y * r.x, cc.pDot(n, r));
	return Math.abs(i) < cc.POINT_EPSILON ? 0 : i;
}, cc.pAngle = function(e, t) {
	var n = Math.acos(cc.pDot(cc.pNormalize(e), cc.pNormalize(t)));
	return Math.abs(n) < cc.POINT_EPSILON ? 0 : n;
}, cc.pRotateByAngle = function(e, t, n) {
	var r = cc.pSub(e, t),
		i = Math.cos(n),
		s = Math.sin(n),
		o = r.x;
	return r.x = o * i - r.y * s + t.x, r.y = o * s + r.y * i + t.y, r;
}, cc.pLineIntersect = function(e, t, n, r, i) {
	if (e.x == t.x && e.y == t.y || n.x == r.x && n.y == r.y) return !1;
	var s = t.x - e.x,
		o = t.y - e.y,
		u = r.x - n.x,
		a = r.y - n.y,
		f = e.x - n.x,
		l = e.y - n.y,
		c = a * s - u * o;
	return i.x = u * l - a * f, i.y = s * l - o * f, c == 0 ? i.x == 0 || i.y == 0 ? !0 : !1 : (i.x = i.x / c, i.y = i.y / c, !0);
}, cc.pSegmentIntersect = function(e, t, n, r) {
	var i = cc.p(0, 0);
	return cc.pLineIntersect(e, t, n, r, i) && i.x >= 0 && i.x <= 1 && i.y >= 0 && i.y <= 1 ? !0 : !1;
}, cc.pIntersectPoint = function(e, t, n, r) {
	var i = cc.p(0, 0);
	if (cc.pLineIntersect(e, t, n, r, i)) {
		var s = cc.p(0, 0);
		return s.x = e.x + i.x * (t.x - e.x), s.y = e.y + i.x * (t.y - e.y), s;
	}
	return cc.PointZero();
}, cc.pSameAs = function(e, t) {
	return e.x && t.x ? e.x == t.x && e.y == t.y : !1;
};
cc.UserDefault = cc.Class.extend({
	_db: null,
	init: function() {
		return this._db = this._getLocalStorage(), !0;
	},
	_getLocalStorage: function() {
		try {
			if ( !! window.localStorage) return window.localStorage;
		} catch (e) {
			return undefined;
		}
	},
	_getWebSqlDatabase: function() {},
	getBoolForKey: function(e, t) {
		var n = this._getValueForKey(e),
			r = t || !1;
		return n == "true" ? !0 : n == "false" ? !1 : n ? Boolean(n) : r;
	},
	getIntegerForKey: function(e, t) {
		var n = this._getValueForKey(e),
			r = t || 0;
		return n ? parseInt(n) : r;
	},
	getFloatForKey: function(e, t) {
		var n = this._getValueForKey(e),
			r = t || 0;
		return n ? parseFloat(n) : r;
	},
	getDoubleForKey: function(e, t) {
		return this.getFloatForKey(e, t);
	},
	getStringForKey: function(e, t) {
		var n = this._getValueForKey(e),
			r = t || "";
		return n ? String(n) : r;
	},
	_getValueForKey: function(e) {
		var t;
		return this._db && (t = this._db.getItem(e)), t;
	},
	setBoolForKey: function(e, t) {
		this.setStringForKey(e, String(t));
	},
	setIntegerForKey: function(e, t) {
		if (!e) return;
		this._setValueForKey(e, parseInt(t));
	},
	setFloatForKey: function(e, t) {
		if (!e) return;
		this._setValueForKey(e, parseFloat(t));
	},
	setDoubleForKey: function(e, t) {
		return this.setFloatForKey(e, t);
	},
	setStringForKey: function(e, t) {
		if (!e) return;
		this._setValueForKey(e, String(t));
	},
	_setValueForKey: function(e, t) {
		this._db && this._db.setItem(e, t);
	}
}), cc.UserDefault.getInstance = function() {
	return this._sUserDefault || (this._sUserDefault = new cc.UserDefault(), this._sUserDefault.init()), this._sUserDefault;
}, cc.UserDefault.purgeInstanceUserDefault = function() {
	this._db && this._db.clear();
}, cc.UserDefault._sUserDefault = null, cc.UserDefault._isFilePathInitialized = !1;
cc.NODE_TAG_INVALID = -1, cc.NODE_ON_ENTER = null, cc.NODE_ON_EXIT = null, cc.saveContext = function() {
	cc.renderContextType == cc.CANVAS && cc.renderContext.save();
}, cc.restoreContext = function() {
	cc.renderContextType == cc.CANVAS && cc.renderContext.restore();
}, cc.s_globalOrderOfArrival = 1, cc.Node = cc.Class.extend({
	_zOrder: 0,
	_vertexZ: 0,
	_rotation: 0,
	_scaleX: 1,
	_scaleY: 1,
	_position: cc.p(0, 0),
	_skewX: 0,
	_skewY: 0,
	_children: null,
	_camera: null,
	_grid: null,
	_visible: !0,
	_anchorPoint: cc.p(0, 0),
	_anchorPointInPoints: cc.p(0, 0),
	_contentSize: cc.SizeZero(),
	_running: !1,
	_parent: null,
	_ignoreAnchorPointForPosition: !1,
	_tag: cc.NODE_TAG_INVALID,
	_userData: null,
	_userObject: null,
	_transformDirty: !0,
	_inverseDirty: !0,
	_cacheDirty: !0,
	_transformGLDirty: null,
	_transform: null,
	_inverse: null,
	_reorderChildDirty: !1,
	_shaderProgram: null,
	_orderOfArrival: 0,
	_glServerState: null,
	_actionManager: null,
	_scheduler: null,
	_initializedNode: !1,
	ctor: function() {
		this._initNode();
	},
	_initNode: function() {
		cc.NODE_TRANSFORM_USING_AFFINE_MATRIX && (this._transformGLDirty = !0), this._anchorPoint = cc.p(0, 0), this._anchorPointInPoints = cc.p(0, 0), this._contentSize = cc.size(0, 0), this._position = cc.p(0, 0);
		var e = cc.Director.getInstance();
		this._actionManager = e.getActionManager(), this.getActionManager = function() {
			return this._actionManager;
		}, this._scheduler = e.getScheduler(), this.getScheduler = function() {
			return this._scheduler;
		}, this._initializedNode = !0;
	},
	init: function() {
		return this._initializedNode === !1 && this._initNode(), !0;
	},
	_arrayMakeObjectsPerformSelector: function(e, t) {
		if (!e || e.length == 0) return;
		var n;
		switch (t) {
		case cc.Node.StateCallbackType.onEnter:
			for (n = 0; n < e.length; n++) e[n] && e[n].onEnter();
			break;
		case cc.Node.StateCallbackType.onExit:
			for (n = 0; n < e.length; n++) e[n] && e[n].onExit();
			break;
		case cc.Node.StateCallbackType.onEnterTransitionDidFinish:
			for (n = 0; n < e.length; n++) e[n] && e[n].onEnterTransitionDidFinish();
			break;
		case cc.Node.StateCallbackType.cleanup:
			for (n = 0; n < e.length; n++) e[n] && e[n].cleanup();
			break;
		case cc.Node.StateCallbackType.updateTransform:
			for (n = 0; n < e.length; n++) e[n] && e[n].updateTransform();
			break;
		case cc.Node.StateCallbackType.onExitTransitionDidStart:
			for (n = 0; n < e.length; n++) e[n] && e[n].onExitTransitionDidStart();
			break;
		case cc.Node.StateCallbackType.sortAllChildren:
			for (n = 0; n < e.length; n++) e[n] && e[n].sortAllChildren();
			break;
		default:
			throw "Unknown callback function";
		}
	},
	_addDirtyRegionToDirector: function(e) {},
	_isInDirtyRegion: function() {},
	setNodeDirty: function() {
		this._setNodeDirtyForCache(), this._transformDirty = this._inverseDirty = !0, cc.NODE_TRANSFORM_USING_AFFINE_MATRIX && (this._transformGLDirty = !0);
	},
	_setNodeDirtyForCache: function() {
		this._cacheDirty = !0, this._parent && this._parent._setNodeDirtyForCache();
	},
	getSkewX: function() {
		return this._skewX;
	},
	setSkewX: function(e) {
		this._skewX = e, this.setNodeDirty();
	},
	getSkewY: function() {
		return this._skewY;
	},
	setSkewY: function(e) {
		this._skewY = e, this.setNodeDirty();
	},
	getZOrder: function() {
		return this._zOrder;
	},
	_setZOrder: function(e) {
		this._zOrder = e;
	},
	setZOrder: function(e) {
		this._setZOrder(e), this._parent && this._parent.reorderChild(this, e);
	},
	getVertexZ: function() {
		return this._vertexZ;
	},
	setVertexZ: function(e) {
		this._vertexZ = e;
	},
	getRotation: function() {
		return this._rotation;
	},
	_rotationRadians: 0,
	setRotation: function(e) {
		if (this._rotation == e) return;
		this._rotation = e, this._rotationRadians = this._rotation * (Math.PI / 180), this.setNodeDirty();
	},
	getScale: function() {
		return cc.Assert(this._scaleX == this._scaleY, "cc.Node#scale. ScaleX != ScaleY. Don't know which one to return"), this._scaleX;
	},
	setScale: function(e, t) {
		this._scaleX = e, this._scaleY = t || e, this.setNodeDirty();
	},
	getScaleX: function() {
		return this._scaleX;
	},
	setScaleX: function(e) {
		this._scaleX = e, this.setNodeDirty();
	},
	getScaleY: function() {
		return this._scaleY;
	},
	setScaleY: function(e) {
		this._scaleY = e, this.setNodeDirty();
	},
	setPosition: function(e, t) {
		arguments.length == 2 ? this._position = new cc.Point(e, t) : arguments.length == 1 && (this._position = new cc.Point(e.x, e.y)), this.setNodeDirty();
	},
	_setPositionByValue: function(e, t) {
		arguments.length == 2 ? (this._position.x = e, this._position.y = t) : arguments.length == 1 && (this._position.x = e.x, this._position.y = e.y), this.setNodeDirty();
	},
	getPosition: function() {
		return cc.p(this._position.x, this._position.y);
	},
	getPositionX: function() {
		return this._position.x;
	},
	setPositionX: function(e) {
		this._position.x = e, this.setNodeDirty();
	},
	getPositionY: function() {
		return this._position.y;
	},
	setPositionY: function(e) {
		this._position.y = e, this.setNodeDirty();
	},
	getChildrenCount: function() {
		return this._children ? this._children.length : 0;
	},
	getChildren: function() {
		return this._children || (this._children = []), this._children;
	},
	getCamera: function() {
		return this._camera || (this._camera = new cc.Camera()), this._camera;
	},
	getGrid: function() {
		return this._grid;
	},
	setGrid: function(e) {
		this._grid = e;
	},
	isVisible: function() {
		return this._visible;
	},
	setVisible: function(e) {
		this._visible = e, this.setNodeDirty();
	},
	getAnchorPoint: function() {
		return cc.p(this._anchorPoint.x, this._anchorPoint.y);
	},
	setAnchorPoint: function(e) {
		cc.Point.CCPointEqualToPoint(e, this._anchorPoint) || (this._anchorPoint = new cc.Point(e.x, e.y), this._anchorPointInPoints = new cc.Point(this._contentSize.width * this._anchorPoint.x, this._contentSize.height * this._anchorPoint.y), this.setNodeDirty());
	},
	_setAnchorPointByValue: function(e) {
		cc.Point.CCPointEqualToPoint(e, this._anchorPoint) || (this._anchorPoint.x = e.x, this._anchorPoint.y = e.y, this._anchorPointInPoints.x = this._contentSize.width * this._anchorPoint.x, this._anchorPointInPoints.y = this._contentSize.height * this._anchorPoint.y, this.setNodeDirty());
	},
	getAnchorPointInPoints: function() {
		return cc.p(this._anchorPointInPoints.x, this._anchorPointInPoints.y);
	},
	getContentSize: function() {
		return cc.size(this._contentSize.width, this._contentSize.height);
	},
	setContentSize: function(e) {
		cc.Size.CCSizeEqualToSize(e, this._contentSize) || (this._contentSize = new cc.Size(e.width, e.height), this._anchorPointInPoints = new cc.Point(this._contentSize.width * this._anchorPoint.x, this._contentSize.height * this._anchorPoint.y), this.setNodeDirty());
	},
	_setContentSizeByValue: function(e) {
		cc.Size.CCSizeEqualToSize(e, this._contentSize) || (this._contentSize.width = e.width, this._contentSize.height = e.height, this._anchorPointInPoints.x = this._contentSize.width * this._anchorPoint.x, this._anchorPointInPoints.y = this._contentSize.height * this._anchorPoint.y, this.setNodeDirty());
	},
	isRunning: function() {
		return this._running;
	},
	getParent: function() {
		return this._parent;
	},
	setParent: function(e) {
		this._parent = e;
	},
	isIgnoreAnchorPointForPosition: function() {
		return this._ignoreAnchorPointForPosition;
	},
	ignoreAnchorPointForPosition: function(e) {
		e != this._ignoreAnchorPointForPosition && (this._ignoreAnchorPointForPosition = e, this.setNodeDirty());
	},
	getTag: function() {
		return this._tag;
	},
	setTag: function(e) {
		this._tag = e;
	},
	getUserData: function() {
		return this._userData;
	},
	setUserData: function(e) {
		this._userData = e;
	},
	getUserObject: function() {
		return this._userObject;
	},
	setUserObject: function(e) {
		this._userObject != e && (this._userObject = e);
	},
	getShaderProgram: function() {
		return this._shaderProgram;
	},
	setShaderProgram: function(e) {
		this._shaderProgram != e && (this._shaderProgram = e);
	},
	getOrderOfArrival: function() {
		return this._orderOfArrival;
	},
	setOrderOfArrival: function(e) {
		this._orderOfArrival = e;
	},
	getGLServerState: function() {
		return this._glServerState;
	},
	setGLServerState: function(e) {
		this._glServerState = e;
	},
	getActionManager: function() {
		return this._actionManager || (this._actionManager = cc.Director.getInstance().getActionManager(), this.getActionManager = function() {
			return this._actionManager;
		}), this._actionManager;
	},
	setActionManager: function(e) {
		this._actionManager != e && (this.stopAllActions(), this._shaderProgram = e);
	},
	getScheduler: function() {
		return this._scheduler || (this._scheduler = cc.Director.getInstance().getScheduler(), this.getScheduler = function() {
			return this._scheduler;
		}), this._scheduler;
	},
	setScheduler: function(e) {
		this._scheduler != e && (this.unscheduleAllCallbacks(), this._scheduler = e);
	},
	getBoundingBox: function() {
		var e = cc.rect(0, 0, this._contentSize.width, this._contentSize.height);
		return cc.RectApplyAffineTransform(e, this.nodeToParentTransform());
	},
	getBoundingBoxToWorld: function() {
		var e = cc.rect(0, 0, this._contentSize.width, this._contentSize.height);
		e = cc.RectApplyAffineTransform(e, this.nodeToWorldTransform()), e = cc.rect(0 | e.origin.x - 4, 0 | e.origin.y - 4, 0 | e.size.width + 8, 0 | e.size.height + 8);
		if (!this._children) return e;
		for (var t = 0; t < this._children.length; t++) {
			var n = this._children[t];
			if (n && n._visible) {
				var r = n.getBoundingBoxToWorld();
				r && (e = cc.Rect.CCRectUnion(e, r));
			}
		}
		return e;
	},
	cleanup: function() {
		this.stopAllActions(), this.unscheduleAllCallbacks(), this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.cleanup);
	},
	description: function() {
		return "<cc.Node | Tag =" + this._tag + ">";
	},
	_childrenAlloc: function() {
		this._children = [];
	},
	getChildByTag: function(e) {
		cc.Assert(e != cc.NODE_TAG_INVALID, "Invalid tag");
		if (this._children != null) for (var t = 0; t < this._children.length; t++) {
			var n = this._children[t];
			if (n && n._tag == e) return n;
		}
		return null;
	},
	addChild: function(e, t, n) {
		if (e === this) {
			console.warn("cc.Node.addChild: An Node can't be added as a child of itself.");
			return;
		}
		cc.Assert(e != null, "Argument must be non-nil");
		if (e._parent !== null) {
			cc.Assert(e._parent === null, "child already added. It can't be added again");
			return;
		}
		var r = t != null ? t : e.getZOrder(),
			i = n != null ? n : e.getTag();
		e.setTag(i), this._children || this._childrenAlloc(), this._insertChild(e, r), e.setParent(this), this._running && (e.onEnter(), e.onEnterTransitionDidFinish());
	},
	removeFromParent: function(e) {
		this._parent && (e == null && (e = !0), this._parent.removeChild(this, e));
	},
	removeFromParentAndCleanup: function(e) {
		cc.log("removeFromParentAndCleanup is deprecated. Use removeFromParent instead"), this.removeFromParent(e);
	},
	removeChild: function(e, t) {
		if (this._children == null) return;
		t == null && (t = !0), this._children.indexOf(e) > -1 && this._detachChild(e, t), this.setNodeDirty();
	},
	removeChildByTag: function(e, t) {
		cc.Assert(e != cc.NODE_TAG_INVALID, "Invalid tag");
		var n = this.getChildByTag(e);
		n == null ? cc.log("cocos2d: removeChildByTag: child not found!") : this.removeChild(n, t);
	},
	removeAllChildrenWithCleanup: function(e) {
		cc.log("removeAllChildrenWithCleanup is deprecated. Use removeAllChildren instead"), this.removeAllChildren(e);
	},
	removeAllChildren: function(e) {
		if (this._children != null) {
			e == null && (e = !0);
			for (var t = 0; t < this._children.length; t++) {
				var n = this._children[t];
				n && (this._running && (n.onExitTransitionDidStart(), n.onExit()), e && n.cleanup(), n.setParent(null));
			}
			this._children.length = 0;
		}
	},
	_detachChild: function(e, t) {
		this._running && (e.onExitTransitionDidStart(), e.onExit()), t && e.cleanup(), e.setParent(null), cc.ArrayRemoveObject(this._children, e);
	},
	_insertChild: function(e, t) {
		this._reorderChildDirty = !0;
		var n = this._children[this._children.length - 1];
		if (!n || n.getZOrder() <= t) this._children.push(e);
		else for (var r = 0; r < this._children.length; r++) {
			var i = this._children[r];
			if (i && i.getZOrder() > t) {
				this._children = cc.ArrayAppendObjectToIndex(this._children, e, r);
				break;
			}
		}
		e._setZOrder(t);
	},
	reorderChild: function(e, t) {
		cc.Assert(e != null, "Child must be non-nil"), this._reorderChildDirty = !0, e.setOrderOfArrival(cc.s_globalOrderOfArrival++), e._setZOrder(t), this.setNodeDirty();
	},
	sortAllChildren: function() {
		if (this._reorderChildDirty) {
			var e, t, n = this._children.length;
			for (e = 0; e < n; e++) {
				var r = this._children[e];
				t = e - 1;
				while (t >= 0 && (r._zOrder < this._children[t]._zOrder || r._zOrder == this._children[t]._zOrder && r._orderOfArrival < this._children[t]._orderOfArrival)) this._children[t + 1] = this._children[t], t -= 1;
				this._children[t + 1] = r;
			}
			this._reorderChildDirty = !1;
		}
	},
	draw: function(e) {},
	visit: function(e) {
		if (!this._visible) return;
		var t = e || cc.renderContext,
			n;
		t.save(), this.transform(t);
		if (this._children && this._children.length > 0) {
			this.sortAllChildren();
			for (n = 0; n < this._children.length; n++) {
				if (!(this._children[n] && this._children[n]._zOrder < 0)) break;
				this._children[n].visit(t);
			}
			this.draw(t);
			if (this._children) for (; n < this._children.length; n++) this._children[n] && this._children[n]._zOrder >= 0 && this._children[n].visit(t);
		} else this.draw(t);
		this._orderOfArrival = 0, t.restore();
	},
	_visitForWebGL: function(e) {
		if (!this._visible) return;
		var t = e,
			n;
		t.save(), this._grid && this._grid.isActive() && this._grid.beforeDraw(), this.transform(t);
		if (this._children && this._children.length > 0) {
			this.sortAllChildren();
			for (n = 0; n < this._children.length; n++) {
				if (!(this._children[n] && this._children[n]._zOrder < 0)) break;
				this._children[n].visit(t);
			}
			this.draw(t);
			if (this._children) for (; n < this._children.length; n++) this._children[n] && this._children[n]._zOrder >= 0 && this._children[n].visit(t);
		} else this.draw(t);
		this._orderOfArrival = 0, this._grid && this._grid.isActive() && this._grid.afterDraw(this), t.restore();
	},
	transformAncestors: function() {
		this._parent != null && (this._parent.transformAncestors(), this._parent.transform());
	},
	transform: function(e) {
		var t = e || cc.renderContext;
		this._ignoreAnchorPointForPosition ? this._parent ? t.translate(this._position.x - this._parent._anchorPointInPoints.x + this._anchorPointInPoints.x, -(this._position.y - this._parent._anchorPointInPoints.y + this._anchorPointInPoints.y)) : t.translate(this._position.x + this._anchorPointInPoints.x, -(this._position.y + this._anchorPointInPoints.y)) : this._parent ? t.translate(this._position.x - this._parent._anchorPointInPoints.x, -(this._position.y - this._parent._anchorPointInPoints.y)) : t.translate(this._position.x, -this._position.y), this._rotation != 0 && t.rotate(this._rotationRadians), (this._scaleX != 1 || this._scaleY != 1) && t.scale(this._scaleX, this._scaleY), (this._skewX != 0 || this._skewY != 0) && t.transform(1, -Math.tan(cc.DEGREES_TO_RADIANS(this._skewY)), -Math.tan(cc.DEGREES_TO_RADIANS(this._skewX)), 1, 0, 0);
	},
	_transformForWebGL: function(e) {
		var t = e,
			n, r = this.nodeToParentTransform();
	},
	onEnter: function() {
		this._running = !0, this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.onEnter), this.resumeSchedulerAndActions();
	},
	onEnterTransitionDidFinish: function() {
		this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.onEnterTransitionDidFinish);
	},
	onExitTransitionDidStart: function() {
		this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.onExitTransitionDidStart);
	},
	onExit: function() {
		this._running = !1, this.pauseSchedulerAndActions(), this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.onExit);
	},
	runAction: function(e) {
		return cc.Assert(e != null, "Argument must be non-nil"), this.getActionManager().addAction(e, this, !this._running), e;
	},
	stopAllActions: function() {
		this.getActionManager().removeAllActionsFromTarget(this);
	},
	stopAction: function(e) {
		this.getActionManager().removeAction(e);
	},
	stopActionByTag: function(e) {
		cc.Assert(e != cc.ACTION_TAG_INVALID, "Invalid tag"), this.getActionManager().removeActionByTag(e, this);
	},
	getActionByTag: function(e) {
		return cc.Assert(e != cc.ACTION_TAG_INVALID, "Invalid tag"), this.getActionManager().getActionByTag(e, this);
	},
	numberOfRunningActions: function() {
		return this.getActionManager().numberOfRunningActionsInTarget(this);
	},
	scheduleUpdate: function() {
		this.scheduleUpdateWithPriority(0);
	},
	scheduleUpdateWithPriority: function(e) {
		this.getScheduler().scheduleUpdateForTarget(this, e, !this._running);
	},
	unscheduleUpdate: function() {
		this.getScheduler().unscheduleUpdateForTarget(this);
	},
	schedule: function(e, t, n, r) {
		t = t || 0, cc.Assert(e, "Argument must be non-nil"), cc.Assert(t >= 0, "Argument must be positive"), n = n == null ? cc.REPEAT_FOREVER : n, r = r || 0, this.getScheduler().scheduleCallbackForTarget(this, e, t, n, r, !this._running);
	},
	scheduleOnce: function(e, t) {
		this.schedule(e, 0, 0, t);
	},
	unschedule: function(e) {
		if (!e) return;
		this.getScheduler().unscheduleCallbackForTarget(this, e);
	},
	unscheduleAllCallbacks: function() {
		this.getScheduler().unscheduleAllCallbacksForTarget(this);
	},
	resumeSchedulerAndActions: function() {
		this.getScheduler().resumeTarget(this), this.getActionManager().resumeTarget(this);
	},
	pauseSchedulerAndActions: function() {
		this.getScheduler().pauseTarget(this), this.getActionManager().pauseTarget(this);
	},
	nodeToParentTransform: function() {
		if (this._transformDirty) {
			var e = this._position.x,
				t = this._position.y;
			this._ignoreAnchorPointForPosition && (e += this._anchorPointInPoints.x, t += this._anchorPointInPoints.y);
			var n = 1,
				r = 0;
			this._rotation && (n = Math.cos(-this._rotationRadians), r = Math.sin(-this._rotationRadians));
			var i = this._skewX || this._skewY;
			!i && !cc.Point.CCPointEqualToPoint(this._anchorPointInPoints, cc.p(0, 0)) && (e += n * -this._anchorPointInPoints.x * this._scaleX + -r * -this._anchorPointInPoints.y * this._scaleY, t += r * -this._anchorPointInPoints.x * this._scaleX + n * -this._anchorPointInPoints.y * this._scaleY), this._transform = cc.AffineTransformMake(n * this._scaleX, r * this._scaleX, -r * this._scaleY, n * this._scaleY, e, t);
			if (i) {
				var s = cc.AffineTransformMake(1, Math.tan(cc.DEGREES_TO_RADIANS(this._skewY)), Math.tan(cc.DEGREES_TO_RADIANS(this._skewX)), 1, 0, 0);
				this._transform = cc.AffineTransformConcat(s, this._transform), cc.Point.CCPointEqualToPoint(this._anchorPointInPoints, cc.p(0, 0)) || (this._transform = cc.AffineTransformTranslate(this._transform, -this._anchorPointInPoints.x, -this._anchorPointInPoints.y));
			}
			this._transformDirty = !1;
		}
		return this._transform;
	},
	parentToNodeTransform: function() {
		return this._inverseDirty && (this._inverse = cc.AffineTransformInvert(this.nodeToParentTransform()), this._inverseDirty = !1), this._inverse;
	},
	nodeToWorldTransform: function() {
		var e = this.nodeToParentTransform();
		for (var t = this._parent; t != null; t = t.getParent()) e = cc.AffineTransformConcat(e, t.nodeToParentTransform());
		return e;
	},
	worldToNodeTransform: function() {
		return cc.AffineTransformInvert(this.nodeToWorldTransform());
	},
	convertToNodeSpace: function(e) {
		return cc.PointApplyAffineTransform(e, this.worldToNodeTransform());
	},
	convertToWorldSpace: function(e) {
		return cc.PointApplyAffineTransform(e, this.nodeToWorldTransform());
	},
	convertToNodeSpaceAR: function(e) {
		return cc.pSub(this.convertToNodeSpace(e), this._anchorPointInPoints);
	},
	convertToWorldSpaceAR: function(e) {
		var t = cc.pAdd(e, this._anchorPointInPoints);
		return this.convertToWorldSpace(t);
	},
	_convertToWindowSpace: function(e) {
		var t = this.convertToWorldSpace(e);
		return cc.Director.getInstance().convertToUI(t);
	},
	convertTouchToNodeSpace: function(e) {
		var t = e.getLocation();
		return this.convertToNodeSpace(t);
	},
	convertTouchToNodeSpaceAR: function(e) {
		var t = e.getLocation();
		return t = cc.Director.getInstance().convertToGL(t), this.convertToNodeSpaceAR(t);
	},
	update: function(e) {},
	updateTransform: function() {
		this._arrayMakeObjectsPerformSelector(this._children, cc.Node.StateCallbackType.updateTransform);
	},
	retain: function() {},
	release: function() {}
}), cc.Node.StateCallbackType = {
	onEnter: 1,
	onExit: 2,
	cleanup: 3,
	onEnterTransitionDidFinish: 4,
	updateTransform: 5,
	onExitTransitionDidStart: 6,
	sortAllChildren: 7
}, cc.Node.create = function() {
	return new cc.Node();
};
cc.AtlasNode = cc.Node.extend({
	RGBAProtocol: !0,
	_itemsPerRow: 0,
	_itemsPerColumn: 0,
	_itemWidth: 0,
	_itemHeight: 0,
	_colorUnmodified: cc.c3b(0, 0, 0),
	_textureAtlas: null,
	_isOpacityModifyRGB: !1,
	_blendFunc: {
		src: cc.BLEND_SRC,
		dst: cc.BLEND_DST
	},
	_opacity: 0,
	_color: null,
	_originalTexture: null,
	_quadsToDraw: 0,
	_uniformColor: 0,
	initWithTileFile: function(e, t, n, r) {
		cc.Assert(e != null, "title should not be null"), this._itemWidth = t, this._itemHeight = n, this._opacity = 255, this._color = cc.white(), this._colorUnmodified = cc.white(), this._isOpacityModifyRGB = !0, this._blendFunc.src = cc.BLEND_SRC, this._blendFunc.dst = cc.BLEND_DST;
		var i = new cc.TextureAtlas();
		return i.initWithFile(e, r), this.setTextureAtlas(i), cc.renderContextType == cc.CANVAS && (this._originalTexture = this._textureAtlas.getTexture()), this._textureAtlas ? (this._updateBlendFunc(), this._updateOpacityModifyRGB(), this._calculateMaxItems(), this._quadsToDraw = r, !0) : (cc.log("cocos2d: Could not initialize cc.AtlasNode. Invalid Texture."), !1);
	},
	updateAtlasValues: function() {
		cc.Assert(!1, "cc.AtlasNode:Abstract updateAtlasValue not overriden");
	},
	draw: function(e) {
		this._super(), cc.renderContextType == cc.CANVAS;
	},
	getColor: function() {
		return this._isOpacityModifyRGB ? this._colorUnmodified : this._color;
	},
	setColor: function(e) {
		if (this._color.r == e.r && this._color.g == e.g && this._color.b == e.b) return;
		this._color = this._colorUnmodified = e;
		if (this.getTexture() && cc.renderContextType == cc.CANVAS) {
			var t = cc.TextureCache.getInstance().getTextureColors(this._originalTexture);
			if (t) {
				var n = this._originalTexture,
					r = cc.rect(0, 0, n.width, n.height),
					i = cc.generateTintImage(n, t, this._color, r),
					s = new Image();
				s.src = i.toDataURL(), s.crossOrigin = "anonymous";
				this.setTexture(s);
			}
		}
		this._isOpacityModifyRGB && (this._color.r = e.r * this._opacity / 255, this._color.g = e.g * this._opacity / 255, this._color.b = e.b * this._opacity / 255);
	},
	getOpacity: function() {
		return this._opacity;
	},
	setOpacity: function(e) {
		this._opacity = e;
	},
	setOpacityModifyRGB: function(e) {
		var t = this._color;
		this._isOpacityModifyRGB = e, this._color = t;
	},
	isOpacityModifyRGB: function() {
		return this._isOpacityModifyRGB;
	},
	getBlendFunc: function() {
		return this._blendFunc;
	},
	setBlendFunc: function(e, t) {
		arguments.length == 1 ? this._blendFunc = e : this._blendFunc = {
			src: e,
			dst: t
		};
	},
	getTexture: function() {
		return this._textureAtlas.getTexture();
	},
	setTexture: function(e) {
		this._textureAtlas.setTexture(e), this._updateBlendFunc(), this._updateOpacityModifyRGB();
	},
	setTextureAtlas: function(e) {
		this._textureAtlas = e;
	},
	getTextureAtlas: function() {
		return this._textureAtlas;
	},
	getQuadsToDraw: function() {
		return this._quadsToDraw;
	},
	setQuadsToDraw: function(e) {
		this._quadsToDraw = e;
	},
	_calculateMaxItems: function() {
		var e;
		this._textureAtlas.getTexture() instanceof cc.Texture2D ? e = this._textureAtlas.getTexture().getContentSize() : e = cc.size(this._textureAtlas.getTexture().width, this._textureAtlas.getTexture().height), this._itemsPerColumn = parseInt(e.height / this._itemHeight), this._itemsPerRow = parseInt(e.width / this._itemWidth);
	},
	_updateBlendFunc: function() {},
	_updateOpacityModifyRGB: function() {}
}), cc.AtlasNode.create = function(e, t, n, r) {
	var i = new cc.AtlasNode();
	return i.initWithTileFile(e, t, n, r) ? i : null;
};

function _ccTexParams(e, t, n, r) {
	this.minFilter = e, this.magFilter = t, this.wrapS = n, this.wrapT = r;
}
var cc = cc = cc || {};
cc.TEXTURE_PIXELFORMAT_AUTOMATIC = 0, cc.TEXTURE_PIXELFORMAT_RGBA8888 = 1, cc.TEXTURE_PIXELFORMAT_RGB888 = 2, cc.TEXTURE_PIXELFORMAT_RGB565 = 3, cc.TEXTURE_PIXELFORMAT_A8 = 4, cc.TEXTURE_PIXELFORMAT_I8 = 5, cc.TEXTURE_PIXELFORMAT_AI88 = 6, cc.TEXTURE_PIXELFORMAT_RGBA4444 = 7, cc.TEXTURE_PIXELFORMAT_RGB5A1 = 8, cc.TEXTURE_PIXELFORMAT_PVRTC4 = 9, cc.TEXTURE_PIXELFORMAT_PVRTC2 = 10, cc.TEXTURE_PIXELFORMAT_DEFAULT = cc.TEXTURE_PIXELFORMAT_RGBA8888, cc.ENABLE_CACHE_TEXTTURE_DATA, cc.g_defaultAlphaPixelFormat = cc.TEXTURE_PIXELFORMAT_DEFAULT, cc.PVRHaveAlphaPremultiplied_ = !1, cc.Texture2D = cc.Class.extend({
	_pVRHaveAlphaPremultiplied: null,
	_pixelFormat: null,
	_pixelsWide: null,
	_pixelsHigh: null,
	_name: null,
	_contentSize: null,
	_maxS: null,
	_maxT: null,
	_hasPremultipliedAlpha: null,
	ctor: function() {
		cc.SUPPORT_PVRTC && (this.initWithPVRTCData = function(e, t, n, r, i, s) {
			if (!cc.Configuration.getInstance().isSupportsPVRTC()) return cc.log("cocos2d: WARNING: PVRTC images is not supported."), !1;
			this.setAntiAliasTexParameters();
			var o, u = new cc.GLsizei();
			return u = i * i * n / 8, r ? o = n == 4 ? gl.COMPRESSED_RGBA_PVRTC_4BPPV1_IMG : gl.COMPRESSED_RGBA_PVRTC_2BPPV1_IMG : o = n == 4 ? gl.COMPRESSED_RGB_PVRTC_4BPPV1_IMG : gl.COMPRESSED_RGB_PVRTC_2BPPV1_IMG, u < 32 && (u = 32), this._contentSize = cc.size(i, i), this._pixelsWide = i, this._pixelsHigh = i, this._maxS = 1, this._maxT = 1, this._hasPremultipliedAlpha = cc.PVRHaveAlphaPremultiplied_, this._pixelFormat = s, !0;
		});
	},
	getPixelFormat: function() {
		return this._pixelFormat;
	},
	getPixelsWide: function() {
		return this._pixelsWide;
	},
	getPixelsHigh: function() {
		return this._pixelsHigh;
	},
	getName: function() {
		return this._name;
	},
	getContentSizeInPixels: function() {
		var e = cc.size(0, 0);
		return e.width = this._contentSize.width / cc.CONTENT_SCALE_FACTOR(), e.height = this._contentSize.height / cc.CONTENT_SCALE_FACTOR(), e;
	},
	getMaxS: function() {
		return this._maxS;
	},
	setMaxS: function(e) {
		this._maxS = e;
	},
	getMaxT: function() {
		return this._maxT;
	},
	setMaxT: function(e) {
		this._maxT = e;
	},
	getHasPremultipliedAlpha: function() {
		return this._hasPremultipliedAlpha;
	},
	description: function() {
		var e = "<cc.Texture2D | Name = " + this._name + " | Dimensions = " + this._pixelsWide + " x " + this._pixelsHigh + " | Coordinates = (" + this._maxS + ", " + this._maxT + ")>";
		return e;
	},
	releaseData: function(e) {
		cc.free(e);
	},
	keepData: function(e, t) {
		return e;
	},
	initWithData: function(e, t, n, r) {
		this.setAntiAliasTexParameters();
		switch (e) {
		case cc.TEXTURE_PIXELFORMAT_RGBA8888:
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB888:
			break;
		case cc.TEXTURE_PIXELFORMAT_RGBA4444:
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB5A1:
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB565:
			break;
		case cc.TEXTURE_PIXELFORMAT_AI88:
			break;
		case cc.TEXTURE_PIXELFORMAT_A8:
			break;
		default:
			cc.Assert(0, "NSInternalInconsistencyException");
		}
		return this._contentSize = r, this._pixelsWide = t, this._pixelsHigh = n, this._pixelFormat = e, this._maxS = r.width / t, this._maxT = r.height / n, this._hasPremultipliedAlpha = !1, !0;
	},
	drawAtPoint: function(e) {
		var t = [0, this._maxT, this._maxS, this._maxT, 0, 0, this._maxS, 0],
			n = this._pixelsWide * this._maxS,
			r = this._pixelsHigh * this._maxT,
			i = [e.x, e.y, 0, n + e.x, e.y, 0, e.x, r + e.y, 0, n + e.x, r + e.y, 0];
	},
	drawInRect: function(e) {
		var t = [0, this._maxT, this._maxS, this._maxT, 0, 0, this._maxS, 0],
			n = [e.origin.x, e.origin.y, e.origin.x + e.size.width, e.origin.y, e.origin.x, e.origin.y + e.size.height, e.origin.x + e.size.width, e.origin.y + e.size.height];
	},
	initWithImage: function(e) {
		var t, n;
		if (e == null) return cc.log("cocos2d: cc.Texture2D. Can't create Texture. UIImage is nil"), !1;
		var r = cc.Configuration.getInstance();
		cc.TEXTURE_NPOT_SUPPORT ? r.isSupportsNPOT() && (t = e.getWidth(), n = e.getHeight()) : (t = cc.NextPOT(e.getWidth()), n = cc.NextPOT(e.getHeight()));
		var i = r.getMaxTextureSize();
		return n > i || t > i ? (cc.log("cocos2d: WARNING: Image (%u x %u) is bigger than the supported %u x %u", t, n, i, i), null) : this._initPremultipliedATextureWithImage(e, t, n);
	},
	initWithString: function(e, t, n, r, i) {
		arguments.length == 3 && (r = arguments[1], i = arguments[2], t = cc.size(0, 0), n = cc.TEXT_ALIGNMENT_CENTER), cc.ENABLE_CACHE_TEXTTURE_DATA && cc.VolatileTexture.addStringTexture(this, e, t, n, r, i);
		var s = new cc.Image();
		return eAlign = new cc.Image.ETextAlign(), eAlign = cc.TEXT_ALIGNMENT_CENTER == n ? cc.Image.ALIGN_CENTER : cc.TEXT_ALIGNMENT_LEFT == n ? cc.Image.ALIGN_LEFT : cc.Image.ALIGN_RIGHT, s.initWithString(e, t.width, t.height, eAlign, r, i) ? this.initWithImage(s) : !1;
	},
	initWithPVRFile: function(e) {
		var t = !1,
			n = new cc.TexturePVR();
		return t = n.initWithContentsOfFile(e), t ? (n.setRetainName(!0), this._name = n.getName(), this._maxS = 1, this._maxT = 1, this._pixelsWide = n.getWidth(), this._pixelsHigh = n.getHeight(), this._contentSize = cc.size(this._pixelsWide, this._pixelsHigh), this._hasPremultipliedAlpha = cc.PVRHaveAlphaPremultiplied_, this._pixelFormat = n.getFormat(), this.setAntiAliasTexParameters()) : cc.log("cocos2d: Couldn't load PVR image %s", e), t;
	},
	setTexParameters: function(e) {
		cc.Assert(this._pixelsWide == cc.NextPOT(this._pixelsWide) && this._pixelsHigh == cc.NextPOT(this._pixelsHigh) || e.wrapS == gl.CLAMP_TO_EDGE && e.wrapT == gl.CLAMP_TO_EDGE, "gl.CLAMP_TO_EDGE should be used in NPOT textures");
	},
	setAntiAliasTexParameters: function() {
		var e = [gl.LINEAR, gl.LINEAR, gl.CLAMP_TO_EDGE, gl.CLAMP_TO_EDGE];
		this.setTexParameters(e);
	},
	setAliasTexParameters: function() {
		var e = [gl.NEAREST, gl.NEAREST, gl.CLAMP_TO_EDGE, gl.CLAMP_TO_EDGE];
		this.setTexParameters(e);
	},
	generateMipmap: function() {
		cc.Assert(this._pixelsWide == cc.NextPOT(this._pixelsWide) && this._pixelsHigh == cc.NextPOT(this._pixelsHigh), "Mimpap texture only works in POT textures");
	},
	bitsPerPixelForFormat: function() {
		var e = 0;
		switch (this._pixelFormat) {
		case cc.TEXTURE_PIXELFORMAT_RGBA8888:
			e = 32;
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB565:
			e = 16;
			break;
		case cc.TEXTURE_PIXELFORMAT_A8:
			e = 8;
			break;
		case cc.TEXTURE_PIXELFORMAT_RGBA4444:
			e = 16;
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB5A1:
			e = 16;
			break;
		case cc.TEXTURE_PIXELFORMAT_PVRTC4:
			e = 4;
			break;
		case cc.TEXTURE_PIXELFORMAT_PVRTC2:
			e = 2;
			break;
		case cc.TEXTURE_PIXELFORMAT_I8:
			e = 8;
			break;
		case cc.TEXTURE_PIXELFORMAT_AI88:
			e = 16;
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB888:
			e = 24;
			break;
		default:
			e = -1, cc.Assert(!1, "illegal pixel format"), cc.log("bitsPerPixelForFormat: %d, cannot give useful result", this._pixelFormat);
		}
		return e;
	},
	_initPremultipliedATextureWithImage: function(e, t, n) {
		var r = null,
			i = null,
			s = null,
			o = null,
			u, a = cc.size(0, 0),
			f = new cc.Texture2DPixelFormat(),
			l = new cc.size_t();
		u = e.hasAlpha(), l = e.getBitsPerComponent(), u ? f = cc.g_defaultAlphaPixelFormat : l >= 8 ? f = cc.TEXTURE_PIXELFORMAT_RGB888 : (cc.log("cocos2d: cc.Texture2D: Using RGB565 texture since image has no alpha"), f = cc.TEXTURE_PIXELFORMAT_RGB565), a = cc.size(e.getWidth(), e.getHeight());
		switch (f) {
		case cc.TEXTURE_PIXELFORMAT_RGBA8888:
		case cc.TEXTURE_PIXELFORMAT_RGBA4444:
		case cc.TEXTURE_PIXELFORMAT_RGB5A1:
		case cc.TEXTURE_PIXELFORMAT_RGB565:
		case cc.TEXTURE_PIXELFORMAT_A8:
			i = e.getData(), cc.Assert(i != null, "null image data.");
			if (e.getWidth() == t && e.getHeight() == n) r = new(n * t * 4)(), cc.memcpy(r, i, n * t * 4);
			else {
				r = new(n * t * 4)();
				var c = i,
					h = r,
					p = e.getHeight();
				for (var d = 0; d < p; ++d) cc.memcpy(h + t * 4 * d, c + e.getWidth() * 4 * d, e.getWidth() * 4);
			}
			break;
		case cc.TEXTURE_PIXELFORMAT_RGB888:
			i = e.getData(), cc.Assert(i != null, "null image data.");
			if (e.getWidth() == t && e.getHeight() == n) r = new(n * t * 3)(), cc.memcpy(r, i, n * t * 3);
			else {
				r = new(n * t * 3)();
				var c = i,
					h = r,
					p = e.getHeight();
				for (var d = 0; d < p; ++d) cc.memcpy(h + t * 3 * d, c + e.getWidth() * 3 * d, e.getWidth() * 3);
			}
			break;
		default:
			cc.Assert(0, "Invalid pixel format");
		}
		if (f == cc.TEXTURE_PIXELFORMAT_RGB565) {
			i = new(n * t * 2)(), s = r, o = i;
			var v = t * n;
			for (var m = 0; m < v; ++m, ++s) o++, o = (s >> 0 & 255) >> 3 << 11 | (s >> 8 & 255) >> 2 << 5 | (s >> 16 & 255) >> 3 << 0;
			delete r, r = i;
		} else if (f == cc.TEXTURE_PIXELFORMAT_RGBA4444) {
			i = new(n * t * 2)(), s = r, o = i;
			var v = t * n;
			for (var m = 0; m < v; ++m, ++s) o++, o = (s >> 0 & 255) >> 4 << 12 | (s >> 8 & 255) >> 4 << 8 | (s >> 16 & 255) >> 4 << 4 | (s >> 24 & 255) >> 4 << 0;
			delete r, r = i;
		} else if (f == cc.TEXTURE_PIXELFORMAT_RGB5A1) {
			i = new(n * t * 2)(), s = r, o = i;
			var v = t * n;
			for (var m = 0; m < v; ++m, ++s) o++, o = (s >> 0 & 255) >> 3 << 11 | (s >> 8 & 255) >> 3 << 6 | (s >> 16 & 255) >> 3 << 1 | (s >> 24 & 255) >> 7 << 0;
			delete r, r = i;
		} else f == cc.TEXTURE_PIXELFORMAT_A8 && (f = cc.TEXTURE_PIXELFORMAT_RGBA8888);
		return r && (this.initWithData(r, f, t, n, a), this._hasPremultipliedAlpha = e.isPremultipliedAlpha(), delete r), !0;
	}
}), cc.Texture2D.setDefaultAlphaPixelFormat = function(e) {
	cc.g_defaultAlphaPixelFormat = e;
}, cc.Texture2D.defaultAlphaPixelFormat = function() {
	return cc.g_defaultAlphaPixelFormat;
}, cc.Texture2D.PVRImagesHavePremultipliedAlpha = function(e) {
	cc.PVRHaveAlphaPremultiplied_ = e;
};
cc.g_sharedTextureCache = null, cc.loadImage = function(e) {
	var t = cc.computeImageFormatType(e);
	if (t == cc.FMT_UNKNOWN) {
		cc.log("unsupported format" + e);
		return;
	}
	var n = new Image();
	n.src = e, n.onLoad = function(t) {
		cc.TextureCache.getInstance().cacheImage(e, n);
	};
}, cc.computeImageFormatType = function(e) {
	return e.toLowerCase().indexOf(".jpg") > 0 || e.toLowerCase().indexOf(".jpeg") > 0 ? cc.FMT_JPG : e.indexOf(".png") > 0 || e.indexOf(".PNG") > 0 ? cc.FMT_PNG : cc.FMT_UNKNOWN;
}, cc.TextureCache = cc.Class.extend({
	textures: {},
	_textureColorsCache: {},
	_textureKeySeq: 1e3,
	ctor: function() {
		cc.Assert(cc.g_sharedTextureCache == null, "Attempted to allocate a second instance of a singleton."), this._textureKeySeq += 0 | Math.random() * 1e3;
	},
	addImageAsync: function(e, t, n) {
		cc.Assert(e != null, "TextureCache: path MUST not be null"), e = cc.FileUtils.getInstance().fullPathFromRelativePath(e);
		var r = this.textures[e.toString()];
		if (r) this._addImageAsyncCallBack(t, n);
		else {
			r = new Image();
			var i = this;
			r.addEventListener("load", function() {
				i._addImageAsyncCallBack(t, n);
			}), r.src = e, this.textures[e.toString()] = r;
		}
		if (cc.renderContextType == cc.CANVAS) return this.textures[e.toString()];
	},
	_addImageAsyncCallBack: function(e, t) {
		e && typeof t == "string" ? e[t]() : e && typeof t == "function" && t.call(e);
	},
	addPVRTCImage: function() {
		cc.Assert(0, "TextureCache:addPVRTCImage does not support");
	},
	description: function() {
		return "<TextureCache | Number of textures = " + this.textures.length + ">";
	},
	addImage: function(e) {
		cc.Assert(e != null, "TextureCache: path MUST not be null"), e = cc.FileUtils.getInstance().fullPathForFilename(e);
		var t = this.textures[e.toString()];
		if (t) cc.Loader.getInstance().onResLoaded();
		else {
			t = new Image();
			var n = this;
			t.addEventListener("load", function() {
				cc.Loader.getInstance().onResLoaded();
			}), t.addEventListener("error", function() {
				cc.Loader.getInstance().onResLoadingErr(e);
			}), t.src = e, this.textures[e.toString()] = t;
		}
		return cc.renderContextType === cc.CANVAS ? this.textures[e.toString()] : null;
	},
	cacheImage: function(e, t) {
		this.textures[e.toString()] || (this.textures[e.toString()] = t);
	},
	addUIImage: function(e, t) {
		cc.Assert(e != null, "TextureCache: image MUST not be nulll");
		var n = null;
		if (t && this.textures.hasOwnProperty(t)) {
			n = this.textures[t];
			if (n) return n;
		}
		return n = new cc.Texture2D(), n.initWithImage(e), t != null && n != null ? this.textures[t] = n : cc.log("cocos2d: Couldn't add UIImage in TextureCache"), n;
	},
	textureForKey: function(e) {
		return this.textures.hasOwnProperty(e) ? this.textures[e] : null;
	},
	getKeyByTexture: function(e) {
		for (var t in this.textures) if (this.textures[t] == e) return t;
		return null;
	},
	_generalTextureKey: function() {
		return this._textureKeySeq++, "_textureKey_" + this._textureKeySeq;
	},
	getTextureColors: function(e) {
		var t = this.getKeyByTexture(e);
		return t || (e instanceof HTMLImageElement ? t = e.src : t = this._generalTextureKey()), this._textureColorsCache.hasOwnProperty(t) || (this._textureColorsCache[t] = cc.generateTextureCacheForColor(e)), this._textureColorsCache[t];
	},
	removeAllTextures: function() {
		this.textures = {};
	},
	removeTexture: function(e) {
		if (!e) return;
		for (var t in this.textures) if (this.textures[t] == e) {
			delete this.textures[t];
			return;
		}
	},
	removeTextureForKey: function(e) {
		if (e == null) return;
		this.textures[e] && delete this.textures[e];
	},
	dumpCachedTextureInfo: function() {
		var e = 0,
			t = 0;
		for (var n in this.textures) {
			var r = this.textures[n],
				i = r.bitsPerPixelForFormat(),
				s = r.getPixelsWide() * r.getPixelsHigh() * i / 8;
			t += s, e++, cc.log("cocos2d: '" + r.toString() + "' id=" + r.getName() + " " + r.getPixelsWide() + " x " + r.getPixelsHigh() + " @ " + i + " bpp => " + s / 1024 + " KB");
		}
		cc.log("cocos2d: TextureCache dumpDebugInfo: " + e + " textures, for " + t / 1024 + " KB (" + (t / 1048576).toFixed(2) + " MB)");
	},
	addPVRImage: function(e) {
		cc.Assert(e != null, "TextureCache: file image MUST not be null"), e = cc.FileUtils.getInstance().fullPathFromRelativePath(e);
		var t = e;
		if (this.textures[t] != null) return this.textures[t];
		var n = new cc.Texture2D();
		return n.initWithPVRFile(t) ? this.textures[t] = n : cc.log("cocos2d: Couldn't add PVRImage:" + t + " in TextureCache"), n;
	}
}), cc.TextureCache.getInstance = function() {
	return cc.g_sharedTextureCache || (cc.g_sharedTextureCache = new cc.TextureCache()), cc.g_sharedTextureCache;
}, cc.TextureCache.purgeSharedTextureCache = function() {
	cc.g_sharedTextureCache = null;
};
cc.TextureAtlas = cc.Class.extend({
	_indices: [],
	_buffersVBO: [0, 1],
	_dirty: !1,
	_capacity: 0,
	_texture: null,
	_quads: [],
	getTotalQuads: function() {
		return this._quads.length;
	},
	getCapacity: function() {
		return this._capacity;
	},
	getTexture: function() {
		return this._texture;
	},
	setTexture: function(e) {
		this._texture = e;
	},
	getQuads: function() {
		return this._quads;
	},
	setQuads: function(e) {
		this._quads = e;
	},
	description: function() {
		return "<CCTextureAtlas | totalQuads =" + this._totalQuads + ">";
	},
	_initIndices: function() {
		if (this._capacity == 0) return;
		for (var e = 0; e < this._capacity; e++) this._indices[e * 6 + 0] = e * 4 + 0, this._indices[e * 6 + 1] = e * 4 + 0, this._indices[e * 6 + 2] = e * 4 + 2, this._indices[e * 6 + 3] = e * 4 + 1, this._indices[e * 6 + 4] = e * 4 + 3, this._indices[e * 6 + 5] = e * 4 + 3;
	},
	initWithFile: function(e, t) {
		var n = cc.TextureCache.getInstance().addImage(e);
		return n ? this.initWithTexture(n, t) : (cc.log("cocos2d: Could not open file: " + e), null);
	},
	initWithTexture: function(e, t) {
		return cc.Assert(e != null, "TextureAtlas.initWithTexture():texture should not be null"), this._capacity = t, this._texture = e, cc.Assert(this._quads.length == 0 && this._indices.length == 0, "TextureAtlas.initWithTexture():_quads and _indices should not be null"), this._quads = [], this._indices = [], (!this._quads || !this._indices) && this._capacity > 0 ? !1 : (this._dirty = !0, this._initIndices(), !0);
	},
	updateQuad: function(e, t) {
		this._quads[t] = e, this._dirty = !0;
	},
	insertQuad: function(e, t) {
		this._quads = cc.ArrayAppendObjectToIndex(this._quads, e, t), this._dirty = !0;
	},
	insertQuadFromIndex: function(e, t) {
		if (e == t) return;
		var n = this._quads[e];
		cc.ArrayRemoveObjectAtIndex(this._quads, e), e > t ? this._quads = cc.ArrayAppendObjectToIndex(this._quads, n, t) : this._quads = cc.ArrayAppendObjectToIndex(this._quads, n, t - 1), this._dirty = !0;
	},
	removeQuadAtIndex: function(e) {
		cc.ArrayRemoveObjectAtIndex(this._quads, e), this._dirty = !0;
	},
	removeAllQuads: function() {
		this._quads.length = 0;
	},
	resizeCapacity: function(e) {
		return e == this._capacity ? !0 : (this._totalQuads = Math.min(this._totalQuads, e), this._capacity = e, !0);
	},
	drawNumberOfQuads: function(e, t) {
		if (0 == e) return;
	},
	drawQuads: function() {
		this.drawNumberOfQuads(this._quads.length, 0);
	}
}), cc.TextureAtlas.create = function(e, t) {
	var n = new cc.TextureAtlas();
	return n && n.initWithFile(e, t) ? n : null;
}, cc.TextureAtlas.createWithTexture = function(e, t) {
	var n = new cc.TextureAtlas();
	return n && n.initWithTexture(e, t) ? n : null;
};