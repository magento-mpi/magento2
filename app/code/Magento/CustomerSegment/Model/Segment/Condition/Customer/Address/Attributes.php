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
 * Customer address attributes selector
 */
class Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Attributes
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
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
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Attributes');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('customer_address_save_commit_after', 'customer_address_delete_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_';
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value'=> $this->getType() . '|' . $code, 'label'=>$label);
        }
        $conditions = array_merge($conditions, Mage::getModel($prefix . 'Region')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label' => __('Address Attributes')
        );
    }

    /**
     * Load attribute options
     *
     * @return Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Attributes
     */
    public function loadAttributeOptions()
    {
        $customerAttributes = Mage::getResourceSingleton('Magento_Customer_Model_Resource_Address')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($customerAttributes as $attribute) {
            // skip "binary" attributes
            if (in_array($attribute->getFrontendInput(), array('file', 'image'))) {
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
                    $options = $this->_countryFactory->create()
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_allregionFactory->create()
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
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
        if (in_array($this->getAttribute(), array('country_id', 'region_id'))) {
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
        if (in_array($this->getAttribute(), array('country_id', 'region_id'))) {
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
     * @return Magento_Eav_Model_Entity_Attribute
     */
    public function getAttributeObject()
    {
        return $this->_eavConfig->getAttribute('customer_address', $this->getAttribute());
    }

    /**
     * Prepare customer address attribute condition select
     *
     * @param $customer
     * @param $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $attribute = $this->getAttributeObject();

        $select->from(array('val'=>$attribute->getBackendTable()), array(new Zend_Db_Expr(1)));
        $condition = $this->getResource()->createConditionSql(
            'val.value', $this->getOperator(), $this->getValue()
        );
        $select->where('val.attribute_id = ?', $attribute->getId())
            ->where("val.entity_id = customer_address.entity_id")
            ->where($condition);
        $select->limit(1);
        return $select;
    }
}
