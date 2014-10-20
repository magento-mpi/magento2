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

    var __super__ = Collapsible.prototype;

    return Collapsible.extend({
        initialize: function() {
            this.template = 'ui/fieldset/fieldset';

            __super__.initialize.apply(this, arguments);
        },

        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on('update', this.onElementUpdate.bind(this));

            return this;
        },

        onElementUpdate: function(element, settings){
            var changed;

            this.elems().some(function(elem){
                return (changed = elem.hasChanged());
            });

            this.trigger('update', changed, this, settings);
        }
    });
});