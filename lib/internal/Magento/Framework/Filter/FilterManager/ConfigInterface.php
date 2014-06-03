<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Filter\FilterManager;

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
