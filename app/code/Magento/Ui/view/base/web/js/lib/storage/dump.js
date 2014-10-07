define([
    './storage',
    'Magento_Ui/js/lib/deferred_events'
], function (Storage, DeferredEvents) {
    return Storage.extend({}, DeferredEvents);
});