'use strict';
var landing = angular.module('landing', []);
landing.controller('landingController', ['$scope', '$location', 'languageService', function ($scope, $location, languageService) {
    $scope.selectLanguage = function () {
        window.location = '/setup/' + $scope.modelLanguage.code + '/index';
    };
    languageService.load(function (response) {
        $scope.languages = response.data.languages;
        var indexOf = $location.absUrl().search('setup/') + 6;
        var value = $location.absUrl().slice(indexOf, indexOf + 5);
        $scope.languages.forEach(function (lang, index) {
            if (lang.code == value) {
                $scope.modelLanguage = $scope.languages[index];
            }
        });

    });

}])
.service('languageService', ['$http', function ($http) {
    return {
        languages: [],
        load: function (callback) {
            var self = this;
            $http.get('data/languages').success(function (data) {
                self.languages = data.languages;
            }).then(callback);
        }
    }
}]);
