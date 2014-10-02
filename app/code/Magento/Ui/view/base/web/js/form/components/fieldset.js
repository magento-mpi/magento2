/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events'
], function(_, Collection, Scope, EventsBus) {
    'use strict';

    var defaults = {
        collapsible:    false,
        opened:         true,
        template:       'ui/fieldset/fieldset'
    };

    var Fieldset = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners();
        },

        initObservable: function(){
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
            }

            return this;
        },

        getTemplate: function(){
            return this.template;
        },

        onElementUpdate: function(){
            var changed;

            this.elems.some(function(elem){
                return (changed = elem.hasChanged());
            });

            this.trigger(changed ? 'change' : 'restore');
        }
    }, EventsBus);

    return Collection(Fieldset);
});