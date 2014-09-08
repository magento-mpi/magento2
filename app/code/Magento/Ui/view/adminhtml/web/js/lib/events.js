define(['_'], function(_) {

    var events = {};

    return {
        on: function(type, fn, context, once) {
            events[type] = events[type] || [];

            events[type].push({
                fn: fn,
                context: context,
                once: once
            });
        },

        off: function(type, fn) {
            var position;
            var fns = events[type];

            if (fn) {
                position = fns.indexOf(fn);
                fns.splice(position, 1);
            } else {
                delete events[type];
            }
        },

        once: function(type, fn, context) {
            this.on.call(this, type, fn, context, true);
        },

        trigger: function(type) {

            var
                args = Array.prototype.slice.call(arguments, 1),
                handlers = events[type],
                handler,
                oncePosition;

            if (!handlers) return;

            for (var i = 0; i < handlers.length; i++) {
                handler = handlers[i];
                handler.fn.apply(handler.context, args);

                if (handler.once) {
                    oncePosition = handlers.indexOf(handler);
                    handlers.splice(oncePosition, 1);
                }
            }
        }
    }
});