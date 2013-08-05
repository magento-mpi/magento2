<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Tax Class
 */
class Mage_Tax_Model_Class_Type_Customer
    extends Mage_Tax_Model_Class_TypeAbstract
    implements Mage_Tax_Model_Class_Type_Interface
{
    /**
     * @var Mage_Customer_Model_Group
     */
    protected $_modelCustomerGroup;

    /**
     * @var Mage_Tax_Helper_Data
     */
    protected $_helper;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER;

    /**
     * @param Mage_Tax_Model_Calculation_Rule $calculationRule
     * @param Mage_Customer_Model_Group $modelCustomerGroup
     * @param Mage_Tax_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Mage_Tax_Model_Calculation_Rule $calculationRule,
        Mage_Customer_Model_Group $modelCustomerGroup,
        Mage_Tax_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelCustomerGroup = $modelCustomerGroup;
        $this->_helper = $helper;
    }

    /**
     * Get Customer Groups with this tax class
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
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
