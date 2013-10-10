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
     * @var \Magento\Tax\Model\TaxClass\Source\CustomerFactory
     */
    protected $_taxCustomerFactory;

    /**
     * @param \Magento\Tax\Model\TaxClass\Source\CustomerFactory $taxCustomerFactory
     */
    public function __construct(\Magento\Tax\Model\TaxClass\Source\CustomerFactory $taxCustomerFactory)
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
        /** @var $classCustomer \Magento\Tax\Model\TaxClass\Source\Customer */
        $classCustomer = $this->_taxCustomreFactory->create();
        $taxClasses = $classCustomer->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => __('None')));
        return $taxClasses;
    }
}
