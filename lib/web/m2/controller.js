define(['m2/lib/utils'], function (utils) {
  var registry = {};

  return {
    registerModel: function (model, scope) {
      this._addModelTo(scope, model);
    },

    get: function (scope) {
      return utils.getValueByPathIn(registry, scope + '.index');
    },

    _addModelTo: function (scope, model) {
      utils.setValueByPathIn(registry, scope, model);
    }
  }
});



  
  //registry
  {
    cms: [modelOne, modelTwo]
  }

  controller.register('cms.pages', modelThree);

  //registry
  {
    cms: {
      index: [modelOne, modelTwo],
      pages: {
        index: [modelThree]
      }
    }
  }

