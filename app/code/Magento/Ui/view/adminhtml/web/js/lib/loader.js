/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(['jquery'], function($) {
    'use strict';

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
        return 'text!' + path.replace(/(\.)/g, '/') + '.html';
    }

    return {
        /**
         * Loops over arguments and loads template for each.
         * @return {Deferred} - promise of templates to be loaded
         */
        loadTemplate: function() {
            var isLoaded = $.Deferred(),
                templates;

            templates = toArray(arguments);
            templates = templates.map(formatTemplatePath);

            require(templates, function() {
                templates = toArray(arguments);
                isLoaded.resolve.apply(isLoaded, templates);
            });

            return isLoaded.promise();
        }
    }
});