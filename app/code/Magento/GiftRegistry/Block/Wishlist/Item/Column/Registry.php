<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item "Add to gift registry" column block
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Block\Wishlist\Item\Column;

class Registry
    extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function isEnabled()
    {
        return \Mage::helper('Magento\GiftRegistry\Helper\Data')->isEnabled() && count($this->getGiftRegistryList());
    }

    /**
     * Return list of current customer gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection
     */
    public function getGiftRegistryList()
    {
        return \Mage::helper('Magento\GiftRegistry\Helper\Data')->getCurrentCustomerEntityOptions();
    }

    /**
     * Check if wishlist item can be added to gift registry
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return bool
     */
    public function checkProductType($item)
    {
        return \Mage::helper('Magento\GiftRegistry\Helper\Data')->canAddToGiftRegistry($item);
    }

    /**
     * Retrieve column related javascript code
     *
     * @return string
     */
    public function getJs()
    {
        $addUrl = $this->getUrl('giftregistry/index/wishlist');
        return "
        function addProductToGiftregistry(itemId, giftregistryId) {
            var form = new Element('form', {method: 'post', action: '" . $addUrl . "'});
            form.insert(new Element('input', {type: 'hidden', name: 'item', value: itemId}));
            form.insert(new Element('input', {type: 'hidden', name: 'entity', value: giftregistryId}));
            $(document.body).insert(form);
            $(form).submit();
            return false;
        }
        ";
    }
}
