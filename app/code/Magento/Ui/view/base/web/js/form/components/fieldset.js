/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../collapsible'
], function(_, Collapsible) {
    'use strict';
    
    var defaults = {
        template: 'ui/fieldset/fieldset',
        hasData: true,
        loading: false
    };

    var __super__ = Collapsible.prototype;

    return Collapsible.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe('loading');
        },

        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on('update', this.onElementUpdate.bind(this));

            return this;
        },

        toggle: function(){
            __super__.toggle.apply(this, arguments);

            if(this.opened() && !this.hasData){
                this.requestData();
            }

            return this;
        },

        requestData: function(){
            this.loading(true);

            this.provider.get(this.source, this.onDataLoaded.bind(this));
        },

        onDataLoaded: function(){
            this.loading(false);
        },

        onElementUpdate: function(element, settings){
            var changed;

            this.elems().some(function(elem){
                return (changed = elem.hasChanged());
            });

            this.trigger('update', changed, this, settings);
        }
    });
});