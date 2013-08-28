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
 * Renderer for PayPal banner in System Configuration
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint
    extends Magento_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'Magento_Paypal::system/config/fieldset/hint.phtml';

    /**
     * Adminhtml js
     *
     * @var Magento_Adminhtml_Helper_Js
     */
    protected $_adminhtmlJs = null;

    /**
     * @param Magento_Adminhtml_Helper_Js $adminhtmlJs
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Js $adminhtmlJs,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlJs = $adminhtmlJs;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Render fieldset html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['help_link'])) {
            $this->setHelpLink($elementOriginalData['help_link']);
        }
        $js = '
            paypalToggleSolution = function(id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if ($(this).hasClassName("open")) {
                    $$(".with-button button.button").each(function(anotherButton) {
                        if (anotherButton != this && $(anotherButton).hasClassName("open")) {
                            $(anotherButton).click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var pos = Element.cumulativeOffset($(this));
                    window.scrollTo(pos[0], pos[1] - 45);
                }
            }

            togglePaypalSolutionConfigureButton = function(button, enable) {
                var $button = $(button);
                $button.disabled = !enable;
                if ($button.hasClassName("disabled") && enable) {
                    $button.removeClassName("disabled");
                } else if (!$button.hasClassName("disabled") && !enable) {
                    $button.addClassName("disabled");
                }
            }

            // check store-view disabling Express Checkout
            document.observe("dom:loaded", function() {
                $$(".pp-method-express button.button").each(function(ecButton){
                    var ecEnabler = $$(".paypal-ec-enabler")[0];
                    if (typeof ecButton == "undefined" || typeof ecEnabler != "undefined") {
                        return;
                    }
                    var $ecButton = $(ecButton);
                    $$(".with-button button.button").each(function(configureButton) {
                        if (configureButton != ecButton && !configureButton.disabled
                            && !$(configureButton).hasClassName("paypal-ec-separate")
                        ) {
                            togglePaypalSolutionConfigureButton(ecButton, false);
                        }
                    });
                });
            });
        ';
        return $this->toHtml() . $this->_adminhtmlJs->getScript($js);
    }
}
