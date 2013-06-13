<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Renderer for PayPal banner in System Configuration
 * @author      Magento Saas Team <saas@magentocommerce.com>
 * @method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint setHelpUrl(string $helpUrl)
 * @method string getHelpUrl()
 * @method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint setHtmlId(string $htmlId)
 * @method string getHtmlId()
 */
class Saas_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint
    extends Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint
{
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['help_url'])) {
            $this->setHelpUrl($elementOriginalData['help_url']);
            $this->setHtmlId($element->getId());
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
                    var ecBoardingEnabler = $$(".paypal-ec-boarding-enabler")[0];
                    if (typeof ecButton == "undefined" ||
                        (typeof ecEnabler != "undefined" && typeof ecBoardingEnabler != "undefined")
                    ) {
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
        return $this->toHtml() . $this->helper('Mage_Adminhtml_Helper_Js')->getScript($js);
    }
}
