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
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initListeners: function () {
            __super__.initListeners.apply(this, arguments);

            this.provider.params.on('update:invalidElement', this.onInvalidUpdate.bind(this));
        },

        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on('update', this.onElementUpdate.bind(this));

            return this;
        },

        onElementUpdate: function(){
            var changed = this.delegate('hasChanged', 'some');

            this.trigger('update', changed);
        },

        onInvalidUpdate: function (invalidElement) {
            var containsInvalid = this.delegate('contains', 'some', invalidElement);

            if (containsInvalid) {
                this.opened(true);
            }
        }
    });
});