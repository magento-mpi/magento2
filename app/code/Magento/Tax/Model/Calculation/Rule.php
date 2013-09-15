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
 * @method \Magento\Tax\Model\Resource\Calculation\Rule _getResource()
 * @method \Magento\Tax\Model\Resource\Calculation\Rule getResource()
 * @method string getCode()
 * @method \Magento\Tax\Model\Calculation\Rule setCode(string $value)
 * @method int getPriority()
 * @method \Magento\Tax\Model\Calculation\Rule setPriority(int $value)
 * @method int getPosition()
 * @method \Magento\Tax\Model\Calculation\Rule setPosition(int $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Calculation;

class Rule extends \Magento\Core\Model\AbstractModel
{
    protected $_ctcs                = null;
    protected $_ptcs                = null;
    protected $_rates               = null;

    protected $_ctcModel            = null;
    protected $_ptcModel            = null;
    protected $_rateModel           = null;

    protected $_calculationModel    = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'tax_rule';

    /**
     * Helper
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_helper;

    /**
     * Tax Model Class
     *
     * @var \Magento\Tax\Model\ClassModel
     */
    protected $_taxClass;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param \Magento\Core\Model\Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\ClassModel $taxClass
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        \Magento\Core\Model\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\ClassModel $taxClass,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_init('Magento\Tax\Model\Resource\Calculation\Rule');

        $this->_helper = $taxHelper;
        $this->_taxClass = $taxClass;
    }

    /**
     * After save rule
     * Redeclared for populate rate calculations
     *
     * @return \Magento\Tax\Model\Calculation\Rule
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
     * @return \Magento\Tax\Model\Calculation\Rule
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

        \Mage::getSingleton('Magento\Tax\Model\Calculation')->deleteByRuleId($this->getId());
        foreach ($ctc as $c) {
            foreach ($ptc as $p) {
                foreach ($rates as $r) {
                    $dataArray = array(
                        'tax_calculation_rule_id'   =>$this->getId(),
                        'tax_calculation_rate_id'   =>$r,
                        'customer_tax_class_id'     =>$c,
                        'product_tax_class_id'      =>$p,
                    );
                    \Mage::getSingleton('Magento\Tax\Model\Calculation')->setData($dataArray)->save();
                }
            }
        }
    }

    public function getCalculationModel()
    {
        if (is_null($this->_calculationModel)) {
            $this->_calculationModel = \Mage::getSingleton('Magento\Tax\Model\Calculation');
        }
        return $this->_calculationModel;
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
        $customerClasses = $this->getAllOptionsForClass(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER);
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
        $productClasses = $this->getAllOptionsForClass(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT);
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

