/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/component'
], function (Scope, Component) {
    'use strict';

    var Tab = Scope.extend({

        initialize: function (config, name) {
            this.initObservable();
        },

        initObservable: function () {

        }
    });

    return Component({
        constr: Tab
    });
});