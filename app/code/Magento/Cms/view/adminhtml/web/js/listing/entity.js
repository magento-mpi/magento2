define(['m2/listing'], function (Listing) {
  return Listing.extend({
    initialize: function () {
      Listing.prototype.initialize.apply(this, arguments);
    },

    updateAttributes: function () {
      alert('You want to update items with ids: ' + this.checkedIds());
    }
  });
});