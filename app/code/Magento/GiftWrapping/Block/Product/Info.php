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
namespace Magento\GiftWrapping\Block\Product;

class Info extends \Magento\Core\Block\Template
{
    /**
     * Return product gift wrapping info
     *
     * @return false|\Magento\Object
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
            return \Mage::getModel('\Magento\GiftWrapping\Model\Wrapping')->load($wrappingId);
        }
        return false;
    }
}
