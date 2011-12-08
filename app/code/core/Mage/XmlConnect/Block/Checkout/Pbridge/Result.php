<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Pbridge result payment block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Pbridge_Result extends Mage_Core_Block_Template
{
    /**
     * Return url for redirect with params of Payment Bridge incoming data
     *
     * @return string
     */
    public function getPbridgeParamsAsUrl()
    {
        $pbParams = Mage::helper('Enterprise_Pbridge_Helper_Data')->getPbridgeParams();
        $params = array_merge(
            array('_nosid' => true, 'method' => 'pbridge_' . $pbParams['original_payment_method']),
            $pbParams
        );
        return Mage::getUrl('xmlconnect/pbridge/output', $params);
    }
}
