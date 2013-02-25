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
 * Field renderer for PayPal merchant country selector
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_Country
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**#@+
     *
     * Request parameters names
     */
    const REQUEST_PARAM_COUNTRY = 'country';
    const REQUEST_PARAM_DEFAULT = 'default_country';
    /**#@-*/

    /**
     * Country of default scope
     *
     * @var string
     */
    protected $_defaultCountry;

    /**
     * Render country field considering request parameter
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $country = $this->getRequest()->getParam(self::REQUEST_PARAM_COUNTRY);
        if ($country) {
            $element->setValue($country);
        }

        if ($element->getCanUseDefaultValue()) {
            $defaultConfigNode = Mage::getConfig()->getNode(null, 'default');
            if ($defaultConfigNode) {
                $this->_defaultCountry = (string)$defaultConfigNode->descend('paypal/general/merchant_country');
            }
            if (!$this->_defaultCountry) {
                $this->_defaultCountry = Mage::helper('Mage_Core_Helper_Data')->getDefaultCountry();
            }
            if ($country) {
                $shouldInherit = $country == $this->_defaultCountry
                    && $this->getRequest()->getParam(self::REQUEST_PARAM_DEFAULT);
                $element->setInherit($shouldInherit);
            }
            if ($element->getInherit()) {
                $this->_defaultCountry = null;
            }
        }

        return parent::render($element);
    }

    /**
     * Get country selector html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $configDataModel = Mage::getSingleton('Mage_Backend_Model_Config');
        $urlParams = array(
            'section' => $configDataModel->getSection(),
            'website' => $configDataModel->getWebsite(),
            'store' => $configDataModel->getStore(),
            self::REQUEST_PARAM_COUNTRY => '__country__',
        );
        $urlString = $this->helper('Mage_Core_Helper_Data')
            ->jsQuoteEscape(Mage::getModel('Mage_Backend_Model_Url')->getUrl('*/*/*', $urlParams));
        $jsString = '
            $("' . $element->getHtmlId() . '").observe("change", function () {
                location.href = \'' . $urlString . '\'.replace("__country__", this.value);
            });
        ';

        if ($this->_defaultCountry) {
            $urlParams[self::REQUEST_PARAM_DEFAULT] = '__default__';
            $urlString = $this->helper('Mage_Core_Helper_Data')
                ->jsQuoteEscape(Mage::getModel('Mage_Backend_Model_Url')->getUrl('*/*/*', $urlParams));
            $jsParentCountry = $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($this->_defaultCountry);
            $jsString .= '
                $("' . $element->getHtmlId() . '_inherit").observe("click", function () {
                    if (this.checked) {
                        location.href = \'' . $urlString . '\'.replace("__country__", \'' . $jsParentCountry . '\')
                            .replace("__default__", "1");
                    }
                });
            ';
        }

        return parent::_getElementHtml($element) . $this->helper('Mage_Adminhtml_Helper_Js')
            ->getScript('document.observe("dom:loaded", function() {' . $jsString . '});');
    }
}
