/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
define([
    'jquery',
    'underscore',
    '../component'
], function($, _, Component) {
    'use strict';

    var defaults = {
        content:        '',
        showSpinner:    false,
        loading:        false,
        template:       'ui/content/content'
    };

    var __super__ = Component.prototype;

    return Component.extend({

        /**
         * Extends instance with default config, calls 'initialize' method of
         *     parent, calls 'initAjaxConfig'
         */
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            _.bindAll(this, 'onContainerToggle', 'onDataLoaded');

            this.initAjaxConfig();
        },

        /**
         * Calls 'initObservable' method of parent, initializes observable
         *     properties of instance
         *     
         * @return {Object} - reference to instance
         */
        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe('content loading');

            return this;
        },

        /**
         * Calls 'initListeners' method of parent, defines instance's subscriptions
         * 
         * @return {Object} - reference to instance
         */
        initListeners: function () {
            __super__.initListeners.apply(this, arguments);

            this.loading.subscribe(function(value){
                this.trigger(value ? 'loading' : 'loaded');
            }, this);

            return this;
        },

        initContainer: function(parent){
            __super__.initContainer.apply(this, arguments);

            parent.on('active', this.onContainerToggle);

            return this;
        },

        /**
         * Initializes default ajax config on instance
         * 
         * @return {Object} - reference to instance
         */
        initAjaxConfig: function(){
            this.ajaxConfig = {
                url:        this.source,
                data:       { FORM_KEY: FORM_KEY },
                success:    this.onDataLoaded
            };

            return this;
        },

        /**
         * Calls 'loadData' if both 'active' variable and 'shouldLoad'
         *     property are truthy
         * 
         * @param  {Boolean} active
         */
        onContainerToggle: function(active){
            if(active && this.shouldLoad()){
                this.loadData();
            }
        },

        /**
         * Defines if instance has 'content' property defined 
         * 
         * @return {Boolean} [description]
         */
        hasData: function(){
            return !!this.content();
        },

        /**
         * Defines if instance should load external data
         * 
         * @return {Boolean}
         */
        shouldLoad: function(){
            return this.source && !this.hasData() && !this.loading();
        },

        /**
         * Sets loading property to true, makes ajax call
         * 
         * @return {Object} - reference to instance
         */
        loadData: function(){
            this.loading(true);

            $.ajax(this.ajaxConfig);

            return this;
        },

        /**
         * Ajax's request success handler. Calls 'updateContent' passing 'data'
         *     to it, then sets 'loading' property to false 
         * 
         * @param  {String} data
         */
        onDataLoaded: function(data){
            this.updateContent(data)
                .loading(false); 
        },

        /**
         * Sets incoming data 'content' property's value
         *  
         * @param  {String} content
         * @return {Object} - reference to instance
         */
        updateContent: function(content){
            this.content(content);

            return this;
        }
    });
});