/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(['jquery'], function($) {

    function toArray(arrayLikeObject) {
        return Array.prototype.slice.call(arrayLikeObject);
    }

    function formatTemplatePath(path) {
        return 'text!' + path.replace(/(\.)/g, '/') + '.html';
    }

    return {
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