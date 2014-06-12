<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config;

/**
 * @codeCoverageIgnore
 */
class GlobWrapper
{
    /**
     * Find pathnames matching a pattern
     *
     * @param string $path
     * @param int $flag
     * @return array
     */
    public function glob($path, $flag = 0)
    {
        return glob($path, $flag);
    }
}
