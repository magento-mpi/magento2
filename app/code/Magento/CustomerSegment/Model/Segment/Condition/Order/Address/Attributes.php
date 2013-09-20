<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order address attribute condition
 */
class Magento_CustomerSegment_Model_Segment_Condition_Order_Address_Attributes
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Array of Customer Address attributes used for customer segment
     *
     * @var array
     */
    protected $_attributes;

    /**
     * @var Magento_Directory_Model_Config_Source_CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var Magento_Directory_Model_Config_Source_AllregionFactory
     */
    protected $_allregionFactory;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Directory_Model_Config_Source_CountryFactory $countryFactory
     * @param Magento_Directory_Model_Config_Source_AllregionFactory $allregionFactory
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Eav_Model_Config $eavConfig,
        Magento_Directory_Model_Config_Source_CountryFactory $countryFactory,
        Magento_Directory_Model_Config_Source_AllregionFactory $allregionFactory,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_countryFactory = $countryFactory;
        $this->_allregionFactory = $allregionFactory;
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Order_Address_Attributes');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('sales_order_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array(
                'value' => $this->getType() . '|' . $code,
                'label' => $label
            );
        }

        return array(
            'value' => $conditions,
            'label' => __('Order Address Attributes')
        );
    }

    /**
     * Load attribute options
     *
     * @return Magento_CustomerSegment_Model_Segment_Condition_Order_Address_Attributes
     */
    public function loadAttributeOptions()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes  = array();

            $attributes = array();
            foreach ($this->_eavConfig->getEntityAttributeCodes('customer_address') as $attributeCode) {
                $attribute = $this->_eavConfig->getAttribute('customer_address', $attributeCode);
                if (!$attribute || !$attribute->getIsUsedForCustomerSegment()) {
                    continue;
                }
                // skip "binary" attributes
                if (in_array($attribute->getFrontendInput(), array('file', 'image'))) {
                    continue;
                }
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
                $this->_attributes[$attribute->getAttributeCode()] = $attribute;
            }

            $this->setAttributeOption($attributes);
        }

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
                    $options = $this->_countryFactory->create()
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_allregionFactory->create()
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Retrieve attribute element
     *
     * @return Magento_Data_Form_Element_Abstract
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
        switch ($this->getAttribute()) {
            case 'country_id': case 'region_id':
                return 'select';
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
        switch ($this->getAttribute()) {
            case 'country_id': case 'region_id':
                return 'select';
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
        return __('Order Address %1', parent::asHtml());
    }

    /**
     * Get order address attribute
     *
     * @return Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeObject()
    {
        $this->loadAttributeOptions();
        return $this->_attributes[$this->getAttribute()];
    }

    /**
     * Get condition query for order address attribute
     *
     * @param $customer
     * @param $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        if ($this->getAttributeObject()->getIsUserDefined()) {
            $tableAlias = 'extra_order_address';
        } else {
            $tableAlias = 'order_address';
        }

        return $this->getResource()->createConditionSql(
            sprintf('%s.%s', $tableAlias, $this->getAttribute()),
            $this->getOperator(),
            $this->getValue()
        );
    }
}
