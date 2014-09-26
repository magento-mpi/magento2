/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore'
], function (_) {
    'use strict';

    function createItem(constr, config, data) {

        return new constr(config, data);
    }

    var collection = {

        apply: function (constr, config, name) {
            var layout,
                createItem;

            registry.get(['globalStorage', config.provider], function(storage, provider){

                layout     = storage.get().layout[name],
                createItem = createItem.bind(null, constr, config);

                if (layout.items) {
                    _.each(layout.items, createItem);
                }
            });
        },

        of: function (constr) {
            return this.apply.bind(this, constr);
        }
    };

    return collection;
});