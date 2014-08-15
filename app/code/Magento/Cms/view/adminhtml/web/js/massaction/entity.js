define(['m2/lib/scope', 'm2/provider', '_'], function (Scope, Provider, _) {
  return Scope.extend({
    initialize: function (massActions, actions) {
      this
        .defArray('actions', actions)
        .defArray('massActions', massActions)
        .def('currentAction')
        .def('currentMassAction')
        .def('listing', null);

      this
        ._bind()
        ._listen()
        ._load();
    },

    _bind: function () {
      _.bindAll(this, 'listing', '_applyMassAction');

      return this;
    },

    _listen: function () {
      this.currentMassAction.subscribe(this._applyMassAction, 'change');

      return this;
    },

    _load: function () {
      Provider.get('cms.pages.listing').done(this.listing);

      return this;
    },

    _applyMassAction: function (action) {
      var listing = this.listing();

      if (listing && action) {
        action = action.type;
        if (listing[action]) {
          listing[action].call(listing);  
        }
      }
    },

    applyAction: function () {
      var action = this.currentAction();
      var listing = this.listing();

      if (listing && action) {
        action = action.type;
        if (listing[action]) {
          listing[action].call(listing);  
        }
      }
    },

    getCheckedQuantity: function () {
      var listing = this.listing();

      return listing ? listing.getCheckedQuantity() : 0;
    }
  });
});