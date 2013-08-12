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
class Magento_Tax_Model_Class_Type_Customer
    extends Magento_Tax_Model_Class_TypeAbstract
    implements Magento_Tax_Model_Class_Type_Interface
{
    /**
     * @var Magento_Customer_Model_Group
     */
    protected $_modelCustomerGroup;

    /**
     * @var Magento_Tax_Helper_Data
     */
    protected $_helper;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER;

    /**
     * @param Magento_Tax_Model_Calculation_Rule $calculationRule
     * @param Magento_Customer_Model_Group $modelCustomerGroup
     * @param Magento_Tax_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Model_Calculation_Rule $calculationRule,
        Magento_Customer_Model_Group $modelCustomerGroup,
        Magento_Tax_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelCustomerGroup = $modelCustomerGroup;
        $this->_helper = $helper;
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
        return $this->_helper->__('customer group');
    }
}
