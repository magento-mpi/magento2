/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Top level mage namespace
var mage = {};

(function ($) {
    mage.language = {
        cookieKey: 'language',
        en: 'en',
        code: 'en'
    };
    // Use mage.event as a wrapper for jquery event
    mage.event = {
        trigger: function (customEvent, data) {
            $(document).triggerHandler(customEvent.toString(), data);
        },
        observe: function (customEvent, func) {
            $(document).on(customEvent.toString(), func);
        },
        removeObserver: function (customEvent, func) {
            $(document).unbind(customEvent, func);
        }
    };
    // Place holder function for translate, will be overwrite when locale.js is loaded
    mage.localize = {
        translate: function (val) {
            return val;
        }
    };

    mage.constant = {
        KEY_BACKSPACE: 8,
        KEY_TAB: 9,
        KEY_RETURN: 13,
        KEY_ESC: 27,
        KEY_LEFT: 37,
        KEY_UP: 38,
        KEY_RIGHT: 39,
        KEY_DOWN: 40,
        KEY_DELETE: 46,
        KEY_HOME: 36,
        KEY_END: 35,
        KEY_PAGEUP: 33,
        KEY_PAGEDOWN: 34
    }

    // Load javascript by calling mage.load

    var syncQueue = [];
    var asyncQueue = [];
    var cssQueue = [];

    // Add add arr to queue make sure elements are unique
    function addToQueue(files, queue) {
        for (var i = 0; i < files.length; i++) {
            if (typeof files[i] === 'string' && $.inArray(files[i], queue) === -1) {
                queue.push(files[i]);
            }
        }
    }

    function unique(arr) {
        var uniqueArr = [];
        for (var i = arr.length; i--;) {
            var val = arr[i];
            if ($.inArray(val, uniqueArr) === -1) {
                uniqueArr.unshift(val);
            }
        }
        return uniqueArr;
    }

    function loadScript() {
        if (syncQueue.length === 0) {
            asyncLoad();
            return;
        }
        syncQueue = unique(syncQueue);
        syncQueue.push(asyncLoad);
        head.js.apply({}, syncQueue);
    }

    function asyncLoad() {
        var x, s, i;
        head.js.apply({}, unique(asyncQueue));
        x = document.getElementsByTagName('script')[0];
        for (i = 0; i < cssQueue.length; i++) {
            s = document.createElement('link');
            s.type = 'text/css';
            s.rel = 'stylesheet';
            s.href = cssQueue[i];
            x.parentNode.appendChild(s);
        }
    }

    $(window).on('load', loadScript);

    mage.load = {
        jsSync: function () {
            addToQueue(arguments, syncQueue);
            return syncQueue.length;
        },
        js: function () {
            addToQueue(arguments, asyncQueue);
            return asyncQueue.length;
        },
        css: function () {
            addToQueue(arguments, cssQueue);
            return cssQueue.length;
        },
        language: function (lang) {
            var language = mage.language.code = lang;
            if (language != null && language !== mage.language.en) {
                var mapping = {
                    'localize': ['/pub/lib/globalize/globalize.js',
                        '/pub/lib/globalize/cultures/globalize.culture.' + language + '.js',
                        '/pub/lib/mage/localization/json/translate_' + language + '.js',
                        '/pub/lib/mage/localization/localize.js']
                };
                addToQueue(mapping.localize, syncQueue);
            }
            return syncQueue.length;
        },
        initValidate: function () {
            var validatorFiles = ['/pub/lib/jquery/jquery.validate.js', '/pub/lib/jquery/additional-methods.js',
                '/pub/lib/jquery/jquery.metadata.js', '/pub/lib/jquery/jquery.hook.js',
                '/pub/lib/mage/validation/validate.js'];
            addToQueue(validatorFiles, syncQueue);
        }
    };

}(jQuery));