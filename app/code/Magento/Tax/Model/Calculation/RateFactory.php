<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax rate factory
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Calculation;

class RateFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new tax rate model
     *
     * @param array $arguments
     * @return \Magento\Tax\Model\Calculation\Rate
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate', $arguments);
    }
}
