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
    'Magento_Ui/js/lib/registry/registry'
], function(_, Collection, Scope, registry) {
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

            return this;
        },

        toggle: function() {
            var opened = this.opened;

            if(this.collapsible){
                opened(!opened());
            }
        },

        waitElements: function(){
            registry.get(
                this.injections,
                this.initElements.bind(this)
            );
            
            return this;
        }
    });

    return Collection(Fieldset);
});