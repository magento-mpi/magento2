/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery',
    './core/data_provider',
    '../lib/registry/registry'
], function($, DataProvider, registry) {
    'use strict';

    function getConfig(settings) {
        var config = settings.config,
            client = config.client = config.client || {};

        $.extend(true, client, {
            ajax: {
                data: {
                    name: settings.name,
                    form_key: FORM_KEY
                }
            }
        });

        return settings;
    }

    function init(el, settings) {
        var name    = settings.name,
            config  = getConfig(settings);

        registry.set(name, new DataProvider(config));
    }

    return init;
});