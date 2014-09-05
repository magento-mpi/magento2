define([
    '_',
    'jquery',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/request_builder'
], function( _, $, Class, requestBuilder ){
    'use strict';

    return Class.extend({
        initialize: function( config ){
            this.config = {
                ajax: {
                    dataType: 'json'
                }
            };

            $.extend( true, this.config, config );
        },

        read: function(params, config){
            config = _.extend(
                {},
                this.config.ajax,
                config || {},
                requestBuilder.getFor(this.config.root, params)
            );

            $.ajax(config).done(this.config.onRead);
        }
    });

});