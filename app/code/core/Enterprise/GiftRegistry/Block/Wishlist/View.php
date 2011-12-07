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
class Enterprise_GiftRegistry_Block_Wishlist_View extends Mage_Wishlist_Block_Customer_Wishlist
{
    /**
     * Prepare block layout, override wishlist block with different template
     *
     * @return Enterprise_GiftRegistry_Block_Wishlist_View
     */
    protected function _prepareLayout()
    {
        $outputEnabled = Mage::helper('Mage_Core_Helper_Data')->isModuleOutputEnabled($this->getModuleName());
        if ($outputEnabled) {
            $wrapper = $this->getLayout()->getBlock('my.account.wrapper');
            if ($wrapper) {
                $oldBlock = $this->getLayout()->getBlock('customer.wishlist');
                if ($oldBlock) {
                    $wrapper->unsetChild('customer.wishlist');
                    $this->setOptionsRenderCfgs($oldBlock->getOptionsRenderCfgs());
                }
                $wrapper->append($this, 'customer.wishlist');
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
        return  Mage::helper('Enterprise_GiftRegistry_Helper_Data')->isEnabled();
    }

    /**
     * Return list of current customer gift registries
     *
     * @return Enterprise_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getEntityValues()
    {
        return Mage::helper('Enterprise_GiftRegistry_Helper_Data')->getCurrentCustomerEntityOptions();
    }

    /**
     * Check if wishlist item can be added to gift registry
     *
     * @param Mage_Catalog_Model_Product $item
     * @return bool
     */
    public function checkProductType($item)
    {
        return Mage::helper('Enterprise_GiftRegistry_Helper_Data')->canAddToGiftRegistry($item);
    }
}
