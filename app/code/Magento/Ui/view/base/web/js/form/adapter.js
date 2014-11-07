define([
    'jquery',
    'underscore'
], function($, _){
    var buttons = {
        'reset':            '#reset',
        'save':             "#save",
        'saveAndContinue':  '#save_and_continue'
    };

    function initListener(callback, action){
        var selector    = buttons[action],
            elem        = $(selector)[0];

        if (!elem) {
            return;
        }

        if(elem.onclick){
            elem.onclick = null;
        }

        $(elem).off()
                .on('click', callback);
    }

    return {
        on: function(handlers){
            _.each(handlers, initListener);
        }
    }
});