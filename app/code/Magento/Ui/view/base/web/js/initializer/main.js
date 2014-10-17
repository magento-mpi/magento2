/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/types/types',
    'Magento_Ui/js/layout/layout',
    'Magento_Ui/js/lib/registry/registry'
], function (_, Types, Layout, registry) {
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
            return name ? this.data[name] : this.data;
        },

        set: function(data){
            this.data = data;

            return this;
        },

        register: function () {
            registry.set('globalStorage', this);

            return this;
        },

        load: function(){
            _.each(this.data.providers, load);
            _.each(this.data.components, load);

            this.types  = new Types(this.data.types);
            this.layout = new Layout(this.data.layout);

            return this; 
        }
    };

    return global.init.bind(global);
});