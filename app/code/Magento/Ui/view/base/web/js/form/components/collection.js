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

            this.initChildTemplate();

            this.layout = registry.get('globalStorage').layout;
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe('active');

            return this;
        },

        initElement: function (element) {
            __super__.initElement.apply(this, arguments);

            var active          = this.active(),
                activeDefined   = !(active === null),
                index           = element.index,
                shouldBeActive  = activeDefined ? active == index : true;

            if (shouldBeActive) {
                this._setActive(element);
            }
        },

        initChildTemplate: function () {
            this.childTemplate = {
                type: this.childType,
                appendTo: this.name,
                parentName: this.name,
                config: {}
            };
        },

        addElement: function () {
            var last    = this.elems.last(),
                index   = ++last.index;

            _.extend(this.childTemplate, {
                index: index,
                config: {
                    index: index
                }
            });

            this.layout.process([this.childTemplate]);
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
            var index   = element.index;

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

