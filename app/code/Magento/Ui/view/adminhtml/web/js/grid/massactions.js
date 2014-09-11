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
        selectActions: [
            'selectAll',
            'selectAllVisible',
            'deselectAll',
            'deselectAllVisible'
        ],
        collectionActions: [
            'delete',
            'enable',
            'disable'
        ],
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
         *
         * @return {Object} - reference to instance
         */
        initObservable: function(){
            this.observe({
                itemsSelected:      this.itemsSelected,
                isSelectedAll:      this.isSelectedAll,
                action:       this.action
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
                name: this.name
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

            params.set('sorting', {
                itemsSelected: this.itemsSelected(),
                isSelectedAll: this.isSelectedAll(),
                action: this.action()
            });

            return this;
        }

    });

    return Component({
        constr: MassActions
    });
});