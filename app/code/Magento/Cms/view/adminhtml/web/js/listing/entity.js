define(['m2/listing'], function (Listing) {
  return Listing.extend({

    getSortableClassFor: function (heading) {
      var rule = heading.sorted;
      return rule ? 'sort-arrow-' + rule : 'not-sorted';
    },

    getTextFor: function (status) {
      return t(status ? 'Enabled' : 'Disabled'); 
    },

    updateAttributes: function () {
      alert('You want to update items with ids: ' + this.checkedIds());
    }
    
  });
});