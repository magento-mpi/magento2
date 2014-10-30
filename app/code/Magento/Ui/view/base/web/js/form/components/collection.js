/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/form/component',
    'underscore',
    'Magento_Ui/js/lib/registry/registry'
], function (Component, _, registry) {
    'use strict';

    var __super__ = Component.prototype;

    var defaults = {
        lastIndex: 0,
        template: 'ui/form/components/collection'
    };

    return Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initRenderer()
                .initChildTemplate()
                .initChildren();
        },

        initElement: function (elem) {
            __super__.initElement.apply(this, arguments);

            elem.setActive();
        },

        initRenderer: function () {
            this.renderer = registry.get('globalStorage').renderer;

            return this;
        },

        initChildTemplate: function () {
            this.childTemplate = {
                template: this.name + '.' + this.itemTemplate,
                parent: this.name
            };

            return this;
        },

        initChildren: function () {
            var children = this.provider.data.get(this.dataScope);
            
            _.each(children, this.initChild.bind(this));
        },

        initChild: function (item, index) {
            this.lastIndex++;

            this.createChild(index);
        },

        addEmptyChild: function () {
            var index = 'new_' + this.lastIndex++;
                
            this.createChild(index);
        },

        createChild: function (index) {
            _.extend(this.childTemplate, {
                name: index,
                dataScope: index
            });

            this.renderer.render({
                layout: [this.childTemplate]
            });
        },

        removeElement: function (element) {
            return this._removeElement.bind(this, element);
        },

        _removeElement: function (element) {
            var shouldRemove = confirm(this.removeMessage);

            if (shouldRemove) {
                this.remove(element);
            }
        }
    });
});

