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
            config = this.getConfig( params, config );

            $.ajax(config)
                .done(this.config.onRead);
        },

        getConfig: function(params, config){
            var url,
                data;

            url = requestBuilder( this.config.root, params );

            data = _.extend({
                form_key: FORM_KEY,
                namespace: this.config.namespace
            }, params);

            return _.extend({url: url, data: data}, this.config.ajax, config);
        }
    });

});