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
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer\Address;

class Attributes
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Attributes');
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
        $conditions = array_merge($conditions, \Mage::getModel($prefix . 'Region')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label' => __('Address Attributes')
        );
    }

    /**
     * Load attribute options
     *
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Attributes
     */
    public function loadAttributeOptions()
    {
        $customerAttributes = \Mage::getResourceSingleton('Magento\Customer\Model\Resource\Address')
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
                    $options = \Mage::getModel('Magento\Directory\Model\Config\Source\Country')->toOptionArray();
                    break;

                case 'region_id':
                    $options = \Mage::getModel('Magento\Directory\Model\Config\Source\Allregion')->toOptionArray();
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
     * @return \Magento\Data\Form\Element\AbstractElement
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
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeObject()
    {
        return \Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute('customer_address', $this->getAttribute());
    }

    /**
     * Prepare customer address attribute condition select
     *
     * @param $customer
     * @param $website
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $attribute = $this->getAttributeObject();

        $select->from(array('val'=>$attribute->getBackendTable()), array(new \Zend_Db_Expr(1)));
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
