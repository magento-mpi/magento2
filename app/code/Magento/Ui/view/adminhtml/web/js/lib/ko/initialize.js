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
    './bind/autocomplete',
    './bind/on',
    './bind/scope',
    './bind/datepicker'
], function(ko, $, templateEngine) {
    'use strict';

    ko.setTemplateEngine(templateEngine);
    ko.applyBindings();

});