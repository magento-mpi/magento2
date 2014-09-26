/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './tab',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/collection/component'
], function (Tab, Scope, Collection) {
    'use strict';

    var TabGroup = Scope.extend({

        initialize: function (config, data) {
            _.extend(this, { config: config }, data);

            this
                .initObservable()
                .initTabs();
        },

        initObservable: function () {
            this.observe({
                collapsible: this.collapsible || false,
                isOpened:    this.opened      || true
            });

            return this;
        }
    });

    return Collection.of(TabGroup);
});