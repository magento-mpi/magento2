<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Mage
 * @package    Mage_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Intialize model
     *
     * @return void
     */    
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_combine');
    }
    
    /**
     * Return options for check new condition elemtnt
     *
     * @return array
     */    
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('value'=>'enterprise_customersegment/segment_condition_combine', 'label'=>Mage::helper('enterprise_customersegment')->__('Conditions Combination')),
            array('value'=>'enterprise_customersegment/segment_condition_period_uptodate', 'label'=>Mage::helper('enterprise_customersegment')->__('Up to Date Combination')),
            array('value'=>'enterprise_customersegment/segment_condition_period_daterange', 'label'=>Mage::helper('enterprise_customersegment')->__('Date Range Combination')),
            array('value'=>'enterprise_customersegment/segment_condition_sales', 'label'=>Mage::helper('enterprise_customersegment')->__('Sales')),
            array('value'=>'enterprise_customersegment/segment_condition_isproductin', 'label'=>Mage::helper('enterprise_customersegment')->__('Is Product In')),
            array('value'=>'enterprise_customersegment/segment_condition_customer_address', 'label'=>Mage::helper('enterprise_customersegment')->__('Customer Address')),            
            array('value'=>'enterprise_customersegment/segment_condition_order_address', 'label'=>Mage::helper('enterprise_customersegment')->__('Order Address')),            
            Mage::getModel('enterprise_customersegment/segment_condition_shoppingcart')->getNewChildSelectOptions(),
            Mage::getModel('enterprise_customersegment/segment_condition_customer')->getNewChildSelectOptions(),
        );
        return $conditions;
    }

    
    /**
     * Return check new condition elemtnt
     *
     * @return Varien_Data_Form_Element_Hidden
     */    
    public function getNewChildElement()
    {
        $id = $this->getPrefix() . '__' . $this->getId() . '__new_child'; 
        $name = 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][new_child]';
        
        return $this->getForm()->addField($id, 'hidden', array(
            'name' => $name,
            'values' => $this->getNewChildSelectOptions(),
            'value_name' => $this->getNewChildName(),
        ))->setRenderer(Mage::getBlockSingleton('enterprise_customersegment/adminhtml_customersegment_edit_tab_conditions_newchild'));
    }

    
}

