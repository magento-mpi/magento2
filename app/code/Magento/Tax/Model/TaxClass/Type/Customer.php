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
 * Customer Tax Class
 */
class Magento_Tax_Model_TaxClass_Type_Customer
    extends Magento_Tax_Model_TaxClass_TypeAbstract
    implements Magento_Tax_Model_TaxClass_Type_Interface
{
    /**
     * @var Magento_Customer_Model_Group
     */
    protected $_modelCustomerGroup;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER;

    /**
     * @param Magento_Tax_Model_Calculation_Rule $calculationRule
     * @param Magento_Customer_Model_Group $modelCustomerGroup
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Model_Calculation_Rule $calculationRule,
        Magento_Customer_Model_Group $modelCustomerGroup,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelCustomerGroup = $modelCustomerGroup;
    }

    /**
     * Get Customer Groups with this tax class
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getAssignedToObjects()
    {
        return $this->_modelCustomerGroup
            ->getCollection()
            ->addFieldToFilter('tax_class_id', $this->getId());
    }

    /**
     * Get Name of Objects that use this Tax Class Type
     *
     * @return string
     */
    public function getObjectTypeName()
    {
        return __('customer group');
    }
}
