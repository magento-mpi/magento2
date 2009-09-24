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

class Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart_Amount
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'numeric';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_shoppingcart_amount');
        $this->setValue(null);
    }
    
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(), 
            'label'=>Mage::helper('enterprise_customersegment')->__('Shopping Cart Total'));
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'subtotal'  => Mage::helper('enterprise_customersegment')->__('Subtotal'),
            'grand_total'  => Mage::helper('enterprise_customersegment')->__('Grand Total'),
            'tax'  => Mage::helper('enterprise_customersegment')->__('Tax'),
            'shipping'  => Mage::helper('enterprise_customersegment')->__('Shipping'),
            'store_credit'  => Mage::helper('enterprise_customersegment')->__('Store Credit'),
            'gift_card'  => Mage::helper('enterprise_customersegment')->__('Gift Card'),
        ));
        return $this;
    }
    
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('Shopping Cart %s Amount %s %s:',
                $this->getAttributeElementHtml(), $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }    


    public function getConditionsSql($customer, $isRoot = false)
    {
        $table = $this->getResource()->getTable('sales/quote');
        $addressTable = $this->getResource()->getTable('sales/quote_address');

        $operator = $this->_getSqlOperator();

        $select = $this->getResource()->createSelect();
        $select->from(array('quote'=>$table), array(new Zend_Db_Expr(1)))
            ->limit(1);

        $joinAddress = false;

        switch ($this->getAttribute()) {
            case 'subtotal':
                $field = 'quote.base_subtotal';
                break;
            case 'grand_total':
                $field = 'quote.base_grand_total';
                break;
            case 'tax':
                $field = 'base_tax_amount';
                $joinAddress = true;
                break;
            case 'shipping':
                $field = 'base_shipping_amount';
                $joinAddress = true;
                break;
            case 'store_credit': 
                $field = 'quote.base_customer_balance_amount_used'; /* @TODO maybe without _used? */ 
                break;
            case 'gift_card':
                $field = 'quote.base_gift_cards_amount_used'; /* @TODO maybe without _used? */ 
                break;
            default:
                Mage::throwException(Mage::helper('enterprise_customersegment')->__('Unknown quote total specified'));
        }

        if ($joinAddress) {
            $subselect = $this->getResource()->createSelect();

            $subselect->from(
                array('address'=>$addressTable),
                array(
                    'quote_id' => 'quote_id',
                    $field     => new Zend_Db_Expr("SUM({$field})")
                )
            );

            $subselect->group('quote_id');

            $select->joinInner(array('address'=>$subselect), 'address.quote_id = quote.entity_id', array());

            $field = "address.{$field}";
        }

        $select->where("{$field} {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'customer_id', $isRoot));

        return $select;
    }
}
