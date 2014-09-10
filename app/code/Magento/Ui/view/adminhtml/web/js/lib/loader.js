/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(['jquery'], function($) {

    return {
        loadTemplate: function(path) {
            var isLoaded = $.Deferred();

            path = 'text!' + path.replace(/(\.)/g, '/') + '.html';

            require([path], function(html) {
                isLoaded.resolve(html);
            });


            return isLoaded.promise();
        }
    }
});