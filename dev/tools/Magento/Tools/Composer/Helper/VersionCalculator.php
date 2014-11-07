<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Composer\Helper;

/**
 * A helper for calculating component version
 */
class VersionCalculator
{
    /**
     * Calculate component version based on root version and version setting in template
     *
     * @param string $rootVersion
     * @param string $value
     * @param bool $useWildcard
     * @return string
     */
    public static function calculateVersionValue($rootVersion, $value, $useWildcard)
    {
        $rootWildcard = preg_replace('/\.\d+$/', '.*', $rootVersion);
        if ($value === 'self.version' && $useWildcard) {
            $newValue = $rootWildcard;
        } else {
            $newValue = $value;
        }
        return $newValue;
    }
}
