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
 * Tax Class factory
 */
class Magento_Tax_Model_Class_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Type to class map
     *
     * @var array
     */
    protected $_types = array(
        Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER => 'Magento_Tax_Model_Class_Type_Customer',
        Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT => 'Magento_Tax_Model_Class_Type_Product',
    );

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param Magento_Tax_Model_Class $taxClass
     * @return Magento_Tax_Model_Class_Type_Interface
     * @throws Magento_Core_Exception
     */
    public function create(Magento_Tax_Model_Class $taxClass)
    {
        $taxClassType = $taxClass->getClassType();
        if (!array_key_exists($taxClassType, $this->_types)) {
            throw new Magento_Core_Exception(sprintf('Invalid type of tax class "%s"', $taxClassType));
        }
        return $this->_objectManager->create(
            $this->_types[$taxClassType],
            array('data' => array('id' => $taxClass->getId()))
        );
    }
}
