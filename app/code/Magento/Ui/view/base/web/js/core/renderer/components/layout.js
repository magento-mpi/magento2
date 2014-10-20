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

    function getNodeType(parent, node){
        return node.type || (parent && parent.childType);
    }

    function mergeNode(node, config){
        return $.extend(true, {}, config, node);
    }

    function Layout(nodes, types){
        this.types      = types;
        this.registry   = registry.create();

        this.process(nodes);
    }

    _.extend(Layout.prototype, {
        process: function(nodes, parent){
            var insert,
                children;

            if (nodes) {
                insert   = parent && parent.component;
                children = _.map(nodes, this.parse.bind(this, parent));

                if (insert) {
                    this.insert(children, parent.name);
                }
            }

            return this;
        },

        parse: function(parent, node, name) {
            if (typeof node === 'string') {
                return node;
            }

            node = this.build.apply(this, arguments);

            if (node) {
                this.manipulate(node)
                    .initComponent(node)
                    .process(node.children, node);
            }

            return node && node.name;
        },

        build: function(parent, node, name){
            var type;

            type = getNodeType.apply(null, arguments);
            name = getNodeName.apply(null, arguments);
            node = mergeNode(node, this.types.get(type));

            node.name = name;

            delete node.type;

            if(node.template){
                return this.waitTemplate(node, parent);      
            }

            this.registry.set(name, node);

            if(type !== 'template'){
                return node;
            }
        },

        initComponent: function(node){
            var source = node.component,
                name;

            if(source){
                source  = [source]; 
                name    = node.name;

                registry.get(node.deps, function(){
                    require(source, function(constr){
                        registry.set(name, new constr(node.config, name));
                    });
                });
            }

            return this;
        }
    });
    
    _.extend(Layout.prototype, {
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

            return this;
        }
    });

    _.extend(Layout.prototype, {
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
    });

    return Layout;
});