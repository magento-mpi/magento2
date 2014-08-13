define(['ng', '_', 'm2/controller'], function (ng, _, controller) {
  
  var View = function (scope) {
    this.scope = scope;
  };

  _.extend(View.prototype, {
    render: function () {
      this.scope.$apply();
    }
  });

  var generateModuleName = createNameGenerator('module');
  var generateControllerName = createNameGenerator('controller');

  function createNameGenerator(prefix) {
    var i = 0;

    return function () {
      return prefix + '__' + i++;
    }
  }

  return {
    init: function (el, model, scopeName) {
      var scope,
          moduleName = generateModuleName(),
          controllerName = generateControllerName();
          
      var module = ng.module(moduleName, []);

      module.controller(controllerName, function ($scope) {
        $scope.model = model;
        $scope.$watch('model.$siblings.simpleListing.props.query', function () {
          controller.update(scopeName);
        });
        scope = $scope;
      });

      el.setAttribute('ng-controller', controllerName);
      ng.bootstrap(el, [moduleName]);

      return new View(scope);
    }
  };
});