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


class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address extends Enterprise_CustomerSegment_Model_Segment_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_customer_address');
    }
	
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('value'=>'enterprise_customersegment/segment_condition_customer_address_combine', 'label'=>Mage::helper('enterprise_customersegment')->__('Conditions Combination')),    
            Mage::getModel('enterprise_customersegment/segment_condition_customer_address_attributes')->getNewChildSelectOptions()
        );
        $conditions = array_merge_recursive(Mage_Rule_Model_Condition_Combine::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }
    
    public function loadValueOptions()
    {
        $options = array(
            'all'  => Mage::helper('enterprise_customersegment')->__('All'),
            'any'  => Mage::helper('enterprise_customersegment')->__('Any'),
            'primary_billing'  => Mage::helper('enterprise_customersegment')->__('Primary Billing'),
            'primary_shipping'  => Mage::helper('enterprise_customersegment')->__('Primary Shipping'),
        );
        $this->setValueOption($options);
        return $this;
    }

    
    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function asHtml()
    {
    	$html = $this->getTypeElement()->getHtml().
        Mage::helper('enterprise_customersegment')->__("If customer %s address(es) and match %s:",
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
        );
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
   
}

