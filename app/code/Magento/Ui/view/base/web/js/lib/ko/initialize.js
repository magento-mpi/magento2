/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** Loads all available knockout bindings, sets custom template engine, initializes knockout on page */
define([
    'ko',
    './template/engine',
    './bind/date',
    './bind/scope',
    './bind/datepicker',
    './bind/stop_propagation',
    './bind/outer_click',
    './bind/class',
    './bind/on_enter'
], function(ko, templateEngine) {
    'use strict';

    ko.setTemplateEngine(templateEngine);
    ko.applyBindings();

});