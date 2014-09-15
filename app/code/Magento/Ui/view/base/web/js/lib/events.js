/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_'
], function(_) {

    function addHandler(events, callback, name) {
        (events[name] = events[name] || []).push(callback);
    }

    function getEvents(obj, name) {
        var events = obj._events = obj._events || {};

        return name ? events[name] : events;
    }

    return {
        on: function(name, callback) {
            var events = getEvents(this);

            typeof name === 'object' ?
                _.each(name, addHandler.bind(window, events)) :
                addHandler(events, callback, name);

            return this;
        },

        off: function(name) {
            var events      = getEvents(this),
                handlers    = events[name];

            if (Array.isArray(handlers)) {
                delete events[name];
            }

            return this;
        },

        trigger: function(name) {
            var handlers = getEvents(this, name),
                args;

            if (typeof handlers !== 'undefined') {
                args = Array.prototype.slice.call(arguments, 1);

                handlers.forEach(function(callback) {
                    callback.apply(this, args);
                });
            }

            return this;
        }
    }
});