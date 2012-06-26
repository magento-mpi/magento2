/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function(before, after) {

    window.setTimeout = (function(oldSetTimeout) {
        return function(func, delay) {
            return oldSetTimeout(function() {
                try {
                    before();
                    func();
                    after();
                }
                catch (exception) {
                    after();
                    throw exception;
                }
            }, delay);
        };
    })(window.setTimeout);

    window.setInterval = (function(oldSetInterval) {
        return function(func, delay) {
            return oldSetInterval(function() {
                try {
                    before();
                    func();
                    after();
                }
                catch (exception) {
                    after();
                    throw exception;
                }
            }, delay);
        };
    })(window.setInterval);

})(
    function() {
        window.onbeforeunload = function(e) {
            var e = e || window.event;
            var messageText = 'Are you sure, you want to leave this page?';
            // For IE and Firefox
            if (e) {
                e.returnValue = messageText;
            }
            // For Chrome and Safari
            return messageText;
        };
    },
    function () {
        window.onbeforeunload = null;
    }
);
