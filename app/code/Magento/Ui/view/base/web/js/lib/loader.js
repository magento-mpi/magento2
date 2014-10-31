/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(['jquery'], function($) {
    'use strict';
    
    var storage = window.localStorage;

    function getStoragePathFor(name, entity) {
        return '__' + entity + 'Cache__' + name;
    }

    /**
     * Converts arrayLikeObject to array
     * @param  {Object|Array} arrayLikeObject - target
     * @return {Array} - result array
     */
    function toArray(arrayLikeObject) {
        return Array.prototype.slice.call(arrayLikeObject);
    }

    /**
     * Formats path of type "path.to.template" to RequireJS compatible
     * @param  {String} path
     * @return {String} - formatted template path
     */
    function formatTemplatePath(path) {
        return 'text!' + path.replace(/^([^\/]+)/g, '$1/template') + '.html';
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
         * Loops over arguments and loads template for each.
         * @return {Deferred} - promise of templates to be loaded
         */
        loadTemplate: function() {
            var isLoaded  = $.Deferred(),
                templates = toArray(arguments);

            waitFor(templates.map(this._loadTemplate)).done(function () {
                templates = toArray(arguments);
                isLoaded.resolve.apply(isLoaded, templates);
            });

            return isLoaded.promise();
        },

        _loadTemplate: function (name) {
            var isLoaded    = $.Deferred(),
                storagePath = getStoragePathFor(name, 'template'),
                path        = formatTemplatePath(name),
                cached;

            if (cached) {
                setTimeout(function () {
                    isLoaded.resolve(cached);    
                }, 0)
            } else {
                require([path], function (template) {
                    storage.setItem(storagePath, template);
                    isLoaded.resolve(template);
                });
            }

            return isLoaded.promise();

        }
    }
});