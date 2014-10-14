/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/initializer/collection',
    '../component'
], function($, _, Collection, Component) {
    'use strict';

    var defaults = {
        content: '',
        source: '',
        template: 'ui/content/content'
    };

    var __super__ = Component.prototype;

    var Html = Component.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe({
                content:        this.content,
                showSpinner:    false
            });

            this.containers.subscribe(this.onContainersUpdate.bind(this));

            return this;
        },

        onContainersUpdate: function(container){
            var containers = this.containers(),
                change = this.onContainerChange.bind(this);

            containers.forEach(function(elem){
                elem.on('active', change);
            });
        },

        onContainerChange: function(active){
            if(active && !this.hasData()){
                this.loadData();
            }
        },

        hasData: function(){
            return this.content();
        },

        loadData: function(){
            this.showSpinner(true);

            $.ajax({
                url: this.source,
                data: {
                    FORM_KEY: FORM_KEY
                },
                success: function(response){
                    this.showSpinner(false);
                    this.updateContent(response);
                }.bind(this)
            });

            return this;
        },

        updateContent: function(content){
            this.content(content);
        },

        hasChanged: function () {
            return false;
        }
    });

    return Collection(Html);
});