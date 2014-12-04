/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
var app = angular.module(
    'magentoSetup',
    [
        'ui.router',
        'ui.bootstrap',
        'main',
        'landing',
        'readiness-check',
        'add-database',
        'web-configuration',
        'customize-your-store',
        'create-admin-account',
        'install',
        'success'
    ]);

app.config(function ($stateProvider) {
    app.stateProvider = $stateProvider;
})
.config(function($provide) {
    $provide.decorator('$state', function($delegate, $stateParams) {
        $delegate.forceReload = function() {
            return $delegate.go($delegate.current, $stateParams, {
                reload: true,
                inherit: false,
                notify: true
            });
        };
        return $delegate;
    });
}).run(function ($rootScope, $state) {
    $rootScope.$state = $state;
});
