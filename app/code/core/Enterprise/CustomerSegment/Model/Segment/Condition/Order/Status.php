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
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Status
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_status');
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

    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => Mage::helper('enterprise_customersegment')->__('Order Status')
        );
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array_merge(
            array('any' => Mage::helper('enterprise_customersegment')->__('Any')),
            Mage::getSingleton('sales/order_config')->getStatuses())
        );
        return $this;
    }

    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('Order Status %s %s:',
                $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    public function getAttributeObject()
    {
        return Mage::getSingleton('eav/config')->getAttribute('order', 'status');
    }

    public function getSubfilterType()
    {
        return 'order';
    }

    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();

        $select = $this->getResource()->createSelect();
        $select->from(array('main'=>$table), array('entity_id'));

        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select->where('main.attribute_id = ?', $attribute->getId())
            ->where("main.value {$operator} ?", $this->getValue());

        $inOperator = ($requireValid ? 'IN' : 'NOT IN');

        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}
