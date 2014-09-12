/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component'
], function(_, Scope, Component) {
    'use strict';

    var defaults = {
        collectionActions: [],
        templateExtender: 'Magento_Ui.listing.grid.massactions'
    };

    var MassActions = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * Updates storage with current state of instance.
         * @param  {Object} config
         */
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .attachTemplateExtender()
                .updateParams();
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function(){
            this.observe({
                isActionListVisible: this.isActionListVisible || false,
                action: this.action
            });

            return this;
        },

        /**
         * Attaches it's template to provider.dump's extenders
         * @return {Object} - reference to instance
         */
        attachTemplateExtender: function () {
            var provider = this.provider.dump,
                extenders = this.provider.dump.get('extenders');

            extenders.push({
                path: this.templateExtender,
                name: this.name,
                as: 'massactions'
            });

            provider.trigger('update:extenders', extenders);

            return this;
        },

        /**
         * Updates storage's params and reloads it.
         */
        reload: function() {
            this.updateParams()
                .provider.refresh();
        },

        /**
         * Updates storage's params by the current state of instance
         * @return {Object} - reference to instance
         */
        updateParams: function() {
            var params = this.provider.params;

            params.set('actions', {
                action: this.action()
            });

            return this;
        },

        /**
         * Toggle visibility of dropdown actions list
         */
        toggleActionListVisible: function() {
            this.isActionListVisible(!this.isActionListVisible());
        },

        /**
         * Updates storage's params by the current state of instance
         */
        setAction: function(actionId) {
            this.action(actionId);
            this.updateParams();
        }

    });

    return Component({
        constr: MassActions
    });
});