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
 * Tax Rule Model
 *
 * @method Magento_Tax_Model_Resource_Calculation_Rule _getResource()
 * @method Magento_Tax_Model_Resource_Calculation_Rule getResource()
 * @method string getCode()
 * @method Magento_Tax_Model_Calculation_Rule setCode(string $value)
 * @method int getPriority()
 * @method Magento_Tax_Model_Calculation_Rule setPriority(int $value)
 * @method int getPosition()
 * @method Magento_Tax_Model_Calculation_Rule setPosition(int $value)
 */
class Magento_Tax_Model_Calculation_Rule extends Magento_Core_Model_Abstract
{
    protected $_ctcs                = null;
    protected $_ptcs                = null;
    protected $_rates               = null;

    protected $_ctcModel            = null;
    protected $_ptcModel            = null;
    protected $_rateModel           = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'tax_rule';

    /**
     * Helper
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_helper;

    /**
     * Tax Model Class
     *
     * @var Magento_Tax_Model_Class
     */
    protected $_taxClass;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Tax_Model_Calculation
     */
    protected $_calculation;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Tax_Helper_Data $taxHelper
     * @param Magento_Tax_Model_Class $taxClass
     * @param Magento_Tax_Model_Calculation $calculation
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Tax_Helper_Data $taxHelper,
        Magento_Tax_Model_Class $taxClass,
        Magento_Tax_Model_Calculation $calculation,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_calculation = $calculation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_init('Magento_Tax_Model_Resource_Calculation_Rule');

        $this->_helper = $taxHelper;
        $this->_taxClass = $taxClass;
    }

    /**
     * After save rule
     * Redeclared for populate rate calculations
     *
     * @return Magento_Tax_Model_Calculation_Rule
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->saveCalculationData();
        $this->_eventManager->dispatch('tax_settings_change_after');
        return $this;
    }

    /**
     * After rule delete
     * redeclared for dispatch tax_settings_change_after event
     *
     * @return Magento_Tax_Model_Calculation_Rule
     */
    protected function _afterDelete()
    {
        $this->_eventManager->dispatch('tax_settings_change_after');
        return parent::_afterDelete();
    }

    public function saveCalculationData()
    {
        $ctc = $this->getData('tax_customer_class');
        $ptc = $this->getData('tax_product_class');
        $rates = $this->getData('tax_rate');

        $this->_calculation->deleteByRuleId($this->getId());
        foreach ($ctc as $c) {
            foreach ($ptc as $p) {
                foreach ($rates as $r) {
                    $dataArray = array(
                        'tax_calculation_rule_id'   =>$this->getId(),
                        'tax_calculation_rate_id'   =>$r,
                        'customer_tax_class_id'     =>$c,
                        'product_tax_class_id'      =>$p,
                    );
                    $this->_calculation->setData($dataArray)->save();
                }
            }
        }
    }

    /**
     * @return Magento_Tax_Model_Calculation
     */
    public function getCalculationModel()
    {
        return $this->_calculation;
    }

    public function getRates()
    {
        return $this->getCalculationModel()->getRates($this->getId());
    }

    public function getCustomerTaxClasses()
    {
        return $this->getCalculationModel()->getCustomerTaxClasses($this->getId());
    }

    public function getProductTaxClasses()
    {
        return $this->getCalculationModel()->getProductTaxClasses($this->getId());
    }

    /**
     * Check Customer Tax Class and if it is empty - use defaults
     *
     * @return int|array|null
     */
    public function getCustomerTaxClassWithDefault()
    {
        $customerClasses = $this->getAllOptionsForClass(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER);
        if (empty($customerClasses)) {
            return null;
        }

        $configValue = $this->_helper->getDefaultCustomerTaxClass();
        if (!empty($configValue)) {
            return $configValue;
        }

        $firstClass = array_shift($customerClasses);
        return isset($firstClass['value']) ? $firstClass['value'] : null;
    }

    /**
     * Check Product Tax Class and if it is empty - use defaults
     *
     * @return int|array|null
     */
    public function getProductTaxClassWithDefault()
    {
        $productClasses = $this->getAllOptionsForClass(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
        if (empty($productClasses)) {
            return null;
        }

        $configValue = $this->_helper->getDefaultProductTaxClass();
        if (!empty($configValue)) {
            return $configValue;
        }

        $firstClass = array_shift($productClasses);
        return isset($firstClass['value']) ? $firstClass['value'] : null;
    }

    /**
     * Get all possible options for specified class name (customer|product)
     *
     * @param string $classFilter
     * @return array
     */
    public function getAllOptionsForClass($classFilter)
    {
        $classes = $this->_taxClass
            ->getCollection()
            ->setClassTypeFilter($classFilter)
            ->toOptionArray();

        return $classes;
    }
}

