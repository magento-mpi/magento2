define(['m2/lib/model', 'm2/controller'], function (Model) {
  return Model.extend({
    initialize: function (queue, scope) {
      this.set('query', queue);
    }
  });
})