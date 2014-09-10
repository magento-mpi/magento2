<!--
/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
define(['jquery'], function($) {
    'use strict';

    return {
        remove: function(oldPart) {
            $(oldPart).remove();
        },

        replace: function(oldPart, newParts) {
            var newPart = _.last(newParts);

            $(oldPart).replaceWith(newPart);
        },

        body: function(oldPart, newParts) {
            var newPart = _.last(newParts);

            $(oldPart).replaceWith(newPart.children);
        },

        update: function(oldPart, newParts) {
            var newPart = _.last(newParts);

            var attributes = newPart.attributes;
            var value, name;

            _.each(attributes, function(attr) {
                value = attr.value;
                name = attr.name;

                if (attr.name.indexOf('data-part') !== -1) {
                    return;
                }

                $(oldPart).attr(name, value);
            });
        },

        prepend: function(oldPart, newParts) {
            newParts.forEach(function (node) {
                $(oldPart).prepend(node.children);
            });
        },

        append: function(oldPart, newParts) {
            $(oldPart).append(newParts.children);
        },

        getActions: function() {
            return 'replace remove body update append prepend'.split(' ');
        }
    };
});