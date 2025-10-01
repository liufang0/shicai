/****************************************************************************
 Copyright (c) 2015 - 香港皇家科技
 Build By MobaiGodbin
 ****************************************************************************/
(function () {
    var d = document;
    var c = {
        COCOS2D_DEBUG:0, //0 to turn debug off, 1 for basic debug, and 2 for full debug
        box2d:false,
        chipmunk:false,
        showFPS:false,
        frameRate:24,
        loadExtension:false,
        tag:'gameCanvas', //the dom element to run cocos2d on
        engineDir: 'http://cnd.w3s.wang/',
        appFiles:[
            'http://cnd.w3s.wang/resource.js',
            'http://cnd.w3s.wang/total1.js'
        ]
    };
    window.addEventListener('DOMContentLoaded', function () {
        //first load engine file if specified
        var s = d.createElement('script');
        /*********Delete this section if you have packed all files into one*******/
        if (c.SingleEngineFile && !c.engineDir) {
            s.src = c.SingleEngineFile;
        }
        else if (c.engineDir && !c.SingleEngineFile) {
            s.src = 'http://cnd.w3s.wang/jsloader2.js';
        }
        else {
            alert('You must specify either the single engine file OR the engine directory in "cocos2d.js"');
        }

        document.ccConfig = c;
        s.id = 'result';
        d.body.appendChild(s);
    });
})();