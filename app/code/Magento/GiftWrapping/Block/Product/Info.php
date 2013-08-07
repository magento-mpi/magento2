<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping info block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftWrapping_Block_Product_Info extends Magento_Core_Block_Template
{
    /**
     * Return product gift wrapping info
     *
     * @return false|Magento_Object
     */
    public function getGiftWrappingInfo()
    {
        $wrappingId = null;
        if ($this->getLayout()->getBlock('additional.product.info')) {
            $wrappingId = $this->getLayout()->getBlock('additional.product.info')
                ->getItem()
                ->getGwId();
        }

        if ($wrappingId) {
            return Mage::getModel('Magento_GiftWrapping_Model_Wrapping')->load($wrappingId);
        }
        return false;
    }
}
