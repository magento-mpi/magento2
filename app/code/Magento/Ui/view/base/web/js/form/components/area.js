/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './tab'
], function(_, Tab) {
    'use strict';

    var defaults = {
        template:   'ui/area',
        storeAs:    'activeArea',
        changed:    false,
        loading:    false
    };

    var __super__ = Tab.prototype;

    return Tab.extend({

        /**
         * Extends instance with defaults. Invokes parent initialize method.
         * Calls initListeners and pushParams methods.
         */
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function() {
            __super__.initObservable.apply(this, arguments);

            this.observe('changed loading');

            return this;
        },

        /**
         * Calls parent's initElement method.
         * Assignes callbacks on various event of incoming element.
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on({
                'update':     this.onChildrenUpdate.bind(this),
                'loading':    this.onContentLoading.bind(this, true),
                'loaded':     this.onContentLoading.bind(this, false)
            });

            return this;
        },

        /**
         * Is being invoked on children update.
         * Sets changed property to one incoming.
         * Invokes setActive method if settings contain makeVisible property
         *     set to true.
         * 
         * @param  {Boolean} changed
         */
        onChildrenUpdate: function(changed){
            this.changed(changed);
        },

        /**
         * Sets loading property to true of false based on finished parameter.
         * @param  {Boolean} finished
         */
        onContentLoading: function(finished){
            this.loading(finished);
        }
    });
});