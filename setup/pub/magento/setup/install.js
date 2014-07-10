'use strict';
var install = angular.module('install', ['ngStorage']);
addDatabase.controller('installController', ['$scope', '$localStorage', function ($scope, $localStorage) {
    $scope.start = false;
    $scope.console = false;
    $scope.toggleConsole = function () {
        $scope.console = $scope.console === false;
    }
}])
.service('progress', ['$http', function ($http) {
    return {
        get: function (callback) {
            $http.get('install/progress').then(callback);
        }
    }
}])
.directive('progressBar', ['progress', function (progress) {
    return {
        restrict: 'A',
        link: function (scope, element) {
            progress.get(function (response) {
                var pBar = element.children().children();
                pBar.text(response.data.progress + '%');
                pBar.attr('aria-valuenow', response.data.progress);
                pBar.css({'width': response.data.progress + '%'});
            });
        },
        template: '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div></div>'
    }
}])
.directive('console', ['progress', function (progress) {
    return {
        restrict: 'A',
        link: function (scope, element) {
            progress.get(function (response) {
                response.data.console.forEach(function (message) {
                    var pHTML = element.children().children().html();
                    element.children().children().html(pHTML + message + '<br>');
                });
            });
        },
        template: '<div class="highlight"><pre></pre></div>'
    }
}]);