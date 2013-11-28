<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Url;

/**
 * Url Config Interface
 */
interface ConfigInterface
{
    /**
     * Get url config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path);
}
