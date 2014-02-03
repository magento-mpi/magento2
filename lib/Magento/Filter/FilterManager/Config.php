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
 * Filter plugin manager config
 */
class Config implements ConfigInterface
{
    /**
     * @var string[]
     */
    protected $factories = array(
        'Magento\Filter\Factory',
        'Magento\Filter\ZendFactory'
    );

    /**
     * @param string[] $factories
     */
    public function __construct(array $factories = array())
    {
        if (!empty($factories)) {
            $this->factories = array_merge($factories, $this->factories);
        }
    }

    /**
     * Get list of factories
     *
     * @return string[]
     */
    public function getFactories()
    {
        return $this->factories;
    }
}
