<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\FrontendPage;

/**
 * Class CatalogProductView
 */
class CatalogProductView extends FrontendPage
{
    const MCA = 'catalog/product/view';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'viewBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
        'customOptionsBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\View\CustomOptions',
            'locator' => '#product-options-wrapper',
            'strategy' => 'css selector',
        ],
        'relatedProductBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Related',
            'locator' => '.block.related',
            'strategy' => 'css selector',
        ],
        'upsellBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Upsell',
            'locator' => '.block.upsell',
            'strategy' => 'css selector',
        ],
        'crosssellBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Crosssell',
            'locator' => '.block.crosssell',
            'strategy' => 'css selector',
        ],
        'downloadableLinksBlock' => [
            'class' => 'Magento\Downloadable\Test\Block\Catalog\Product\View\Links',
            'locator' => '[data-container-for=downloadable-links]',
            'strategy' => 'css selector',
        ],
        'mapBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\Price',
            'locator' => '#map-popup-click-for-price',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages .messages',
            'strategy' => 'css selector',
        ],
        'reviewSummary' => [
            'class' => 'Magento\Review\Test\Block\Product\View\Summary',
            'locator' => '.product-reviews-summary',
            'strategy' => 'css selector',
        ],
        'customerReviewBlock' => [
            'class' => 'Magento\Review\Test\Block\Product\View',
            'locator' => '#customer-reviews',
            'strategy' => 'css selector',
        ],
        'reviewFormBlock' => [
            'class' => 'Magento\Review\Test\Block\Form',
            'locator' => '#review-form',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title h1.title .base',
            'strategy' => 'css selector',
        ],
        'eventBlock' => [
            'class' => 'Magento\CatalogEvent\Test\Block\Catalog\Event',
            'locator' => '.top-container',
            'strategy' => 'css selector',
        ],
        'bundleViewBlock' => [
            'class' => 'Magento\Bundle\Test\Block\Catalog\Product\View',
            'locator' => '.bundle-options-container',
            'strategy' => 'css selector',
        ],
        'downloadableViewBlock' => [
            'class' => 'Magento\Downloadable\Test\Block\Catalog\Product\View',
            'locator' => '.product-info-main',
            'strategy' => 'css selector',
        ],
        'giftCardBlock' => [
            'class' => 'Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard',
            'locator' => '.product-info-main',
            'strategy' => 'css selector',
        ],
        'groupedViewBlock' => [
            'class' => 'Magento\GroupedProduct\Test\Block\Catalog\Product\View',
            'locator' => '.product-info-main',
            'strategy' => 'css selector',
        ],
        'multipleWishlistViewBlock' => [
            'class' => 'Magento\MultipleWishlist\Test\Block\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Product\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\View\CustomOptions
     */
    public function getCustomOptionsBlock()
    {
        return $this->getBlockInstance('customOptionsBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Related
     */
    public function getRelatedProductBlock()
    {
        return $this->getBlockInstance('relatedProductBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Upsell
     */
    public function getUpsellBlock()
    {
        return $this->getBlockInstance('upsellBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Crosssell
     */
    public function getCrosssellBlock()
    {
        return $this->getBlockInstance('crosssellBlock');
    }

    /**
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View\Links
     */
    public function getDownloadableLinksBlock()
    {
        return $this->getBlockInstance('downloadableLinksBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\Price
     */
    public function getMapBlock()
    {
        return $this->getBlockInstance('mapBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Review\Test\Block\Product\View\Summary
     */
    public function getReviewSummary()
    {
        return $this->getBlockInstance('reviewSummary');
    }

    /**
     * @return \Magento\Review\Test\Block\Product\View
     */
    public function getCustomerReviewBlock()
    {
        return $this->getBlockInstance('customerReviewBlock');
    }

    /**
     * @return \Magento\Review\Test\Block\Form
     */
    public function getReviewFormBlock()
    {
        return $this->getBlockInstance('reviewFormBlock');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * @return \Magento\CatalogEvent\Test\Block\Catalog\Event
     */
    public function getEventBlock()
    {
        return $this->getBlockInstance('eventBlock');
    }

    /**
     * @return \Magento\Bundle\Test\Block\Catalog\Product\View
     */
    public function getBundleViewBlock()
    {
        return $this->getBlockInstance('bundleViewBlock');
    }

    /**
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\View
     */
    public function getDownloadableViewBlock()
    {
        return $this->getBlockInstance('downloadableViewBlock');
    }

    /**
     * @return \Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard
     */
    public function getGiftCardBlock()
    {
        return $this->getBlockInstance('giftCardBlock');
    }

    /**
     * @return \Magento\GroupedProduct\Test\Block\Catalog\Product\View
     */
    public function getGroupedViewBlock()
    {
        return $this->getBlockInstance('groupedViewBlock');
    }

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Product\View
     */
    public function getMultipleWishlistViewBlock()
    {
        return $this->getBlockInstance('multipleWishlistViewBlock');
    }
}
