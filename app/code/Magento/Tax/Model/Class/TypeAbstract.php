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
 * Abstract Tax Class
 */
abstract class Magento_Tax_Model_Class_TypeAbstract extends \Magento\Object implements Magento_Tax_Model_Class_Type_Interface
{
    /**
     * @var Magento_Tax_Model_Calculation_Rule
     */
    protected $_calculationRule;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType;

    /**
     * @param Magento_Tax_Model_Calculation_Rule $calculationRule
     * @param array $data
     */
    public function __construct(Magento_Tax_Model_Calculation_Rule $calculationRule, array $data = array())
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
