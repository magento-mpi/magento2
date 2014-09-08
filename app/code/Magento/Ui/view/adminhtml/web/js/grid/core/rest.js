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
            var url = requestBuilder( this.config.root, params ),
                data;

            config = config || {};

            data = _.extend({
                form_key: FORM_KEY,
                namespace: this.config.namespace
            }, params);

            config = _.extend(
                {
                    url: url,
                    data: data
                },
                this.config.ajax,
                config
            );

            $.ajax(config)
                .done(this.config.onRead);
        }
    });

});