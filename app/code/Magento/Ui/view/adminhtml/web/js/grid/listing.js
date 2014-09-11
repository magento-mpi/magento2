/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    './core/component',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/mixin/loader',
], function(_, Component, Scope, Loader) {
    'use strict';

    var Listing =  Scope.extend({
        initialize: function(settings) {
            this.initObservable()
                .initProvider(settings)
                .updateItems();
            
            this.fields = this.provider.meta.get('fields');
        },

        initObservable: function() {
            this.observe({
                rows:       [],
                view:       'grid',
                isLocked:   false,
                templateExtenders: []
            });

            return this;
        },

        initProvider: function(settings) {
            this.provider = settings.provider;

            this.provider.on({
                'beforeRefresh':    this.lock.bind(this),
                'refresh':          this.onRefresh.bind(this)
            });

            this.provider.dump.on('update:extenders', this.updateExtenders);

            return this;
        },

        updateExtenders: function (extenders) {
            this.templateExtenders(extenders);
        },

        updateItems: function() {
            var items = this.provider.data.get('items');

            this.rows(items);

            return this;
        },

        getCellTemplateFor: function(field) {
            return this.getRootTemplatePath() + '.cell.' + field.data_type;
        },

        getTemplate: function() {
            var templateExtenders = this.templateExtenders();

            return {
                name:      'Magento_Ui.templates.listing.' + this.view(),
                extenders: templateExtenders.map(this.adjustTemplateExtender.bind(this))
            };
        },

        adjustTemplateExtender: function (extender) {
            return this.getRootTemplatePath() + '.' + extender.path;
        },

        getRootTemplatePath: function() {
            return 'Magento_Ui.templates.listing.' + this.view();
        },

        onRefresh: function() {
            this.unlock()
                .updateItems();
        }
    }, Loader);

    return Component({
        constr: Listing
    });
});