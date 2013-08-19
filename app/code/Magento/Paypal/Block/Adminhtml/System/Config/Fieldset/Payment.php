<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset renderer for PayPal solution
 */
class Magento_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment
    extends Magento_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * Add custom css class
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        $enabledString = $this->_isPaymentEnabled($element) ? ' enabled' : '';
        return parent::_getFrontendClass($element) . ' with-button' . $enabledString;
    }

    /**
     * Check whether current payment method is enabled
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isPaymentEnabled($element)
    {
        $groupConfig = $element->getGroup();
        $activityPath = isset($groupConfig['activity_path']) ? $groupConfig['activity_path'] : '';

        if (empty($activityPath)) {
            return false;
        }

        $isPaymentEnabled = (string)Mage::getSingleton('Magento_Backend_Model_Config')->getConfigDataValue($activityPath);

        return (bool)$isPaymentEnabled;
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading" ><div class="heading"><strong>' . $element->getLegend();

        $groupConfig = $element->getGroup();

        $html .= '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .= '</div>';

        $disabledAttributeString = $this->_isPaymentEnabled($element) ? '' : ' disabled="disabled"';
        $disabledClassString = $this->_isPaymentEnabled($element) ? '' : ' disabled';
        $htmlId = $element->getHtmlId();
        $html .= '<div class="button-container"><button type="button"' . $disabledAttributeString
            . ' class="button action-configure'
            . (empty($groupConfig['paypal_ec_separate']) ? '' : ' paypal-ec-separate')
            . $disabledClassString . '" id="' . $htmlId
            . '-head" onclick="paypalToggleSolution.call(this, \'' . $htmlId . "', '"
            . $this->getUrl('*/*/state') . '\'); return false;"><span class="state-closed">'
            . __('Configure') . '</span><span class="state-opened">'
            . __('Close') . '</span></button>';

        if (!empty($groupConfig['more_url'])) {
            $html .= '<a class="link-more" href="' . $groupConfig['more_url'] . '" target="_blank">'
                . __('Learn More') . '</a>';
        }
        if (!empty($groupConfig['demo_url'])) {
            $html .= '<a class="link-demo" href="' . $groupConfig['demo_url'] . '" target="_blank">'
                . __('View Demo') . '</a>';
        }

            $html .='</div></div>';

        return $html;
    }

    /**
     * Return header comment part of html for payment solution
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        return false;
    }
}
