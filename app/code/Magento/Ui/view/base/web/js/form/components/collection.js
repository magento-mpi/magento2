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
        active: null,
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

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active');

            return this;
        },

        initElement: function (element) {
            var indexed         = this.elems.indexBy('index'),
                activeIndex     = this.active(),
                activeExists    = indexed[activeIndex],
                activeDefined   = activeIndex !== null,
                elementIndex    = element.index,
                activeElement;

            element.setDataScope(this.dataScope);

            activeElement = activeDefined
                ? (activeExists ? (elementIndex == activeIndex) && element : element)
                : (elementIndex == 0) && element

            if (activeElement) {
                this._setActive(activeElement);
            }
        },

        initRenderer: function () {
            this.renderer = registry.get('globalStorage').renderer;

            return this;
        },

        initChildTemplate: function () {
            this.childTemplate = {
                template: this.name + '.' + this.itemTemplate,
                appendTo: this.name,
                parentName: this.name
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
                name: index
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
        },

        setActive: function (element) {
            return this._setActive.bind(this, element);
        },

        _setActive: function (element) {
            var index = element.index;

            this.active(index);
            this.activate(element);
            this.deactivate(this.elems.without(element));
        },

        deactivate: function (elements) {
            elements.each(function (element) {
                element.active(false);
            });

            return this;
        },

        activate: function (element) {
            element.active(true);
        }
    });
});

