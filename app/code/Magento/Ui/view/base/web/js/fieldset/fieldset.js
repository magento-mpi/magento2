/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/collection',
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
                .waitElements();
        },

        initObservable: function(){
            this.observe({
                'elems':    [],
                'opened':   this.opened
            });

            return this;
        },

        initElements: function(){
            var elems = this.elems;

            elems.push.apply(elems, arguments);

            elems().forEach(function(elem){
                if(elem.on){
                    elem.on('update', this.onElementUpdate.bind(this));
                }
            }, this);
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
            var elems = this.elems(),
                changed;

            elems.some(function(elem){
                return (changed = elem.hasChanged());
            });

            this.trigger(changed ? 'change' : 'restore');
        }
    }, EventsBus);

    return Collection(Fieldset);
});