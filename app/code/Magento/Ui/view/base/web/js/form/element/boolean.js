/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract'
], function (Abstract) {
    'use strict';

    var __super__ = Abstract.prototype;

    return Abstract.extend({

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.initialValue = !!this.value();
            this.value(this.initialValue);

            return this;
        },

        initListeners: function () {
            var onUniqueUpdate  = this.onUniqueUpdate.bind(this);
            
            __super__.initListeners.apply(this, arguments);

            if (this.unique) {
                this.provider.params.on('update:' + this.index, onUniqueUpdate);
            }

            return this;
        },

        store: function (value) {
            var isUndefined = typeof value === 'undefined';

            __super__.store.apply(this, arguments);

            if (this.unique && !isUndefined) {
                this.setUnique();
            }

            return this;
        },

        setUnique: function () {
            var params      = this.provider.params,
                isActive    = this.value();

            if (isActive) {
                params.set(this.index, this.name);    
            }

            return this;
        },

        onUniqueUpdate: function (name) {
            var isActive = this.name === name;

            if (!isActive) {
                this.value(undefined);
            }
        },
    });
});