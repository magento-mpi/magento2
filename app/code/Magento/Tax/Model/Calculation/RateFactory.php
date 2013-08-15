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
class Magento_Tax_Model_Calculation_RateFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new tax rate model
     *
     * @param array $arguments
     * @return Magento_Tax_Model_Calculation_Rate
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Tax_Model_Calculation_Rate', $arguments);
    }
}
