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
    public function glob($path, $flag = 0)
    {
        return glob($path, $flag);
    }
}
