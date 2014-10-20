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

    function initComponent(node){
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
        initialize: function(nodes, types) {
            this.types      = types;
            this.registry   = registry.create();

            this.process(nodes);
        },

        process: function(nodes, parent){
            var parse       = this.parse.bind(this, parent),
                insert      = parent && parent.component,
                children    = _.map(nodes, parse);

            if(insert){
                this.insert(children, parent.name);
            }

            return this;
        },

        parse: function(parent, node, name) {
            if (typeof node === 'string') {
                return node;
            }

            node = this.build.apply(this, arguments);

            if(node.template){
                return this.waitTemplate(node, parent);      
            }

            this.registry.set(node.name, node);

            if(node.type === "template"){
                return;
            }

            this.manipulate(node);

            if (node.component) {
                initComponent(node);
            }

            if (node.children) {
                this.process(node.children, node);
            }

            return node.name;
        },

        build: function(parent, node, name){
            var type = node.type || (parent && parent.childType);

            type = this.types.get(type);
            name = getNodeName.apply(null, arguments);
            node = $.extend(true, {}, type, node, {name: name});

            delete node.type;

            return node;
        },

        manipulate: function(node) {
            var name = node.name;

            if (node.appendTo) {
                this.insert(name, node.appendTo, -1);
            }

            if (node.prependTo) {
                this.insert(name, node.prependTo, 0);
            }

            if (node.wrapIn) {
                this.wrap(node.wrapIn, node);
            }

            return this;
        },

        waitTemplate: function(node, parent){
            var callback = this.applyTemplate.bind(this, node, parent);

            this.registry.get(node.template, callback);
        },

        applyTemplate: function(node, parent){
            var result = {},
                templates = _.toArray(arguments).slice(2);

            templates.push(node);

            templates.forEach(function(part){
                $.extend(true, result, part);
            });

            delete result.template;

            this.process([result], parent);
        },

        insert: function(items, targets, position){
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

            this.insert(child.name, node.name)
                .process([node]);
        }
    });
});