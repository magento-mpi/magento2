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
class Magento_GiftRegistry_Block_Wishlist_Item_Column_Registry
    extends Magento_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_giftRegistryData->isEnabled() && count($this->getGiftRegistryList());
    }

    /**
     * Return list of current customer gift registries
     *
     * @return Magento_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getGiftRegistryList()
    {
        return $this->_giftRegistryData->getCurrentCustomerEntityOptions();
    }

    /**
     * Check if wishlist item can be added to gift registry
     *
     * @param Magento_Catalog_Model_Product $item
     * @return bool
     */
    public function checkProductType($item)
    {
        return $this->_giftRegistryData->canAddToGiftRegistry($item);
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
