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
        dataScope: '{customEntry}',
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

    /**
     * Parses incoming options, considers options with undefined value property
     *     as caption
     *  
     * @param  {Array} nodes
     * @return {Object}
     */
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

    /**
     * Recursively loops over data to find non-undefined, non-array value
     * 
     * @param  {Array} data
     * @return {*} - first non-undefined value in array
     */
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
         *     If instance's 'customEntry' property is set to true, calls 'initInput'
         */
        initialize: function (config) {
            _.extend(this, defaults);

            this.initOptions(config);

            __super__.initialize.apply(this, arguments);
   
            if(this.customEntry){
                this.initInput();
            }
        },

        /**
         * Calls 'initObservable' of parent, initializes 'options' and 'initialOptions'
         *     properties, calls 'setOptions' passing options to it
         *     
         * @returns {Select} Chainable.
         */
        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.initialOptions = this.options;

            this.observe('options')
                .setOptions(this.options());

            return this;
        },

        /**
         * Parses options and merges the result with instance
         * 
         * @param  {Object} config
         * @returns {Select} Chainable.
         */
        initOptions: function(config){
            var result = parseOptions(config.options);

            _.extend(config, result);

            return this;
        },

        /**
         * Creates input from template, renders it via renderer.
         * 
         * @returns {Select} Chainable.
         */
        initInput: function(){
            this.renderer.render({
                layout: [
                    utils.template(inputNode, this)
                ]
            });

            return this;
        },

        /**
         * Calls 'getInitialValue' of parent and if the result of it is not empty
         * string, returs it, else returnes caption or first found option's value
         *     
         * @returns {Number|String}
         */
        getInititalValue: function(){
            var value = __super__.getInititalValue.apply(this, arguments);

            if(value !== ''){
                return value;
            }
            
            if(!this.caption){
                return findFirst(this.options);
            }
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it 
         * 
         * @param {String} field
         * @param {*} value
         */
        filter: function(field, value){
            var source = this.initialOptions,
                result;

            result = _.filter(source, function(item){
                return item[field] === value;
            });

            this.setOptions(result);
        },

        /**
         * Sets 'data' to 'options' observable array, if instance has 
         * 'customEntry' property set to true, calls 'setHidden' method
         *  passing !options.length as a parameter
         * 
         * @param {Array} data
         * @returns {Select} Chainable.
         */
        setOptions: function(data){
            var size = data.length;

            this.options(data);
            
            if(this.customEntry){
                this.setHidden(!size);
            }

            return this;
        },

        /**
         * Processes preview for option by it's value, and sets the result
         * to 'preview' observable
         * 
         * @param {String} value
         * @returns {Select} Chainable.
         */
        setPreview: function(value){
            var option  = this.options.indexBy('value')[value],
                preview = option ? option.label : '';
                
            this.preview(preview);

            return this;
        }
    });
});