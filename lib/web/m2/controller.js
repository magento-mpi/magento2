define(function () {
  var registry = Object.create({
    _get: function (scope) {
      this[scope] = this[scope] || this._createEmptyScope();
      return this[scope];
    },

    _createEmptyScope: function () {
      return {
        models: {},
        views: []
      }
    },

    getViewsFor: function (scope) {
      return this._get(scope).views;
    },

    getModelsFor: function (scope) {
      return this._get(scope).models;
    }
  });

  return {

    registerModel: function (name, model, scope) {
      var alias;
      var models = registry.getModelsFor(scope);

      for (alias in models) {
        if (models.hasOwnProperty(alias)) {
          models[alias].$siblings[name] = model;
          model.$siblings[alias] = models[alias]; 
        }
      }

      models[name] = model;

      model.on('change', this._getUpdaterFor(scope), this);
    },

    registerView: function (view, scopes) {
      scopes.forEach(function (scope) {
        registry.getViewsFor(scope).push(view);
      });
    },

    update: function (scope) {
      var views = registry.getViewsFor(scope);
      
      views.forEach(function (view) {
        view.render();
      });
    },

    _getUpdaterFor: function (scope) {
      return function () {
        this.update(scope);
      }
    }
  };
});