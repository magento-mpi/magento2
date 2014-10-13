/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events'
], function(_, Scope, EventsBus) {
    'use strict';

    return Scope.extend({
        initialize: function(config) {
            _.extend(this, config);

            this.initElements()
                .initObservable()
                .initListeners();
        },

        initElements: function(){
            var containers;

            this.elems.forEach(function(elem){
                containers = elem.containers;
                
                if(containers){
                    containers.push(this);
                }
            }, this);

            return this;
        },

        initObservable: function(){
            this.observe({
                containers: []
            });

            return this;
        },

        initListeners: function(){
            return this;
        },

        getTemplate: function(){
            return this.template;
        }
    }, EventsBus);
});