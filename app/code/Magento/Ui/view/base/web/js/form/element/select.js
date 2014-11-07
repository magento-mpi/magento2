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

    var defaults = {
        template: 'ui/form/element/select'
    };

    var __super__ = Abstract.prototype;
    
    var inputNode = {
        name:  '{index}_input',
        type: 'input',
        parent: '{parentName}',
        config: {
            hidden: true,
            label: '{label}',
            listeners: {
                "params:{parentName}.{index}.hidden":{
                    "hide": {
                        "conditions": false
                    },
                    "show": {
                        "conditions": true
                    }
                }
            }
        }
    };

    function parseOptions(nodes){
        var caption;

        nodes = _.map(nodes, function(node) {
            if (node.value == null || node.value === '') {
                if (_.isUndefined(caption)) {
                    caption = node.label;
                }
            } else {
                return node;
            }
        });

        return {
            options: _.compact(nodes),
            caption: caption
        };
    }

    function findFirst(data){
        var value;

        data.some(function(node){
            value = node.value;

            if(Array.isArray(value)){
                value = findFirst(value);  
            }

            return !_.isUndefined(value);
        });

        return value;
    }

    return Abstract.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function (config) {
            _.extend(this, defaults);

            this.initOptions(config);

            __super__.initialize.apply(this, arguments);
   
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
            var node = utils.template(inputNode, this);

            this.renderer.render({
                layout: [node]
            });

            return this;
        },

        getInititalValue: function(){
            var value = __super__.getInititalValue.apply(this, arguments);

            if(value !== ''){
                return value;
            }
            else if(!this.caption){
                return findFirst(this.options);
            }
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

        setPreview: function(value){
            var option  = _.indexBy(this.options(), 'value')[value],
                preview = '';

            if(option){
                preview = option.label;
            }

            this.preview(preview);

            return this;
        }
    });
});