/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function() {

    var bindBeforeUnload = function() {
        window.onbeforeunload = function(e) {
            var e = e || window.event;
            var messageText = 'Automatic redirect has been triggered.';
            // For IE and Firefox
            if (e) {
                e.returnValue = messageText;
            }
            // For Chrome and Safari
            return messageText;
        };
    }

    var unbindBeforeUnload = function () {
        window.onbeforeunload = null;
    }

    window.setTimeout = (function(oldSetTimeout) {
        return function(func, delay) {
            return oldSetTimeout(function() {
                try {
                    bindBeforeUnload();
                    func();
                    unbindBeforeUnload();
                }
                catch (exception) {
                    unbindBeforeUnload();
                    throw exception;
                }
            }, delay);
        };
    })(window.setTimeout);

    window.setInterval = (function(oldSetInterval) {
        return function(func, delay) {
            return oldSetInterval(function() {
                try {
                    bindBeforeUnload();
                    func();
                    unbindBeforeUnload();
                }
                catch (exception) {
                    unbindBeforeUnload();
                    throw exception;
                }
            }, delay);
        };
    })(window.setInterval);

})();
