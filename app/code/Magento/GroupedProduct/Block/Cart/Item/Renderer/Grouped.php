<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Cart\Item\Renderer;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail as ThumbnailSource;

/**
 * Shopping cart item render block
 */
class Grouped extends \Magento\Checkout\Block\Cart\Item\Renderer implements \Magento\View\Block\IdentityInterface
{
    /**
     * Path in config to the setting which defines if parent or child product should be used to generate a thumbnail.
     */
    const CONFIG_THUMBNAIL_SOURCE = 'checkout/cart/grouped_product_image';

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Core\Helper\Url $urlHelper
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Core\Helper\Url $urlHelper,
        \Magento\Message\ManagerInterface $messageManager,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageHelper,
            $urlHelper,
            $messageManager,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Get item grouped product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getGroupedProduct()
    {
        $option = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            return $option->getProduct();
        }
        return $this->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductForThumbnail()
    {
        /**
         * Show grouped product thumbnail if it must be always shown according to the related setting in system config
         * or if child product thumbnail is not available
         */
        if ($this->_storeConfig->getValue(self::CONFIG_THUMBNAIL_SOURCE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == ThumbnailSource::OPTION_USE_PARENT_IMAGE
            || !($this->getProduct()->getThumbnail() && $this->getProduct()->getThumbnail() != 'no_selection')
        ) {
            $product = $this->getGroupedProduct();
        } else {
            $product = $this->getProduct();
        }
        return $product;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = parent::getIdentities();
        if ($this->getItem()) {
            $identities = array_merge($identities, $this->getGroupedProduct()->getIdentities());
        }
        return $identities;
    }
}
