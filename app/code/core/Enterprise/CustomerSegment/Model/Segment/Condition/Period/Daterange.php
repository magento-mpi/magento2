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

/**
 * Date range combo
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Period_Daterange
    extends Enterprise_CustomerSegment_Model_Segment_Condition_Combine
{
    /**
     * Input type for operator options
     *
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * Value form element
     *
     * @var Varien_Data_Form_Element_Text
     */
    private $_valueElement = null;

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

    /**
     * Inherited hierarchy getter
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_combine')->getNewChildSelectOptions();
    }

    /**
     * Value element type getter
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Enable chooser selection button
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * Avoid value distortion by possible options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        return array();
    }

    /**
     * Chooser button HTML getter
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'
            . Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger"'
            . 'title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
    }

    /**
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/customersegment/chooserDaterange', array(
            'value_element_id' => $this->_valueElement->getId(),
        ));
    }

    /**
     * Render as HTML
     *
     * Chooser div is declared in such a way, that element value will be treated as is
     *
     * @return string
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('If Date Range %s %s and %s of these Conditions Match:',
                $this->getOperatorElementHtml(), $this->_valueElement->getHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml()
            . '<div class="rule-chooser no-split" url="' . $this->getValueElementChooserUrl() . '"></div>';
    }
}
