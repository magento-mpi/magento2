/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './collapsible'
], function(Collapsible) {
    'use strict';

    return Collapsible.extend({
        defaults: {
            template: 'ui/fieldset/fieldset'
        }
    });
});