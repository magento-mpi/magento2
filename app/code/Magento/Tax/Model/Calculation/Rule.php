<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Framework\Api\MetadataServiceInterface;
use \Magento\Tax\Api\Data\TaxRuleInterface;

/**
 * Tax Rule Model
 *
 * @method \Magento\Tax\Model\Resource\Calculation\Rule _getResource()
 * @method \Magento\Tax\Model\Resource\Calculation\Rule getResource()
 * @method int getPosition()
 * @method \Magento\Tax\Model\Calculation\Rule setPosition(int $value)
 */
class Rule extends \Magento\Framework\Model\AbstractExtensibleModel implements TaxRuleInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'tax_rule';

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
     * @var \Magento\Tax\Model\Calculation\Rule\Validator
     */
    protected $validator;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param MetadataServiceInterface $metadataService
     * @param \Magento\Tax\Model\ClassModel $taxClass
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param Rule\Validator $validator
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        MetadataServiceInterface $metadataService,
        \Magento\Tax\Model\ClassModel $taxClass,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Tax\Model\Calculation\Rule\Validator $validator,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_calculation = $calculation;
        $this->validator = $validator;
        parent::__construct($context, $registry, $metadataService, $resource, $resourceCollection, $data);
        $this->_init('Magento\Tax\Model\Resource\Calculation\Rule');
        $this->_taxClass = $taxClass;
    }

    /**
     * After save rule
     * Re-declared for populate rate calculations
     *
     * @return $this
     */
    public function afterSave()
    {
        parent::afterSave();
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
    public function afterDelete()
    {
        $this->_eventManager->dispatch('tax_settings_change_after');
        return parent::afterDelete();
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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return (int) $this->getData('sort_order');
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculateSubtotal()
    {
        return (bool) $this->getData('calculate_subtotal');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerTaxClassIds()
    {
        return $this->_getUniqueValues($this->getCustomerTaxClasses());
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTaxClassIds()
    {
        return $this->_getUniqueValues($this->getProductTaxClasses());
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRateIds()
    {
        return $this->_getUniqueValues($this->getRates());
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getData('priority');
    }

    /**
     * Get unique values of indexed array.
     *
     * @param array|null $values
     * @return array|null
     */
    protected function _getUniqueValues($values)
    {
        if (!$values) {
            return null;
        }
        return array_values(array_unique($values));
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
