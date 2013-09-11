<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax rate factory
 *
 * @category   Magento
 * @package    Magento_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Calculation;

class RateFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
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
