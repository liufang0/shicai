!
    function(t) {
        t.Ajax = function(e) {
            var a = {
                url: e.url,
                data: e.data,
                type: e.type || "post",
                dataType: e.dataType || "json",
                timeout: e.timeout || 3e4,
                cache: !1,
                success: function(a, o, i) {
                    t.loading && t.loading.hide(), 200 == a.code ? "function" == typeof e.success && e.success(a.data) : 300 == a.code ? weui.alert(a.msg, function() {
                        // window.location.replace(a.jump)
                    }) : "function" == typeof e.error ? e.error(a.msg) : weui.alert(a.msg)
                },
                error: function(a, o) {
                    t.loading && t.loading.hide(), "abort" != o && ("timeout" == o ? weui.alert("请求超时,请检查网络后重试!") : "function" == typeof e.AjaxError ? e.AjaxError() : weui.alert("系统错误,请稍后后重试!"))
                }
            };
            t.ajaxStatus = $.ajax(a)
        }
    }(this);