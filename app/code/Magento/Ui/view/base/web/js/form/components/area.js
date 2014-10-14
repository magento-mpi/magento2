/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    '../component'
], function(_, Collection, Component) {
    'use strict';

    var defaults = {
        template:   'ui/area',
        active:     false,
        changed:    false,
        loading:    false
    };

    var __super__ = Component.prototype;

    var Area = Component.extend({
        initialize: function() {
            _.extend(this, defaults);
            
            __super__.initialize.apply(this, arguments);

            this.pushParams();
        },

        initObservable: function() {
            __super__.initObservable.apply(this, arguments);

            this.observe({
                active:   this.active,
                changed:  this.changed,
                loading:  this.loading
            });

            return this;
        },

        initListeners: function() {
            var params  = this.provider.params,
                handlers;

            handlers = {
                update:     this.onChildrenUpdate.bind(this),
                loading:    this.onContentLoading.bind(this, true),
                loaded:     this.onContentLoading.bind(this, false)
            };

            params.on('update:activeArea', this.updateState.bind(this));

            this.elems.forEach(function(elem){
                elem.on(handlers);
            });

            return this;
        },

        pushParams: function() {
            var params = this.provider.params;

            if(this.active()){
                params.set('activeArea', this.name);
            }
        },

        updateState: function(area) {
            var active = area === this.name;

            this.trigger('active', active)
                .active(active);
                
            return this;
        },
        
        setActive: function(){
            this.active(true);

            this.pushParams();
        },

        onChildrenUpdate: function(changed, element, settings){
            var params  = this.provider.params;

            if (settings.makeVisible) {
                this.setActive();
            }

            this.changed(changed);
        },

        onContentLoading: function(finished){
            this.loading(finished);
        }
    });

    return Collection(Area);
});