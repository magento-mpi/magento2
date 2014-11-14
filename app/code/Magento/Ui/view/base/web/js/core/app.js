/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './renderer/renderer',
    'Magento_Ui/js/lib/registry/registry'
], function (_, Renderer, registry) {
    'use strict';

    function load(config, name){
        require([config.path], function(constr){
            registry.set(name, new constr(config));
        });
    }

    var global = {
        init: function(data){
            this.data = {};

            this.register()
                .initRenderer(data.renderer)
                .initProviders(data.providers)
                .register();
        },

        initRenderer: function(data){
            this.renderer = new Renderer(data);

            return this;
        },
        
        initProviders: function(providers){
            _.each(providers, load);  

            return this; 
        },

        register: function () {
            registry.set('globalStorage', this);

            return this;
        }
    };

    return global.init.bind(global);
});