<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Renderer for Transaction Status URL field in Payone configuration
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Adminhtml_System_Config_Payone_PaymentBridgeUrl
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Return Payment Bridge IPN URL wrapped by span container
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $bridgeIpnUrl = Mage::helper('Enterprise_Pbridge_Helper_Data')->getBridgeBaseUrl() . 'ipn.php?action=PayoneIpn';
        return sprintf('<span style="white-space:nowrap" id="%s">%s</span>', $element->getHtmlId(), $bridgeIpnUrl);
    }
}
