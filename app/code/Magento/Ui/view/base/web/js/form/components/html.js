/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery',
    'underscore',
    '../component'
], function($, _, Component) {
    'use strict';

    var defaults = {
        content:        '',
        showSpinner:    true,
        loading:        false,
        template:       'ui/content/content'
    };

    var __super__ = Component.prototype;

    return Component.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initAjaxConfig();
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe('content loading');

            this.loading.subscribe(function(value){
                this.trigger(value ? 'loading' : 'loaded');
            }, this);

            this.containers.subscribe(this.onContainersUpdate.bind(this));

            return this;
        },

        initAjaxConfig: function(){
            this.ajaxConfig = {
                url:        this.source,
                data:       { FORM_KEY: FORM_KEY },
                success:    this.onDataLoaded.bind(this)
            };

            return this;
        },

        onContainersUpdate: function(container){
            var containers  = this.containers(),
                change      = this.onContainerChange.bind(this);

            containers.forEach(function(elem){
                elem.on('active', change);
            });
        },

        onContainerChange: function(active){
            if(active && this.shouldLoad()){
                this.loadData();
            }
        },

        hasData: function(){
            return !!this.content();
        },

        shouldLoad: function(){
            return this.source && !this.hasData() && !this.loading();
        },

        loadData: function(){
            this.loading(true);

            $.ajax(this.ajaxConfig);

            return this;
        },

        onDataLoaded: function(data){
            this.updateContent(data)
                .loading(false); 
        },

        updateContent: function(content){
            this.content(content);

            return this;
        }
    });
});