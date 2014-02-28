/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configure RequireJs with fixed relative paths basing on the context
 *
 * @param  config
 * @param  context
 */
mageConfigRequireJs = function(config, context)
{
    if (config.paths) {
        if (typeof context == 'undefined') {
            context = '';
        } else {
            context += '/';
        }
        for (var key in config.paths) {
            if (config.paths[key].substr(0, 2) == './') {
                config.paths[key] = context + config.paths[key].substr(2);
            }
        }
    }
    require.config(config);
};
