/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/registry/registry'
], function(_, Scope, registry) {
    'use strict';

    var Tab = Scope.extend({
        initialize: function(config) {
            _.extend(this, config);

            this.initObservable()
                .initProvider();
        },

        initObservable: function() {
            this.observe({
                'active': this.active,
            });

            return this;
        },

        initProvider: function() {
            this.provider.params.on('update:activeTab', this.pullParams.bind(this));

            if (this.active()) {
                this.pushParams();
            }

            return this;
        },

        setActive: function() {
            var active = this.active();

            this.active(true)

            if (!active) {
                this.pushParams();
            }
        },

        pushParams: function() {
            this.provider.params.set('activeTab', this.name);
        },

        pullParams: function(tab) {
            if (this.name !== tab) {
                this.active(false);
            }
        }
    });

    return Tab;
});