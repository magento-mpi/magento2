/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
    
var timeoutCounter = 0,
    unbindBeforeUnload = function () {
        timeoutCounter--;
        if (timeoutCounter < 1) {
            window.onbeforeunload = null;
        }
    },
    bindBeforeUnload = function() {
        if (timeoutCounter < 1) {
            window.onbeforeunload = function (e) {
                var e = e || window.event,
                    messageText = 'Are you sure, you want to leave this page?';
                // For IE and Firefox
                if (e) {
                    e.returnValue = messageText;
                }
                // For Chrome and Safari
                return messageText;
            };
        }
        timeoutCounter++;
    }

window.setTimeout = (function(oldSetTimeout) {
    return function(func, delay) {
        oldSetTimeout(function() {
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
        oldSetInterval(function() {
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