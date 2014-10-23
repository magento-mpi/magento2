define([
    'jquery',
    'underscore',
    'Magento_Ui/js/lib/class'
], function($, _, Class){
    'use strict';

    var defaults = {
        ajaxConfig: {
            data: {
                form_key: FORM_KEY
            }
        }
    };

    return Class.extend({
        initialize: function(){
            _.extend(this, defaults);
        },

        get: function(settings){
            var config,
                ajaxConfig = defaults.ajaxConfig;

            config = $.extend(true, {}, ajaxConfig, settings);

            $.ajax(config);

            return this;
        }
    });
});