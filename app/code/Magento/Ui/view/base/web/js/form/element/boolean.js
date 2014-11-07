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
        initListeners: function () {
            var onUniqueUpdate  = this.onUniqueUpdate.bind(this);
            
            __super__.initListeners.apply(this, arguments);

            if (this.unique) {
                this.provider.params.on('update:' + this.index, onUniqueUpdate);
            }

            return this;
        },

        getInititalValue: function(){
            return !!__super__.getInititalValue.apply(this, arguments);
        },

        store: function (value) {
            __super__.store.apply(this, arguments);

            if (this.unique && !_.isUndefined(value)) {
                this.setUnique();
            }

            return this;
        },

        setUnique: function () {
            var params  = this.provider.params,
                checked = this.value();

            if (checked) {
                params.set(this.index, this.name);    
            }

            return this;
        },

        onUniqueUpdate: function (name) {
            var checked = this.name === name;

            if (!checked) {
                this.value(undefined);
            }
        },
    });
});