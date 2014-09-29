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

            if(!this.collapsible){
                this.opened = true;
            }

            this.initObservable()
                .initProvider();
        },

        initObservable: function(){
            this.observe({
                'elems':    this.injections || [],
                'opened':   this.opened
            });

            return this;
        },

        initProvider: function(){
            return this;
        },

        toggle: function() {
            var opened = this.opened;

            if(this.collapsible){
                opened(!opened());
            }
        }
    });

    return Collection(Fieldset);
});