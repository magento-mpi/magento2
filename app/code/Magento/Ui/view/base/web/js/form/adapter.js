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

        if(elem.onclick){
            elem.onclick = null;
        }

        $(elem).off()
                .on('click', callback);
    }

    return {
        on: function(handlers){
            var elem,
                selector;

            _.each(handlers, initListener);
        }
    }
});