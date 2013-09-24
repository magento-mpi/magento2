<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Config_Source_Class_Customer implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @param Magento_Tax_Model_Class_Source_CustomerFactory $taxCustomerFactory
     */
    public function __construct(Magento_Tax_Model_Class_Source_CustomerFactory $taxCustomerFactory)
    {
        $this->_taxCustomreFactory = $taxCustomerFactory;
    }

    /**
     * Retrieve a list of customer tax classes
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $classCustomer Magento_Tax_Model_Class_Source_Customer */
        $classCustomer = $this->_taxCustomreFactory->create();
        $taxClasses = $classCustomer->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => __('None')));
        return $taxClasses;
    }
}
