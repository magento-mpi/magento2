<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order address attribute condition
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Attributes
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Array of Customer Address attributes used for customer segment
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Attributes');
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
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Order Address Attributes')
        );
    }

    /**
     * Load attribute options
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes  = array();

            /* @var $config Mage_Eav_Model_Config */
            $config     = Mage::getSingleton('Mage_Eav_Model_Config');
            $attributes = array();

            foreach ($config->getEntityAttributeCodes('customer_address') as $attributeCode) {
                $attribute = $config->getAttribute('customer_address', $attributeCode);
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
                    $options = Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Country')
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Allregion')
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
     * @return Varien_Form_Element_Abstract
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
        return Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Order Address %s', parent::asHtml());
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
     * @return Varien_Db_Select
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
