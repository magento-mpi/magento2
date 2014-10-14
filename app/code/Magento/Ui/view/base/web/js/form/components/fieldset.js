/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    '../collapsible'
], function(_, Collection, Collapsible) {
    'use strict';

    var __super__ = Collapsible.prototype;

    var Fieldset = Collapsible.extend({
        initialize: function() {
            this.template = 'ui/fieldset/fieldset';

            __super__.initialize.apply(this, arguments);
        },

        initListeners: function(){
            var update = this.onElementUpdate.bind(this);

            this.elems.forEach(function(elem){
                elem.on('update', update);
            });

            return this;
        },

        onElementUpdate: function(element, settings){
            var changed;

            this.elems.some(function(elem){
                return (changed = elem.hasChanged());
            });

            this.trigger('update', changed, this, settings);
        }
    });

    return Collection(Fieldset);
});