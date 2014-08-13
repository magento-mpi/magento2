define(['m2/lib/model'], function (Model) {
  return Model.extend({
    initialize: function (data) {
      this.set('items', data.items);
      this.set('query', data.query);
    },

    removeLast: function () {
      var items = this.get('items');
      var last = items.length - 1;

      items.splice(last, 1)
      this.set('items', items);
    }
  });
})