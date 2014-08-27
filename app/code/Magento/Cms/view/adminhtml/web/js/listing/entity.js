define(['Magento_Ui/js/components/listing', 'jquery', 'mage/translate'], function (Listing, $) {

  var t = $.mage.__;

  return Listing.extend({

    getSortableClassFor: function (heading) {
      var rule = heading.sorted;
      return rule ? 'sort-arrow-' + rule : 'not-sorted';
    },

    updateAttributes: function () {
      alert('You want to update items with ids: ' + this.checkedIds());
    },

    getViewTemplate: function () {
      return 'Magento_Cms.templates.pages_listing.' + this.view();
    },

    getStatusClassFor: function (row) {
      return row.status ? 'enabled' : 'disabled';
    },

    getStatusTextFor: function (status) {
      return t(status ? 'Enabled' : 'Disabled'); 
    }

  });
});