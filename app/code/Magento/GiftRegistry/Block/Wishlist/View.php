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
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct(
            $helperPool,
            $wishlistData,
            $coreRegistry,
            $taxData,
            $catalogData,
            $coreData,
            $context,
            $storeManager,
            $customerSession,
            $productFactory,
            $data
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
