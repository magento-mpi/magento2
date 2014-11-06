/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './abstract_select'
], function (_, Select) {
    'use strict';

    var defaults = {
        template: 'ui/form/element/select'
    };

    var __super__ = Select.prototype;

    return Select.extend({

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         */
        initialize: function () {
            _.extend(this, defaults);
            
            __super__.initialize.apply(this, arguments);
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