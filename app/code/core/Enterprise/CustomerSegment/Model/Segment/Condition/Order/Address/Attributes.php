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
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Attributes
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_address_attributes');
        $this->setValue(null);
    }


    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value'=> $this->getType() . '|' . $code, 'label'=>$label);
        }

        return array('value' => $conditions, 'label'=>Mage::helper('enterprise_customersegment')->__('Order Address Attributes'));
    }

    /**
     * Load attribute options
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('sales/order_address')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if (/*$attribute->getIsUsedForCustomerSegment()*/$attribute->getFrontendLabel()) {
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
                    $options = Mage::getModel('adminhtml/system_config_source_country')
                        ->toOptionArray();
                    break;

                case 'region':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')
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


    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'country_id': case 'region':
                return 'select';
        }
        return 'string';
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'country_id': case 'region':
                return 'select';
        }
        return 'text';
    }

    public function asHtml()
    {
        return Mage::helper('enterprise_customersegment')->__('Order Address %s', parent::asHtml());
    }

    public function getAttributeObject()
    {
        return Mage::getSingleton('eav/config')->getAttribute('order_address', $this->getAttribute());
    }

    public function getConditionsSql($customer, $store)
    {
        $select = $this->getResource()->createSelect();

        $attribute = $this->getAttributeObject();

        $select->from(array('val'=>$attribute->getBackendTable()), array(new Zend_Db_Expr(1)));
        $select->limit(1);

        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select->where('val.attribute_id = ?', $attribute->getId())
            ->where("val.entity_id = order_address.entity_id")
            ->where("val.value {$operator} ?", $this->getValue());

        return $select;
    }
}
