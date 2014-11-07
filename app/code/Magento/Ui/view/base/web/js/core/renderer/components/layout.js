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

    function getDataScope(parent, node){
        var dataScope   = node.dataScope || '',
            parentScope = parent && parent.dataScope;

        return parentScope ?
                ( dataScope ?
                    (parentScope + '.' + dataScope) :
                    parentScope ) :
                dataScope;
    }

    function mergeNode(node, config){
        return $.extend(true, {}, config, node);
    }

    function additional(node){
        return _.pick(node, 'name', 'index', 'dataScope');
    }

    function Layout(nodes, types){
        this.types      = types;
        this.registry   = registry.create();

        this.run(nodes);
    }

    _.extend(Layout.prototype, {
        run: function(nodes, parent){
            _.each(nodes || [], this.iterator.bind(this, parent));

            return this;
        },

        iterator: function(parent, node, name){
            var action = typeof node === 'string' ?
                this.addChild :
                this.process;

            action.apply(this, arguments);
        },

        process: function(parent, node, name) {
            if(!parent && node.parent){
                return this.waitParent(node, name);
            }

            if(node.template){
                return this.waitTemplate.apply(this, arguments);      
            }

            node = this.build.apply(this, arguments);

            if(node){
                this.addChild(parent, node.name)
                    .manipulate(node)
                    .initComponent(node)
                    .run(node.children, node);
            }

            return this;
        },

        build: function(parent, node, name){
            var type;

            type = getNodeType.apply(null, arguments);
            node = mergeNode(node, this.types.get(type));

            node.index      = node.name || name;
            node.name       = getNodeName(parent, node, name);
            node.dataScope  = getDataScope(parent, node);

            delete node.type;

            this.registry.set(node.name, node);

            return node.isTemplate ? (node.isTemplate = false) : node;
        },

        initComponent: function(node){
            var source = node.component,
                component;

            if(source){

                registry.get(node.deps, function(){

                    require([source], function(constr){
                        component = new constr(
                            node.config,
                            additional(node)
                        );

                        registry.set(node.name, component);
                    });
                });
            }

            return this;
        }
    });
        
    _.extend(Layout.prototype, {
        waitTemplate: function(parent, node, name){
            var args = _.toArray(arguments);

            this.registry.get(node.template, function(){
                this.applyTemplate.apply(this, args);
            }.bind(this));
        },

        waitParent: function(node, name){
            var process = this.process.bind(this);

            this.registry.get(node.parent, function(parent){
                process(parent, node, name);
            });

            return this;
        },

        applyTemplate: function(parent, node, name){
            var template = this.registry.get(node.template);

            node = $.extend(true, {}, template, node);

            delete node.template;

            this.process(parent, node, name);
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

            if(node.insertTo){
                this.insertTo(name, node.insertTo);
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

        insertTo: function(item, targets){
            _.each(targets, function(info, target){
                this.insert(item, target, info.position);
            }, this);

            return this;
        },

        wrap: function(node, child){
            node = this.build('', node);

            this.insert(child.name, node.name)
                .run([node]);

            return this;
        },

        addChild: function(parent, child){
            if(parent && parent.component){
                this.insert(child, parent.name);
            }

            return this;
        }
    });

    return Layout;
});