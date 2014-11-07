/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../collapsible'
], function(_, Collapsible) {
    'use strict';

    var defaults = {
        template: 'ui/fieldset/fieldset'
    };

    var __super__ = Collapsible.prototype;

    return Collapsible.extend({

        /**
         * Extends instance with default config, binds required methods
         *     to instance, calls initialize method of parent class.
         */
        initialize: function() {
            _.extend(this, defaults);

            _.bindAll(this, 'onInvalidUpdate', 'onElementUpdate');

            __super__.initialize.apply(this, arguments);
        },

        /**
         * Calls initListeners of parent class, inits instance's listeners
         * 
         * @return {Object} - reference to instance
         */
        initListeners: function() {
            __super__.initListeners.apply(this, arguments);

            this.provider.params.on('update:invalid', this.onInvalidUpdate);

            return this;
        },

        /**
         * Calls parent's initElement method.
         * Assignes callbacks on various events of incoming element.
         * 
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        initElement: function(elem) {
            __super__.initElement.apply(this, arguments);

            elem.on('update', this.onElementUpdate);

            return this;
        },

        /**
         * Is being called on child element's update event, triggers update event
         *     passing hasChanged flag to it
         */
        onElementUpdate: function() {
            var changed = this.delegate('hasChanged', 'some');

            this.trigger('update', changed);
        },

        /**
         * Is being called on invalid event of params storage,
         *     if fieldset contains incoming element, opens inself
         * 
         * @param  {Object} elem
         */
        onInvalidUpdate: function(elem) {
            var containsInvalid = this.delegate('contains', 'some', elem);

            if (containsInvalid) {
                this.opened(true);
            }
        }
    });
});