'use strict';
var landing = angular.module('landing', ['$localStorage']);
landing.controller('landingController', [
        '$scope',
        '$location',
        '$localStorage',
        'languageService',
        function ($scope, $location, $localStorage, languageService) {
            $scope.selectLanguage = function () {
                $localStorage.lang = $scope.modelLanguage.code;
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
        }
    ]
)
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
