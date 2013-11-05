<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter\FilterManager;

/**
 * Filter manager config interface
 */
interface ConfigInterface
{
    /**
     * Get list of factories
     *
     * @return string[]
     */
    public function getFactories();
}
