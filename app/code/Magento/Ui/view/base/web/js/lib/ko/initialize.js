/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** Loads all available knockout bindings, sets custom template engine, initializes knockout on page */
define([
    'ko',
    'jquery',
    './template/engine',
    './bind/date',
    './bind/scope',
    './bind/datepicker',
    './bind/stop_propagation',
    './bind/outer_click'
], function(ko, $, templateEngine) {
    'use strict';

    ko.setTemplateEngine(templateEngine);
    ko.applyBindings();

});