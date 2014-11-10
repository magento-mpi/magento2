/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './storage',
    'Magento_Ui/js/lib/deferred_events'
], function (Storage, DeferredEvents) {
    return Storage.extend({}, DeferredEvents);
});