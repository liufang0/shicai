(function() {
	var e = ["/Public/main/ft/cocos1.js", "/Public/main/ft/cocos2.js", "/Public/main/ft/cocos3.js"],
		t = document,
		n = t.ccConfig;
	if (!n.engineDir)e = [];
	else {
		if (n.box2d || n.chipmunk) e.push("Draw_Nodes/CCDrawNode.js"), e.push("physics_nodes/CCPhysicsSprite.js"), e.push("physics_nodes/CCPhysicsDebugNode.js"), n.box2d && e.push("../box2d/box2d.js"), n.chipmunk && e.push("../chipmunk/chipmunk.js");
		e.forEach(function(t, r) {
			e[r] = n.engineDir + t
		})
	}
	var r = 0,
		i = e.concat(n.appFiles);
	i.push("/Public/main/ft/main.js");
	if (navigator.userAgent.indexOf("Trident/5") > -1) {
		this.serial = -1;
		var s = function() {
				var e = this.serial + 1;
				if (e < i.length) {
					var n = t.createElement("script");
					n.src = i[e], n.serial = e, n.onload = s, t.body.appendChild(n), p = e / (i.length - 1)
				}
			};
		s()
	} else i.forEach(function(e, n) {
		var s = t.createElement("script");
		s.async = !1, s.src = e, s.onload = function() {
			r++, p = r / i.length
		}, t.body.appendChild(s), i[n] = s
	})
})()