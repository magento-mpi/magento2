/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($, window) {
    "use strict";

    // List of arguments, passed to the last call of define()
    var lastDefineArgs;

    // Intercept RequireJS define() call, which is used by tested scripts
    window.define = function () {
        lastDefineArgs = arguments;
    };

    // ---Private functions---
    // Retrieve the URL to a specific script in the way as it is used by JsTestDriver
    var getUrlByScriptPath = function (scriptPath) {
        var result = undefined;
        $('script[src]').each(function() {
            if (this.src.indexOf(scriptPath) >= 0) {
                result = this.src;
                return false;
            }
            return true;
        });
        return result;
    };

    // Load the script by url
    var loadScript = function (url) {
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'script',
            async: false,
            error: function () {
                throw new Error('Could not load ' + url);
            }
        });
    };

    // ---Expose interface to work with RequireJS Util---
    var requirejsUtil = {
        getDefineArgsInScript: function (scriptPath) {
            lastDefineArgs = undefined;

            var url = getUrlByScriptPath(scriptPath);
            if (!url) {
                throw new Error('Could not find script by path, check that the script is loaded: ' + scriptPath);
            }
            loadScript(url);

            return lastDefineArgs;
        }
    };

    window.jsunit = window.jsunit || {};
    $.extend(window.jsunit, {requirejsUtil: requirejsUtil});
})(jQuery, window);
