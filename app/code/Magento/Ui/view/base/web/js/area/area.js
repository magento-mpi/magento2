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
        visible: false
    };

    var Area = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initProvider()
                .pullParams()
                .waitElements();
        },

        initObservable: function() {
            this.observe({
                'elems':    [],
                'visible':  this.visible
            });

            return this;
        },

        initProvider: function() {
            var params = this.provider.params;

            params.on('update:activeTab', this.pullParams.bind(this));

            return this;
        },

        initElements: function(){
            var elems = this.elems;

            elems.push.apply(elems, arguments);

            return this;
        },

        pullParams: function() {
            var params  = this.provider.params,
                area    = params.get('activeTab');

            this.visible(area === this.name);

            return this;
        },

        waitElements: function(){
            registry.get(
                this.injections,
                this.initElements.bind(this)
            );

            return this;
        }
    });

    return Collection(Area);
});