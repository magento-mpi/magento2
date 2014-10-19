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

    function registerNode(node){
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

            node = this.build.apply(this, arguments);

            this.manipulate(node);

            if (node.children) {
                this.process(node.children, node);
            }

            return node.name;
        },

        build: function(parent, node, name){
            var type = node.type || parent.childType;

            type        = this.types.get(type);
            node.name   = getNodeName.apply(null, arguments);

            return $.extend(true, {}, type, node);
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

            if (!node.virtual) {
                registerNode(node);
            }

            return this;
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
            node = this.build('', node);

            this.insert(node.name, child.name)
                .process([node]);
        }
    });
});