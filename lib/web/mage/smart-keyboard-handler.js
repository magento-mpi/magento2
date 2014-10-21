/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery'
],function($){
    function KeyboardHandler() {
        var focusState = false;
        var tabFocusClass = 'keyfocus';
        return {
            init: SmartKeyboardFocus
        };

        function SmartKeyboardFocus() {
            $(document).on('keydown keypress', function(event){
                if(event.which === 9 && !focusState) {
                    $('body')
                        .on('focusin', onFocusInHandler)
                        .on('click', onClickHandler);
                }
            });

        }
        function onFocusInHandler () {
            focusState = true;
            $('body').addClass(tabFocusClass)
                    .off('focusin', onFocusInHandler);
        }

        function onClickHandler(event) {
            focusState  = false;
            $('body').removeClass(tabFocusClass)
                .off('click', onClickHandler);
        }
    }

    return new KeyboardHandler;
});