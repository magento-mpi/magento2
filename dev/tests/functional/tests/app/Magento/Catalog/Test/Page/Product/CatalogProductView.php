<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductView
 * Frontend product view page
 *
 * @package Magento\Catalog\Test\Page\Product
 */
class CatalogProductView extends Page
{
    /**
     * URL for catalog product grid
     */
    const MCA = 'catalog/product/view';

    /**
     * Review summary selector
     *
     * @var string
     */
    protected $reviewSummarySelector = '.product.reviews.summary';

    /**
     * Reviews selector
     *
     * @var string
     */
    protected $reviewsSelector = 'product_reviews';

    /**
     * Messages selector
     *
     * @var string
     */
    protected $messagesSelector = '.page.messages .messages';

    /**
     * Product View block
     *
     * @var string
     */
    protected $viewBlock = '.column.main';

    /**
     * Product options block
     *
     * @var string
     */
    protected $optionsBlock = '.product.options.wrapper';

    /**
     * Related product selector
     *
     * @var string
     */
    protected $relatedProductSelector = '.block.related';

    /**
     * Upsell selector
     *
     * @var string
     */
    protected $upsellSelector = '.block.upsell';

    /**
     * Gift Card Block selector
     *
     * @var string
     */
    protected $giftCardBlockSelector = '[data-container-for=giftcard_info]';

    /**
     * Cross-sell selector
     *
     * @var string
     */
    protected $crosssellSelector = '.block.crosssell';

    /**
     * @var string
     */
    protected $downloadableLinksSelector = '[data-container-for=downloadable-links]';

    /**
     * MAP popup
     *
     * @var string
     */
    protected $mapBlock = '#map-popup-content';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Page initialization
     *
     * @param DataFixture|\Magento\Catalog\Test\Fixture\Product $fixture
     */
    public function init(DataFixture $fixture)
    {
        $this->_url = $_ENV['app_frontend_url'] . $fixture->getProductUrl() . '.html';
    }

    /**
     * Get product view block
     *
     * @return \Magento\Catalog\Test\Block\Product\View
     */
    public function getViewBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductView(
            $this->_browser->find($this->viewBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get product options block
     *
     * @return \Magento\Catalog\Test\Block\Product\View\Options
     */
    public function getOptionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductViewOptions(
            $this->_browser->find($this->optionsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get product options block
     *
     * @return \Magento\Catalog\Test\Block\Product\View\CustomOptions
     */
    public function getCustomOptionBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductViewCustomOptions(
            $this->_browser->find('#product-options-wrapper')
        );
    }

    /**
     * Get reviews block
     *
     * @return \Magento\Review\Test\Block\Product\View
     */
    public function getReviewsBlock()
    {
        return Factory::getBlockFactory()->getMagentoReviewProductView(
            $this->_browser->find($this->reviewsSelector, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get review summary block
     *
     * @return \Magento\Review\Test\Block\Product\View\Summary
     */
    public function getReviewSummaryBlock()
    {
        return Factory::getBlockFactory()->getMagentoReviewProductViewSummary(
            $this->_browser->find($this->reviewSummarySelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get upsell block
     *
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Upsell
     */
    public function getUpsellProductBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductProductListUpsell(
            $this->_browser->find($this->upsellSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get related product block
     *
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Related
     */
    public function getRelatedProductBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductProductListRelated(
            $this->_browser->find($this->relatedProductSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get gift card options block
     *
     * @return \Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard
     */
    public function getGiftCardBlock()
    {
        return Factory::getBlockFactory()->getMagentoGiftCardCatalogProductViewTypeGiftCard(
            $this->_browser->find($this->giftCardBlockSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\Links
     */
    public function getDownloadableLinksBlock()
    {
        return Factory::getBlockFactory()->getMagentoDownloadableCatalogProductLinks(
            $this->_browser->find($this->downloadableLinksSelector)
        );
    }

    /**
     * Get product price block
     *
     * @return \Magento\Catalog\Test\Block\Product\Price
     */
    public function getMapBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductPrice(
            $this->_browser->find($this->mapBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Retrieve cross-sell block
     *
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Crosssell
     */
    public function getCrosssellBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductProductListCrosssell(
            $this->_browser->find($this->crosssellSelector, Locator::SELECTOR_CSS)
        );
    }
}
