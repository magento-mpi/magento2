<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Attributes
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_customer_attributes');
        $this->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value' => $this->getType() . '|' . $code, 'label' => $label);
        }

        return $conditions;
    }

    /**
     * Retrieve attribute object
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttributeObject()
    {

        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('customer', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('customer/customer'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    /**
     * Load condition options for castomer attributes
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Attributes
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('customer/customer')
            ->loadAllAttributes()
            ->getAttributesByCode();
            
        $attributes = array();
        
        foreach ($productAttributes as $attribute) {
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
        if (!$this->getData('value_select_options') && is_object($this->getAttributeObject())) {

            if ($this->getAttributeObject()->usesSource()) {
                if ($this->getAttributeObject()->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions($addEmptyOption);
                $this->setData('value_select_options', $optionsArr);
               }

            if ($this->_isCurrentAttributeDefaultAddress()) {
                $optionsArr = $this->_getOptionsForAttributeDefaultAddress();
                $this->setData('value_select_options', $optionsArr);
            }
            
        }
        return $this->getData('value_select_options');
    }

    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return 'select';
        }

        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'string';
        }
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return 'select';
        }

        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    /**
     * Retrieve value element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }
        }
        return $element;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }
        return false;
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
    
    public function getOperatorElementHtml()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return '';
        }
        
        return parent::getOperatorElementHtml();
    }
        
    protected function _isCurrentAttributeDefaultAddress()
    {
        return $this->getAttributeObject()->getAttributeCode() == 'default_billing' ||
        $this->getAttributeObject()->getAttributeCode() == 'default_shipping';
    }

    protected function _getOptionsForAttributeDefaultAddress()
    {
        return array(
            array('value' => 'is_exists', 'label' => Mage::helper('enterprise_customersegment')->__('exists')),
            array('value' => 'is_not_exists', 'label' => Mage::helper('enterprise_customersegment')->__('does not exist')),
        );
    }

    /**
     * Customer attributes are standalone conditions, hence they must be self-sufficient
     *
     * @return string
     */
    public function asHtml()
    {
        return Mage::helper('enterprise_customersegment')->__('Customer %s', parent::asHtml());
    }

    public function getConditionsSql($customer)
    {
        $attribute = $this->getAttributeObject();

        $table = $attribute->getBackendTable();

        $operator = $this->_getSqlOperator();

        $select = $this->_createSelect();
        $select->from($table, array(new Zend_Db_Expr(1)))
            ->where('entity_id = ?', $customer->getId())
            ->limit(1);

        if ($attribute->getBackendType() == 'static') {
            $select->where("{$attribute->getAttributeCode()} {$operator} ?", $this->getValue());
        } else {
            $select->where('attribute_id = ?', $attribute->getId())
                ->where("value {$operator} ?", $this->getValue());
        }

        return $select;
    }
}
