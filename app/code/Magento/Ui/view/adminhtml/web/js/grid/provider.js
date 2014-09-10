define([
    'jquery',
    './core/data_provider',
    '../lib/registry/registry'
], function($, DataProvider, registry){
    'use strict';

    function getConfig(settings){
        var config,
            client;

        config = settings.config;
        client = config.client = config.client || {};

        $.extend(true, client, {
            ajax: {
                data: {
                    name: settings.name,
                    component: settings.component,
                    form_key: FORM_KEY
                }
            }
        });

        return settings;
    }

    function init( el, settings ){
        var name,
            config;

        name = settings.name;
        config = getConfig( settings );

        registry.set(name, new DataProvider(config));
    }

    return init;
});