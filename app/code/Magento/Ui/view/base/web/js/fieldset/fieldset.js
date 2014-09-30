/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/collection',
    'Magento_Ui/js/lib/ko/scope'
], function(_, Collection, Scope) {
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

        toggle: function() {
            var opened = this.opened;

            if(this.collapsible){
                opened(!opened());
            }

            return this;
        },

        getTemplate: function(){
            return this.template;
        }
    });

    return Collection(Fieldset);
});