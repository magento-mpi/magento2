/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(function() {
    return {
        lock: function() {
            this.isLocked(true);

            return this;
        },

        unlock: function() {
            this.isLocked(false);

            return this;
        }
    };
});