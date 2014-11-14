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

            elem.activate();
        },

        initRenderer: function () {
            this.renderer = registry.get('globalStorage').renderer;

            return this;
        },

        initChildTemplate: function () {
            var template = this.name + '.' + this.itemTemplate;

            this.childTemplate = {
                template:   template,
                parent:     this.name
            };

            return this;
        },

        initChildren: function () {
            var data     = this.provider.data,
                children = data.get(this.dataScope);
            
            _.each(children, function(item, index){
                this.addChild(index);
            }, this);

            return this;
        },

        addChild: function(index) {
            var setIndex = _.isObject(index) || _.isUndefined(index);

            if (setIndex) {
                index = 'new_' + this.lastIndex;
            }

            _.extend(this.childTemplate, {
                name:       index,
                dataScope:  index
            });

            this.renderer.render({
                layout: [this.childTemplate]
            });

            this.lastIndex++;
        },

        removeChild: function(elem) {
            return function() {
                if (confirm(this.removeMessage)) {
                    this._removeChild(elem);
                }

            }.bind(this);
        },

        _removeChild: function(elem) {
            var isActive = elem.active(),
                first;

            this.remove(elem);
            this.provider.data.remove(elem.dataScope);

            first = this.elems()[0];

            if (first && isActive) {
                first.activate();
            }
        }
    });
});

