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
 * Abstract Tax Class
 */
abstract class Mage_Tax_Model_Class_TypeAbstract extends Magento_Object implements Mage_Tax_Model_Class_Type_Interface
{
    /**
     * @var Mage_Tax_Model_Calculation_Rule
     */
    protected $_calculationRule;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType;

    /**
     * @param Mage_Tax_Model_Calculation_Rule $calculationRule
     * @param array $data
     */
    public function __construct(Mage_Tax_Model_Calculation_Rule $calculationRule, array $data = array())
    {
        parent::__construct($data);
        $this->_calculationRule = $calculationRule;
    }

    /**
     * Get Collection of Tax Rules that are assigned to this tax class
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getAssignedToRules()
    {
        return $this->_calculationRule
            ->getCollection()
            ->setClassTypeFilter($this->_classType, $this->getId());
    }
}
