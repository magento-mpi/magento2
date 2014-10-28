/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './abstract'
], function (_, Abstract) {
    'use strict';

    var defaults = {
        disabled: false
    };

    var __super__ = Abstract.prototype;
    
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
            else{
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
            _.extend(this, defaults);
            
            this.initOptions(config);

            __super__.initialize.apply(this, arguments);

            this.formatInitialValue();
        },

        initOptions: function(config){
            var result = parseOptions(config.options);

            _.extend(config, result);

            return this;
        },

        formatInitialValue: function() {
            this.hasLeafNode = hasLeafNode(this.options);
            
            return this;
        }
    });
});