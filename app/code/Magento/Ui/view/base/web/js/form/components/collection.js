/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'mage/utils',
    'Magento_Ui/js/form/component',
    'underscore',
    'Magento_Ui/js/lib/registry/registry'
], function (utils, Component, _, registry) {
    'use strict';

    var __super__ = Component.prototype;

    var defaults = {
        lastIndex: 0,
        template: 'ui/form/components/collection'
    };

    var childTemplate = {
        template:   "{name}.{itemTemplate}",
        parent:     "{name}",
        name:       "{childName}",
        dataScope:  "{childName}"
    };

    return Component.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.initChildren();
        },

        initElement: function (elem) {
            __super__.initElement.apply(this, arguments);

            elem.activate();

            this.trigger('update');
        },

        initChildren: function () {
            var data     = this.provider.data,
                children = data.get(this.dataScope),
                initial  = this.initialItems = [];
                        
            _.each(children, function(item, index){
                initial.push(this.name + '.' + index);
                this.addChild(index);
            }, this);

            return this;
        },

        addChild: function(index) {
            var setIndex = _.isObject(index) || _.isUndefined(index);

            if (setIndex) {
                index = 'new_' + this.lastIndex++;
            }

            this.childName = index;

            this.renderer.render({
                layout: [
                    utils.template(childTemplate, this)
                ]
            });
        },

        hasChanged: function(){
            var initial = this.initialItems,
                current = this.elems.pluck('name'),
                changed = !utils.identical(initial, current);

            return changed || this.elems.some(function(elem){
                return elem.delegate('hasChanged', 'some');
            });
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

            elem.destroy();

            first = this.elems()[0];

            if (first && isActive) {
                first.activate();
            }

            this.trigger('update');
        }
    });
});

