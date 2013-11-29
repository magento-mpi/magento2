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
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Module\Manager $moduleManager,
        array $data = array()
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct(
            $context,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $wishlistData,
            $customerSession,
            $productFactory,
            $helperPool,
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
        $outputEnabled = $this->_moduleManager->isOutputEnabled($this->getModuleName());
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
