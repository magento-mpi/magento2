<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Wishlist;

/**
 * Wishlist view block
 */
class View extends \Magento\Wishlist\Block\Customer\Wishlist
{
    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = array()
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct(
            $context,
            $httpContext,
            $productRepository,
            $helperPool,
            $formKey,
            $data
        );
    }

    /**
     * Prepare block layout, override wishlist block with different template
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $outputEnabled = $this->_moduleManager->isOutputEnabled($this->getModuleName());
        if ($outputEnabled) {
            if ($this->_layout->hasElement('content')) {
                $oldBlock = $this->_layout->getBlock('customer.wishlist');
                if ($oldBlock) {
                    $this->_layout->unsetChild('content', 'customer.wishlist');
                    $this->setOptionsRenderCfgs($oldBlock->getOptionsRenderCfgs());
                }
                $this->_layout->setChild('content', $this->getNameInLayout(), 'customer.wishlist');
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
        return $this->_giftRegistryData->isEnabled();
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
