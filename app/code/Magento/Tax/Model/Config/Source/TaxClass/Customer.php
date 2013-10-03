<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Config\Source\TaxClass;

class Customer implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @param \Magento\Tax\Model\ClassModel\Source\CustomerFactory $taxCustomerFactory
     */
    public function __construct(\Magento\Tax\Model\ClassModel\Source\CustomerFactory $taxCustomerFactory)
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
        /** @var $classCustomer \Magento\Tax\Model\ClassModel\Source\Customer */
        $classCustomer = $this->_taxCustomreFactory->create();
        $taxClasses = $classCustomer->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => __('None')));
        return $taxClasses;
    }
}
