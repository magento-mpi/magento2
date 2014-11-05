/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'mage/utils',
    './abstract'
], function (_, utils, Abstract) {
    'use strict';

    var __super__ = Abstract.prototype;
    
    var inputNode = {
        name:       '{index}_input',
        type:       'input',
        parent:     '{parentName}'
    };

    var inputConfig = {
        hidden: true,
        label:  '{label}'
    };

    function hasLeafNode(nodes){
        return _.some(nodes, function(node){
            return typeof node.value === 'object';
        });
    }

    function parseOptions(nodes){
        var caption;

        nodes = _.map(nodes, function(node){
            if(node.value == null){
                if(!caption){
                    caption = node.label;
                }
            }
            else if(node.value !== ''){
                return node;
            }
        });

        return {
            options: _.compact(nodes),
            caption: caption
        };
    }

    return Abstract.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function (config) {            
            this.initOptions(config);

            __super__.initialize.apply(this, arguments);

            this.formatInitialValue()
   
            if(this.customEntry){
                this.initInput();
            }
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.initialOptions = this.options;
            this.observe('options');

            this.setOptions(this.options());

            return this;
        },

        initOptions: function(config){
            var result = parseOptions(config.options);

            _.extend(config, result);

            return this;
        },

        initInput: function(){
            var node        = utils.template(inputNode, this),
                config      = utils.template(inputConfig, this),
                listeners   = {},
                event;

            event = "params:{parentName}."+ this.index +".hidden";
            listeners[event] = {
                "hide": false,
                "show": true
            };

            config.listeners = listeners;
            node.config      = config;

            this.renderer.render({
                layout: [node]
            });
            return this;
        },

        filter: function(field, value){
            var source = this.initialOptions,
                result;

            result = _.filter(source, function(item){
                return item[field] === value;
            });

            this.setOptions(result);
        },

        setOptions: function(data){
            var size = data.length;

            this.options(data);
            
            if(this.customEntry){
                this.setHidden(!size);
            }

            return this;
        },

        formatInitialValue: function() {
            var value = this.value(),
                option;

            if(_.isUndefined(value) && !this.caption){
                option = this.options[0];

                if(option){
                    value = option.value;
                }
            }

            this.initialValue = value;
            this.value(value);

            this.hasLeafNode = hasLeafNode(this.options);
            
            return this;
        }
    });
});