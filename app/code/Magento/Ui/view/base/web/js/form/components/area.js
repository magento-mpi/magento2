/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../component'
], function(_, Component) {
    'use strict';

    var defaults = {
        template:   'ui/area',
        active:     false,
        changed:    false,
        loading:    false
    };

    var __super__ = Component.prototype;

    return Component.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initListeners()
                .pushParams();
        },

        initObservable: function() {
            __super__.initObservable.apply(this, arguments);

            this.observe('active changed loading');

            return this;
        },

        initListeners: function() {
            var params  = this.provider.params;

            params.on('update:activeArea', this.updateState.bind(this));

            return this;
        },

        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on({
                update:     this.onChildrenUpdate.bind(this),
                loading:    this.onContentLoading.bind(this, true),
                loaded:     this.onContentLoading.bind(this, false)
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
});