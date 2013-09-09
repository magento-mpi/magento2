<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Segment condition for sales rules
 */
class Magento_CustomerSegment_Model_Segment_Condition_Segment extends Magento_Rule_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'multiselect';

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = array(
                'multiselect' => array('==', '!=', '()', '!()'),
            );
            $this->_arrayInputTypes = array('multiselect');
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Render chooser trigger
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'
            . $this->_viewUrl->getViewFileUrl('images/rule_chooser_trigger.gif')
            . '" alt="" class="v-middle rule-chooser-trigger" title="'
            . __('Open Chooser') . '" /></a>';
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
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return Mage::helper('Magento_Adminhtml_Helper_Data')->getUrl('adminhtml/customersegment/chooserGrid', array(
            'value_element_id' => $this->_valueElement->getId(),
            'form' => $this->getJsFormObject(),
        ));
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
     * Render element HTML
     *
     * @return string
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();
        return $this->getTypeElementHtml()
            . __('If Customer Segment %1 %2', $this->getOperatorElementHtml(), $this->_valueElement->getHtml())
            . $this->getRemoveLinkHtml()
            . '<div class="rule-chooser" url="' . $this->getValueElementChooserUrl() . '"></div>';
    }

    /**
     * Specify allowed comparison operators
     *
     * @return Magento_CustomerSegment_Model_Segment_Condition_Segment
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array(
            '=='  => __('matches'),
            '!='  => __('does not match'),
            '()'  => __('is one of'),
            '!()' => __('is not one of'),
        ));
        return $this;
    }

    /**
     * Present selected values as array
     *
     * @return array
     */
    public function getValueParsed()
    {
        $value = $this->getData('value');
        $value = array_map('trim', explode(',', $value));
        return $value;
    }

    /**
     * Validate if qoute customer is assigned to role segments
     *
     * @param   Magento_Sales_Model_Quote_Address $object
     * @return  bool
     */
    public function validate(Magento_Object $object)
    {
        if (!Mage::helper('Magento_CustomerSegment_Helper_Data')->isEnabled()) {
            return false;
        }
        $customer = null;
        if ($object->getQuote()) {
            $customer = $object->getQuote()->getCustomer();
        }
        if (!$customer) {
            return false;
        }

        $quoteWebsiteId = $object->getQuote()->getStore()->getWebsite()->getId();
        if (!$customer->getId()) {
            $visitorSegmentIds = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerSegmentIds();
            if (is_array($visitorSegmentIds) && isset($visitorSegmentIds[$quoteWebsiteId])) {
                $segments = $visitorSegmentIds[$quoteWebsiteId];
            } else {
                $segments = array();
            }
        } else {
            $segments = Mage::getSingleton('Magento_CustomerSegment_Model_Customer')
                ->getCustomerSegmentIdsForWebsite($customer->getId(), $quoteWebsiteId);
        }
        return $this->validateAttribute($segments);
    }
}
