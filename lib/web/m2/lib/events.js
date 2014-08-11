define(['_'], function (_) {

  var EventBus = function () {
    this.events = {};
  }

  _.extend(EventBus.prototype, {
    on: function (type, fn, context, once) {
      this.events[type] = this.events[type] || [];
      
      this.events[type].push({
        fn: fn,
        context: context,
        once: once
      });
    },

    once: function (type, fn, context) {
      this.on.call(this, type, fn, context, true);
    },

    off: function (type, fn) {
      var position;
      var fns = this.events[type];

      if (fn) {
        position = fns.indexOf(fn);
        fns.splice(position, 1);
      } else {
        delete this.events[type];
      }
    },

    trigger: function (type) {
      var args = Array.prototype.slice.call(arguments, 1),
          handlers = this.events[type],
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
  });

  return EventBus;
});