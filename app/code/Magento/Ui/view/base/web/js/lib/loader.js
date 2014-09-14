/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(['jquery'], function($) {
    'use strict';

    var storage = window.localStorage;

    function generateStorageNameFor(path) {
        return '__templateCache__' + path;
    }

    /**
     * Formats path of type "path.to.template" to RequireJS compatible
     * @param  {String} path
     * @return {String} - formatted template path
     */
    function formatTemplatePath(path) {
        return 'text!' + path.replace(/(\.)/g, '/') + '.html';
    }

    /**
     * Waits for all items in passed array of promises to resolve.
     * @param  {Array} promises - array of promises
     * @return {Deferred} - promise of promises to resolve
     */
    function waitFor(promises) {
        return $.when.apply(this, promises);
    }

    return {
        /**
         * Loads template by path.
         * @return {Deferred} - promise of template to be loaded
         */
        loadTemplate: function(path) {
            var isLoaded       = $.Deferred(),
                storagePath    = generateStorageNameFor(path),
                cachedTemplate = storage.getItem(storagePath);

            if (cachedTemplate) {
                isLoaded.resolve(cachedTemplate);
            } else {
                path = formatTemplatePath(path);

                require([path], function(html) {
                    storage.setItem(storagePath, html);
                    isLoaded.resolve(html);
                });
            }

            return isLoaded.promise();
        },

        loadTemplates: function (templates) {
            var isAllLoaded  = $.Deferred(),
                resolve      = isAllLoaded.resolve.bind(isAllLoaded);

            waitFor(templates.map(this.loadTemplate)).done(resolve);

            return isAllLoaded;
        }
    }
});