<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist view block
 */
class Enterprise_GiftRegistry_Block_Wishlist_View extends Magento_Wishlist_Block_Customer_Wishlist
{
    /**
     * Gift registry data
     *
     * @var Enterprise_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Enterprise_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($context, $data);
    }

    /**
     * Prepare block layout, override wishlist block with different template
     *
     * @return Enterprise_GiftRegistry_Block_Wishlist_View
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
     * @return Enterprise_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getEntityValues()
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
}
