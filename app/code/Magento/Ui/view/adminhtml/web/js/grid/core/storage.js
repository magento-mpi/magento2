define([
    '_',
    './rest',
    'Magento_Ui/js/framework/ko/scope'
], function(_, Rest, Scope){
    'use strict';
    
    return Scope.extend({
        initialize: function( config ){
            this.params = {};
            this.meta = {};

            _.extend(this, config);

            this.initClient();
        },

        initClient: function(){
            this.client = new Rest({
                api : this.api,
                onRead: this.onRead.bind(this)
            });

            return this;
        },

        load: function(options, callback){
            var params;

            if( typeof options === 'function' ){
                callback = options;
                options = {};
            }

            params = _.extend({}, this.params, options )

            if( this.beforeLoad ){
                this.beforeLoad();
            }

            this.client.read( params );

            return this;
        },

        setResult: function( result ){
            this.data = result.data;
            this.meta = result.meta;

            return this;
        },

        setParams: function(params){
            _.extend(this.params, params);

            return this;
        },

        getParams: function(){
            return this.params;
        },

        getMeta: function(){
            return this.meta;
        },

        onRead: function( result ){
            this.setResult( result )
                .trigger('load');
        }
    });
});