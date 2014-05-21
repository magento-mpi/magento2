/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Update config.paths by fixing relative paths basing on the context
 */
var mageUpdateConfigPaths = function(config, context)
{
    "use strict";
    if (config.paths) {
        if (context) {
            context += '/';
        }
        for (var key in config.paths) {
            if (config.paths[key].substr(0, 2) == './') {
                config.paths[key] = context + config.paths[key].substr(2);
            }
        }
    }
    return config;
};
