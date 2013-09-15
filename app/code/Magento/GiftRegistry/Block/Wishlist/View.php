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
 * Wishlist view block
 */
namespace Magento\GiftRegistry\Block\Wishlist;

class View extends \Magento\Wishlist\Block\Customer\Wishlist
{
    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Magento_Catalog_Helper_Product_ConfigurationPool $helperPool
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_ConfigurationPool $helperPool,
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct(
            $helperPool, $wishlistData, $coreRegistry, $taxData, $catalogData, $coreData, $context, $data
        );
    }

    /**
     * Prepare block layout, override wishlist block with different template
     *
     * @return \Magento\GiftRegistry\Block\Wishlist\View
     */
    protected function _prepareLayout()
    {
        $outputEnabled = $this->_coreData->isModuleOutputEnabled($this->getModuleName());
        if ($outputEnabled) {
            if ($this->_layout->hasElement('my.account.wrapper')) {
                $oldBlock = $this->_layout->getBlock('customer.wishlist');
                if ($oldBlock) {
                    $this->_layout->unsetChild('my.account.wrapper', 'customer.wishlist');
                    $this->setOptionsRenderCfgs($oldBlock->getOptionsRenderCfgs());
                }
                $this->_layout->setChild('my.account.wrapper', $this->getNameInLayout(), 'customer.wishlist');
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Return add url
     *
     * @return bool
     */
    public function getAddUrl()
    {
        return $this->getUrl('giftregistry/index/wishlist');
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  $this->_giftRegistryData->isEnabled();
    }

    /**
     * Return list of current customer gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection
     */
    public function getEntityValues()
    {
        return $this->_giftRegistryData->getCurrentCustomerEntityOptions();
    }

    /**
     * Check if wishlist item can be added to gift registry
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return bool
     */
    public function checkProductType($item)
    {
        return $this->_giftRegistryData->canAddToGiftRegistry($item);
    }
}
