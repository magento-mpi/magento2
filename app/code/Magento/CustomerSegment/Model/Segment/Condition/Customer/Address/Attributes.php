<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer\Address;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Customer address attributes selector
 */
class Attributes extends AbstractCondition
{
    /**
     * @var \Magento\Directory\Model\Config\Source\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Directory\Model\Config\Source\AllregionFactory
     */
    protected $_allregionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Customer\Model\Resource\Address
     */
    protected $_resourceAddress;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\Customer\Model\Resource\Address $resourceAddress
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\Config\Source\AllregionFactory $allregionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\Customer\Model\Resource\Address $resourceAddress,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory,
        \Magento\Directory\Model\Config\Source\AllregionFactory $allregionFactory,
        array $data = []
    ) {
        $this->_conditionFactory = $conditionFactory;
        $this->_resourceAddress = $resourceAddress;
        $this->_eavConfig = $eavConfig;
        $this->_countryFactory = $countryFactory;
        $this->_allregionFactory = $allregionFactory;
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Attributes');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return ['customer_address_save_commit_after', 'customer_address_delete_commit_after'];
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [];
        foreach ($this->loadAttributeOptions()->getAttributeOption() as $code => $label) {
            $conditions[] = ['value' => $this->getType() . '|' . $code, 'label' => $label];
        }
        $conditions = array_merge(
            $conditions,
            $this->_conditionFactory->create('Customer\Address\Region')->getNewChildSelectOptions()
        );
        return ['value' => $conditions, 'label' => __('Address Attributes')];
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $customerAttributes = $this->_resourceAddress->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        foreach ($customerAttributes as $attribute) {
            // skip "binary" attributes
            if (in_array($attribute->getFrontendInput(), ['file', 'image'])) {
                continue;
            }
            if ($attribute->getIsUsedForCustomerSegment()) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }
        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->_countryFactory->create()->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_allregionFactory->create()->toOptionArray();
                    break;

                default:
                    $options = [];
                    if (!$this->getData('value_select_options') && is_object($this->getAttributeObject())) {
                        if ($this->getAttributeObject()->usesSource()) {
                            if ($this->getAttributeObject()->getFrontendInput() == 'multiselect') {
                                $addEmptyOption = false;
                            } else {
                                $addEmptyOption = true;
                            }
                            $options = $this->getAttributeObject()->getSource()->getAllOptions($addEmptyOption);
                        }
                    }
                    break;
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Retrieve attribute element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Get input type for attribute operators.
     *
     * @return string
     */
    public function getInputType()
    {
        if (in_array($this->getAttribute(), ['country_id', 'region_id'])) {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }

        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'boolean':
                return 'select';
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
        }

        return 'string';
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        if (in_array($this->getAttribute(), ['country_id', 'region_id'])) {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }

        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'boolean':
                return 'select';
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
        }

        return 'text';
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return __('Customer Address %1', parent::asHtml());
    }

    /**
     * Retrieve attribute object
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeObject()
    {
        return $this->_eavConfig->getAttribute('customer_address', $this->getAttribute());
    }

    /**
     * Prepare customer address attribute condition select
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $attribute = $this->getAttributeObject();

        $select->from(['val' => $attribute->getBackendTable()], [new \Zend_Db_Expr(1)]);
        $condition = $this->getResource()->createConditionSql('val.value', $this->getOperator(), $this->getValue());
        $select->where(
            'val.attribute_id = ?',
            $attribute->getId()
        )->where(
            "val.entity_id = customer_address.entity_id"
        )->where(
            $condition
        );
        $select->limit(1);
        return $select;
    }
}
