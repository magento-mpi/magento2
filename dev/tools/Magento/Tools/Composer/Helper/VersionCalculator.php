<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        if ($value === 'self.version' && $useWildcard) {
            return preg_replace('/\.\d+$/', '.*', $rootVersion);
        } else {
            return $value;
        }
    }
}
