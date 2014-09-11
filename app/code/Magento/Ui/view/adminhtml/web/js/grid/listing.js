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
            _.extend(this, settings);

            this.initObservable()
                .initProvider()
                .updateItems();
            
            this.fields = this.provider.meta.get('fields');
        },

        initObservable: function() {
            this.observe({
                rows:       [],
                view:       'grid',
                isLocked:   false,
                templateExtenders: [],
                extenders: []
            });

            return this;
        },

        initProvider: function() {
            this.provider.on({
                'beforeRefresh':    this.lock.bind(this),
                'refresh':          this.onRefresh.bind(this)
            });

            this.provider.dump.wait('update:extenders', this.updateExtenders.bind(this));

            return this;
        },

        updateExtenders: function (extenders) {
            var adjusted = extenders.reduce(this.adjustExtender, {});
            this.extenders(adjusted);

            this.templateExtenders(extenders.map(this.adjustTemplateExtender, this));
        },

        updateItems: function() {
            var items = this.provider.data.get('items');

            this.rows(items);

            return this;
        },

        getExtenderName: function(){

        },

        getCellTemplateFor: function(field) {
            return this.getRootTemplatePath() + '.cell.' + field.data_type;
        },

        getTemplate: function() {
            return {
                name:      'Magento_Ui.templates.listing.' + this.view(),
                extenders: this.templateExtenders()
            };
        },

        adjustTemplateExtender: function (extender) {
            return this.getRootTemplatePath() + '.' + extender.path;
        },

        getRootTemplatePath: function() {
            return 'Magento_Ui.templates.listing.' + this.view();
        },

        adjustExtender: function (adjusted, extender) {
            adjusted[extender.as] = extender.name;

            return adjusted;
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