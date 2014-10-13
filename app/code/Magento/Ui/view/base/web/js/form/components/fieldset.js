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
], function(_, Collection, Component, EventsBus) {
    'use strict';

    var defaults = {
        collapsible:    false,
        opened:         true,
        template:       'ui/fieldset/fieldset'
    };

    var __super__ = Component.prototype;

    var Fieldset = Component.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe({
                'opened': this.opened
            });

            return this;
        },

        initListeners: function(){
            var update = this.onElementUpdate.bind(this);

            this.elems.forEach(function(elem){
                elem.on('update', update);
            });

            return this;
        },

        toggle: function() {
            var opened = this.opened;

            if(this.collapsible){
                opened(!opened());

                this.trigger('active', opened());
            }

            return this;
        },

        onElementUpdate: function(){
            var changed;

            this.elems.some(function(elem){
                return (changed = elem.hasChanged());
            });

            this.trigger(changed ? 'change' : 'restore');
        }
    });

    return Collection(Fieldset);
});