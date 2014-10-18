/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'jquery',
    'mage/utils',
    'Magento_Ui/js/lib/class',
    'Magento_Ui/js/lib/registry/registry'
], function(_, $, utils, Class, registry) {
    'use strict';

    function initNode(node){
        var deps    = node.deps,
            source  = [node.component],
            name    = node.name;

        registry.get(deps, function(){

            require(source, function(constr){
                registry.set(name, new constr(node.config, name));
            });
        });
    }

    function getNodeName(parent, node, name) {
        var parentName = node.parentName || (parent && parent.name);

        if (typeof name !== 'string') {
            name = node.name;
        }

        if (parentName) {
            name = parentName + '.' + name;
        }

        return name;
    }

    function stringToArray(str){
        return typeof str === 'string' ?
            str.split(' ') :
            str;
    }

    return Class.extend({
        initialize: function(nodes) {
            this.types = registry.get('globalStorage').types;

            this.process(nodes);
        },

        process: function(nodes, parent){
            var parse       = this.parse.bind(this, parent),
                insert      = parent && !parent.virtual,
                children    = _.map(nodes, parse);

            if(insert){
                this.insert(parent.name, children);
            }

            return this;
        },

        parse: function(parent, node, name) {
            if (typeof node === 'string') {
                return node;
            }

            node = this.buildNode.apply(this, arguments);

            this.manipulate(node);

            if (!node.virtual) {
                initNode(node);
            }

            if (node.children) {
                this.process(node.children, node);
            }

            return node.name;
        },

        manipulate: function(node) {
            var name = node.name;

            if (node.appendTo) {
                this.insert(node.appendTo, name, -1);
            }

            if (node.prependTo) {
                this.insert(node.prependTo, name, 0);
            }

            if (node.wrapIn) {
                this.wrap(node.wrapIn, node);
            }

            return this;
        },

        buildNode: function(parent, node, name){
            var type = this.types.get(node.type);

            name = getNodeName.apply(null, arguments);

            node.name = name;

            return $.extend(true, {}, type, node);
        },

        insert: function(targets, items, position){
            items   = _.compact(utils.stringToArray(items));
            targets = utils.stringToArray(targets);

            _.each(targets, function(target){
                registry.get(target, function(target){
                    target.insert(items, position);
                });
            });

            return this;
        },

        wrap: function(node, child){
            node = this.buildNode('', node);

            this.insert(node.name, child.name)
                .process([node]);
        }
    });
});