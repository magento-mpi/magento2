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


class Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_product_combine');
    }

    public function getNewChildSelectOptions()
    {
        $children = array_merge_recursive(
            parent::getNewChildSelectOptions(),
            array(
                array( // self
                    'value' => $this->getType(),
                    'label' => Mage::helper('rule')->__('Conditions Combination')
                )
            )
        );

        if ($this->getDateConditions()) {
            $children = array_merge_recursive(
                $children,
                array(
                    array(
                        'value' => array(
                            Mage::getModel('enterprise_customersegment/segment_condition_uptodate')->getNewChildSelectOptions(),
                            Mage::getModel('enterprise_customersegment/segment_condition_daterange')->getNewChildSelectOptions(),
                        ),
                        'label' => Mage::helper('enterprise_customersegment')->__('Date Ranges')
                    )
                )
            );
        }

        $children = array_merge_recursive(
            $children,
            array(
                Mage::getModel('enterprise_customersegment/segment_condition_product_attributes')->getNewChildSelectOptions()
            )
        );

        return $children;
    }

    public function getConditionsSql($customer, $isRoot = false) {
        return false;
    }

    public function getSubfilterType()
    {
        return 'product';
    }

    public function getSubfilterSql($fieldName, $requireValid)
    {
        $table = $this->getResource()->getTable('catalog/product');

        $select = $this->getResource()->createSelect();
        $select->from(array('main'=>$table), array('entity_id'));

        if ($this->getAggregator() == 'all') {
            $whereFunction = 'where';
        } else {
            $whereFunction = 'orWhere';
        }

        $gotConditions = false;

        foreach ($this->getConditions() as $condition) {
            if ($condition->getSubfilterType()) {
                switch ($condition->getSubfilterType()) {
                    case 'product':
                        $subfilter = $condition->getSubfilterSql('product.entity_id', ($this->getValue() == 1));
                        if ($subfilter) {
                            $select->$whereFunction($subfilter);
                            $gotConditions = true;
                        }
                        break;
                }
            }
        }

        if (!$gotConditions) {
            $select->where('1=1');
        }

        $inOperator = ($requireValid ? 'IN' : 'NOT IN');

        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}
