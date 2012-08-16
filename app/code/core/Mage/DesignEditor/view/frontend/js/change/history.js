/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

( function ( $ ) {
    /**
     * Change history
     */
    $.fn.history = (function() {
        var _object;

        var _init = function() {
            if (!_object) {
                _object = new HistoryObject();
            }
            return _object;
        }

        return _init();
    })();

    /**
     * History object
     */
    function HistoryObject() {
        var history = [];
        return {
            add: function(revision, title) {
                history[revision] = title;
                console.log(history[revision]);
                /** @todo add your code */
            },
            undo: function() {
                /** @todo add your code */
            },
            redo: function() {
                /** @todo add your code */
            },
            revertToRevision: function(revision) {
                /** @todo add your code */
            }

        };

    };
})( jQuery );