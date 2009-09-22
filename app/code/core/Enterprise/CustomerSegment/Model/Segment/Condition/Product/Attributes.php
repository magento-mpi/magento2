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


class Enterprise_CustomerSegment_Model_Segment_Condition_Product_Attributes extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    protected $_isUsedForRuleProperty = 'is_used_for_customer_segment';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_product_attributes');
        $this->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value'=> $this->getType() . '|' . $code, 'label'=>$label);
        }

        return array('value' => $conditions, 'label' => Mage::helper('enterprise_customersegment')->__('Product Attributes'));
    }

    public function asHtml()
    {
        return Mage::helper('enterprise_customersegment')->__('Product %s', parent::asHtml());
    }

    public function getAttributeObject()
    {
        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $this->getAttribute());
        } catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    public function getResource()
    {
        return Mage::getResourceSingleton('enterprise_customersegment/segment');
    }

    protected function _getSqlOperator()
    {
        /*
            '{}'  => Mage::helper('rule')->__('contains'),
            '!{}' => Mage::helper('rule')->__('does not contain'),
            '()'  => Mage::helper('rule')->__('is one of'),
            '!()' => Mage::helper('rule')->__('is not one of'),

            requires custom selects
        */

        switch ($this->getOperator()) {
            case "==":
                return '=';

            case "!=":
                return '<>';

            case ">":
            case "<":
            case ">=":
            case "<=":
                return $this->getOperator();

            default:
                Mage::throwException(Mage::helper('enterprise_customersegment')->__('Unknown operator specified'));
        }
    }

    public function getSubfilterType()
    {
        return 'product';
    }

    public function getSubfilterSql($fieldName, $requireValid)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();
        $addressTable = $this->getResource()->getTable('catalog/product');

        $select = $this->getResource()->createSelect();
        $select->from(array('main'=>$table), array('entity_id'));

        $operator = $this->_getSqlOperator();

        if ($attribute->getBackendType() == 'static') {
            $select->where("main.{$attribute->getAttributeCode()} {$operator} ?", $this->getValue());
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId())
                ->where("main.value {$operator} ?", $this->getValue());
        }

        $inOperator = ($requireValid ? 'IN' : 'NOT IN');

        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}
