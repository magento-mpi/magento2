define(function (require) {

  var
    Scope        = require('Magento_Ui/js/framework/ko/scope'),
    RestClient   = require('Magento_Ui/js/framework/rest/client'),
    LocalAdapter = require('Magento_Ui/js/framework/rest/adapter/local'),
    Resourceful  = require('Magento_Ui/js/framework/mixin/resourceful');
    
  var ID_ATTRIBUTE = 'id';

  return Scope.extend({

    mixins: [ Resourceful ],

    initialize: function (initial, config) {
      this
        .defArray('rows', initial.rows)
        .defArray('fields', initial.fields)
        .defArray('checkedIds')
        .def('currentAction')
        .def('view', 'grid');

      var adapter = new LocalAdapter(config.resource);
      this.client = new RestClient(adapter);
    },

    remove: function () {
      var idsToRemove = this.checkedIds();

      this.lock();
      this.client.remove(idsToRemove).done(function (removedIds) {
        this.reload()._unselect(removedIds);
      }.bind(this));
    },

    _unselectOne: function (id) {
      var position = this.checkedIds.indexOf(id);

      if (position >= 0) {
        this.checkedIds.splice(position, 1);
      }
    },

    _unselect: function (ids) {
      _.each(ids, this._unselectOne, this);
    },

    _select: function (ids) {
      this.checkedIds(ids || []);
    },

    select: function (rows) {
      var toSelect = _.pluck(rows, ID_ATTRIBUTE);
      this._select(toSelect);
    },

    selectAll: function () {
      this.client.read().done(this.select.bind(this));
    },

    unselectAll: function () {
      this._select([]);
    },

    unselectVisible: function () {
      return this.unselectAll();
    },
    
    selectVisible: function () {
      this.select(this.rows());
    },

    reload: function () {
      this.lock().client.read().done(function (rows) {
        this.unlock().rows(rows);
      }.bind(this));

      return this;
    },

    getCheckedQuantity: function () {
      return this.checkedIds().length;
    },

    isEmpty: function () {
      return !this.rows().length;
    },

    isView: function (type) {
      return this.view() === type;
    },

    getViewTemplate: function () {
      return 'Magento_Ui.templates.listing.' + this.view();
    }
  });
});