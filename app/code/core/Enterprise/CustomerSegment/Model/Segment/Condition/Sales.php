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


class Enterprise_CustomerSegment_Model_Segment_Condition_Sales extends Enterprise_CustomerSegment_Model_Segment_Condition_Combine
{
    /**
     * Intialize model
     *
     * @return void
     */    
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_sales');
        $this->setValue(null);
    }

    /**
     * Return options for check new condition elemtnt
     *
     * @return array
     */    
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_sales_combine')->getNewChildSelectOptions();
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('enterprise_customersegment')->__('is'),
            '!='  => Mage::helper('enterprise_customersegment')->__('is not'),
        ));
        return $this;
    }
    
    public function loadValueOptions()
    {
        $options = Mage::getSingleton('sales/order_config')->getStatuses();
        $options = array_merge (array('any'=>Mage::helper('enterprise_customersegment')->__('Any')), $options);
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
    
    public function getExplicitApply()
    {
        return false;
    }
    
    public function asHtml()
    {
       $html = $this->getTypeElement()->getHtml().
       Mage::helper('enterprise_customersegment')->__("If order status %s %s and match %s:",
              $this->getOperatorElement()->getHtml(),
              $this->getValueElement()->getHtml(),
              $this->getAggregatorElement()->getHtml()
       );
       $html.= $this->getRemoveLinkHtml();
       return $html;
    }    
    
}

