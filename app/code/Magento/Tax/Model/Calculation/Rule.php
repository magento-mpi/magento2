<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

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
 */
class Rule extends \Magento\Framework\Model\AbstractModel
{
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
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_calculation;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\ClassModel $taxClass
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\ClassModel $taxClass,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_calculation = $calculation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_init('Magento\Tax\Model\Resource\Calculation\Rule');

        $this->_helper = $taxHelper;
        $this->_taxClass = $taxClass;
    }

    /**
     * After save rule
     * Re-declared for populate rate calculations
     *
     * @return $this
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
     * Re-declared for dispatch tax_settings_change_after event
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        $this->_eventManager->dispatch('tax_settings_change_after');
        return parent::_afterDelete();
    }

    /**
     * @return void
     */
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
                        'tax_calculation_rule_id' => $this->getId(),
                        'tax_calculation_rate_id' => $r,
                        'customer_tax_class_id' => $c,
                        'product_tax_class_id' => $p
                    );
                    $this->_calculation->setData($dataArray)->save();
                }
            }
        }
    }

    /**
     * @return \Magento\Tax\Model\Calculation
     */
    public function getCalculationModel()
    {
        return $this->_calculation;
    }

    /**
     * @return array
     */
    public function getRates()
    {
        return $this->getCalculationModel()->getRates($this->getId());
    }

    /**
     * @return array
     */
    public function getCustomerTaxClasses()
    {
        return $this->getCalculationModel()->getCustomerTaxClasses($this->getId());
    }

    /**
     * @return array
     */
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
        $classes = $this->_taxClass->getCollection()->setClassTypeFilter($classFilter)->toOptionArray();

        return $classes;
    }

    /**
     * Fetches rules by rate, customer tax class and product tax class
     * and product tax class combination
     *
     * @param array $rateId
     * @param array $customerTaxClassIds
     * @param array $productTaxClassIds
     * @return array
     */
    public function fetchRuleCodes($rateId, $customerTaxClassIds, $productTaxClassIds)
    {
        return $this->getResource()->fetchRuleCodes($rateId, $customerTaxClassIds, $productTaxClassIds);
    }
}
