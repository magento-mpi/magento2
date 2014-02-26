<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Model\Resource\Grid;

class ActionsGroup implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Logging\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Logging\Model\Config $config
     */
    public function __construct(\Magento\Logging\Model\Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_config->getLabels();
    }
}
