/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/registry/registry'
], function (_, registry) {
    'use strict';

    function load(config, name){
        require([config.path], function(callback){
            callback(config, name);
        });
    }

    var global = {
        init: function(data){
            this.data = {};

            this.set(data)
                .register()
                .load();
        },

        get: function(name){
            return name ? this.data[name] || this.data;
        },

        set: function(data){
            this.data = data;
        },

        register: function () {
            registry.set('globalStorage', this);

            return this;
        },

        load: function(){
            var data = this.data;

            _.each(data.providers,   load);
            _.each(data.components,  load);   
        }
    };

    return global.init.bind(global);
});