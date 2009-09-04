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


class Enterprise_CustomerSegment_Model_Segment_Condition_Period_Daterange extends Enterprise_CustomerSegment_Model_Segment_Condition_Combine
{
    /**
     * Intialize model
     *
     * @return void
     */    
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_period_daterange');
        $this->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_combine')->getNewChildSelectOptions();
    }
    
    public function getInputType()
    {
        return 'text';
    }

    public function getValueElementType()
    {
        return 'text';
    }
    
    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        return true;
    }
    
    /**
     * Retrieve after element HTML
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';
        $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger" title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }
        return $html;
    }
    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = 'adminhtml/customersegment/chooser';
        if ($this->getJsFormObject()) {
            $url .= '/form/'.$this->getJsFormObject();
        }
        return Mage::helper('adminhtml')->getUrl($url);
    }
    
    
    public function asHtml()
    {
       $html = $this->getTypeElement()->getHtml().
       Mage::helper('enterprise_customersegment')->__("If period is from %s to (value) and match %s:",
              $this->getValueElement()->getHtml(),
              $this->getAggregatorElement()->getHtml()
       );
       $html.= $this->getRemoveLinkHtml();
       $html.= $this->getChooserContainerHtml();
       return $html;
    }    

}

