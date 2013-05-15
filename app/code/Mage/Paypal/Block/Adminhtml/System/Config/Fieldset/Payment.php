<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset renderer for PayPal solution
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment
    extends Mage_Backend_Block_System_Config_Form_Fieldset
{

    /**
     * @var Mage_Core_Model_Config_Data
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Config_Data $config
     * @param Mage_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Model_Config $config,
        Mage_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Add custom css class
     *
     * @param Varien_Data_Form_Element_Abstract $element
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
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isPaymentEnabled($element)
    {
        $groupConfig = $element->getGroup();
        $activityPath = isset($groupConfig['activity_path']) ? (array)$groupConfig['activity_path'] : '';

        if (empty($activityPath)) {
            return false;
        }

        $isPaymentEnabled = false;
        while ($activityPath && !$isPaymentEnabled) {
            $isPaymentEnabled = (bool)(string)$this->_config->getConfigDataValue(
                array_shift($activityPath)
            );
        }

        return (bool)$isPaymentEnabled;
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading" ><div class="heading"><strong>' . $element->getLegend();

        $groupConfig = $element->getGroup();
        if (!empty($groupConfig['more_url'])) {
            $html .= '<a class="link-more" href="' . $groupConfig['more_url'] . '" target="_blank">'
                . $this->__('Learn More') . '</a>';
        }
        if (!empty($groupConfig['demo_url'])) {
            $html .= '<a class="link-demo" href="' . $groupConfig['demo_url'] . '" target="_blank">'
                . $this->__('View Demo') . '</a>';
        }
        $html .= '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .= '</div>';

        $disabledAttributeString = $this->_isPaymentEnabled($element) ? '' : ' disabled="disabled"';
        $disabledClassString = $this->_isPaymentEnabled($element) ? '' : ' disabled';
        $htmlId = $element->getHtmlId();
        $html .= '<div class="button-container"><button type="button"' . $disabledAttributeString . ' class="button'
            . (empty($groupConfig['paypal_ec_separate']) ? '' : ' paypal-ec-separate')
            . $disabledClassString . '" id="' . $htmlId
            . '-button" onclick="paypalToggleSolution.call(this, \'' . $htmlId . "', '"
            . $this->getUrl('*/*/state') . '\'); return false;"><span class="state-closed">'
            . $this->__('Configure') . '</span><span class="state-opened">'
            . $this->__('Close') . '</span></button></div></div>';

        return $html;
    }

    /**
     * Return header comment part of html for payment solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        return false;
    }
}
