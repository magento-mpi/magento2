<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge result payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Checkout_Payment_Result extends Magento_Core_Block_Template
{
    /**
     * Return JSON array of Payment Bridge incoming data
     *
     * @return string
     */
    public function getJsonHiddenPbridgeParams()
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode(
            Mage::helper('Enterprise_Pbridge_Helper_Data')->getPbridgeParams()
        );
    }
}
