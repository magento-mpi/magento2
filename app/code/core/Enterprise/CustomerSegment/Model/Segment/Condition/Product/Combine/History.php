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

class Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_History
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_product_combine_history');
    }

    public function getNewChildSelectOptions()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_product_combine')->setDateConditions(true)->getNewChildSelectOptions();
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array(
            'viewed_history'  => Mage::helper('enterprise_customersegment')->__('viewed'),
            'ordered_history' => Mage::helper('enterprise_customersegment')->__('ordered'),
        ));
        return $this;
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array(
            '=='  => Mage::helper('rule')->__('was'),
            '!='  => Mage::helper('rule')->__('was not')
        ));
        return $this;
    }

    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('If Product %s %s and matches %s of these Conditions:',
                $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    protected function _prepareConditionsSql($customer)
    {
        $select = $this->getResource()->createSelect();

        switch ($this->getValue()) {
            case 'ordered_history':
                $select->from(array('item' => $this->getResource()->getTable('sales/order_item')), array(new Zend_Db_Expr(1)));
                $select->joinInner(array('order' => $this->getResource()->getTable('sales/order')), 'item.order_id = order.entity_id', array());
                $select->where('order.customer_id = ?', $customer->getId());
                break;
            default:
                $select->from(array('item' => $this->getResource()->getTable('reports/viewed_product_index')), array(new Zend_Db_Expr(1)));
                $select->where('item.customer_id = ?', $customer->getId());
                break;
        }

        $select->limit(1);

        return $select;
    }

    protected function _getRequiredValidation()
    {
        return ($this->getOperator() == '==');
    }

    protected function _getProductSubfilterField()
    {
        return 'item.product_id';
    }

    protected function _getDateSubfilterField()
    {
        switch ($this->getValue()) {
            case 'ordered_history':
                return 'item.created_at';
            default:
                return 'item.added_at';
        }
    }
}
