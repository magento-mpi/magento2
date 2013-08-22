<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge result payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Checkout_Payment_Result extends Magento_Core_Block_Template
{
    /**
     * Return JSON array of Payment Bridge incoming data
     *
     * @return string
     */
    public function getJsonHiddenPbridgeParams()
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode(
            Mage::helper('Magento_Pbridge_Helper_Data')->getPbridgeParams()
        );
    }
}
