'use strict';
angular.module('magentoSetup', ['ngSanitize'])
    .service('License', function ($http) {
        return {
            load: function() {
                return $http.get('license');
            }
        }
    })
    .service('Menu', function ($http, $rootScope, $compile) {
        return {
            load: function() {
                return $http.get('menu');
            },
            loadMenuContent: function(item) {
                console.debug([1, item]);
                $http.get(item.name)
                    .success(function (data) {
                        $rootScope.$broadcast('updateContent', data);
                    });
            },
        }
    })
    .service('Page', function ($http, Menu, License, $rootScope) {
        var parent = this;
        parent.currentStep = {};
        parent.menu = [];
        parent.getMenuByName = function(name) {
            this.current = {next: null};
            this.name = name;
            angular.forEach(parent.menu, function(value, key) {
                if (value.name === this.name) {
                    this.current = value;
                }
            }, this);
            return this.current;
        };
        var obj = {
            load: function() {
                if (parent.menu.length == 0) {
                    Menu.load().success(function(data) {
                        angular.forEach(data, function(value, key) {
                            this.menu.push(value);
                        }, parent);
                    });
                }
                return this;
            },
            loadMenu: function() {
                this.load();
                return parent.menu;
            },
            setStep: function(step, scope) {
                if (step == 'license') {
                    License.load().success(function (data) {
                        scope.html = data;
                    });
                }
                parent.currentStep.name = step;
            },
            nextStep: function() {
                var currentStep = parent.currentStep;
                var menu = parent.getMenuByName(currentStep.name);
                if (menu.next !== null) {
                    currentStep.name = menu.next;
                    Menu.loadMenuContent(parent.getMenuByName(currentStep.name));
                } else {
                    console.debug('Finish');
                }
            },
        };

        $rootScope.$on('changeStep', function(event, args) {
            obj.nextStep();
        });
        return obj;
    })
    .controller('navigation', function ($scope, Menu) {
        var self = this;
        self.current = 0;
        self.max = 0;
        Menu.load()
            .success(function(data) {
                self.items = data;
            });

        this.loadMenuContent = function(item, index) {
            Menu.loadMenuContent(item);
            self.current = index !== null ? index : self.current + 1;
        };

        this.isActive = function(index) {
            return self.current === index;
        };

        this.isDisabled = function(index) {
            return index > self.max;
        };

        $scope.$on('changeStep', function() {
            self.current++;
            self.max++;o
        });
    })
    .controller('continue', function ($scope, $rootScope) {
        $scope.text = 'Continue';
        this.next = function() {
            $rootScope.$broadcast('changeStep', []);
        };
    })
    .controller('main', function ($scope, Page, $rootScope) {
        $rootScope.setup = {};
        Page.load().setStep('license', $scope);
        $rootScope.$on('updateContent', function(event, data) {
            $scope.html = data;
        });
    })
    .directive('dynamic', function ($compile) {
        return {
            restrict: 'A',
            replace: true,
            template: '<div style="height:600px; overflow-y: scroll;"></div>',
            link: function (scope, ele, attrs) {
                scope.$watch(attrs.dynamic, function(html) {
                    ele.html(html);
                    $compile(ele.contents())(scope);
                });
            }
        };
    });;