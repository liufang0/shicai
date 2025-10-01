!
    function(i) {
        var t = {
            bootstrap: function() {
                this.addEvents(), this.setModel(), this.initialize()
            },
            addEvents: function() {
                var i = this;
                $.each(this.events, function(t, e) {
                    var n = t.split(" "),
                        o = n[0],
                        a = n[1],
                        c = e;
                    $("#main").on(o, a, function(t) {
                        i[c](t)
                    })
                })
            },
            setModel: function() {
                var t = function(t) {
                        i.Ajax(t)
                    },
                    e = {};
                $.each(this.models, function(k, v) {
                    e[k] = function(data) {
                        data.url = v, t(data)
                    }
                }), this.Model = e
            },
            models: {
                apply: "/index.php/Home/Login/dologin"
            },
            events: {
                "click #img-code": "refreshCode",
                "click #apply": "apply",
                "click #wxsq": "wxLogin"
            },
            initialize: function() {},
            wxLogin:function(){
                var domain = "http://"+window.location.host;

                //window.location.href = "http://test.taobaote.cn/index.php/Home/Fase/wx_oauth2?u=http://test.octoinbi.net.cn/index.php/Home/Login/index";
                window.location.href = "http://api.thehengyou.com/index.php/Home/Fase/wx_oauth2?u="+domain+"/index.php/Home/Login/index";
            },
            refreshCode: function() {
                var i = $("input[name=pic_code]"),
                    t = $("#img-code");
                i.val(""), t.attr("src", "/index.php/Home/Login/getcode?v=" + (new Date).getTime())
            },
            apply: function() {
                var t = this.Model,
                    e = $("#login-form"),
                    n = this.checkForm(),
                    o = this;
                n && (i.loading = weui.loading("提交中..."), t.apply({
                    data: e.serialize(),
                    success: function(i) {
                        window.location.href = i.jump
                    },
                    error: function(i) {
                        weui.alert(i, function() {
                            o.refreshCode()
                        })
                    }
                }))
            },
            checkForm: function() {
                return "" == $.trim($("input[name=account]").val()) ? (weui.alert("请填写您的账号"), !1) :
                       "" == $.trim($("input[name=password]").val()) ? (weui.alert("请填写密码！"), !1) :
                        "" == $.trim($("input[name=pic_code]").val()) || (weui.alert("请填写图片验证码！"), !1)
            }
        };
        t.bootstrap();
        var isHasLogin = $("#isHasLogin").val();
        if(isHasLogin==1){
            weui.alert("您的账户登入超时，请重新登录您的账户！", function() {
                // window.location.href = "/";
            })
        }
    }(this);