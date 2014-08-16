<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Fixture\FixtureInterface;
use Mtf\Page\FrontendPage;

/**
 * Class CatalogProductView
 */
class CatalogProductView extends FrontendPage
{
    const MCA = 'catalog/product/view';

    /**
     * @var array
     */
    protected $_blocks = [
        'viewBlock' => [
            'name' => 'viewBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
        'relatedProductBlock' => [
            'name' => 'relatedProductBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Related',
            'locator' => '.block.related',
            'strategy' => 'css selector',
        ],
        'upsellBlock' => [
            'name' => 'upsellBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Upsell',
            'locator' => '.block.upsell',
            'strategy' => 'css selector',
        ],
        'crosssellBlock' => [
            'name' => 'crosssellBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Crosssell',
            'locator' => '.block.crosssell',
            'strategy' => 'css selector',
        ],
        'downloadableLinksBlock' => [
            'name' => 'downloadableLinksBlock',
            'class' => 'Magento\Downloadable\Test\Block\Catalog\Product\View\Links',
            'locator' => '[data-container-for=downloadable-links]',
            'strategy' => 'css selector',
        ],
        'mapBlock' => [
            'name' => 'mapBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\Price',
            'locator' => '#map-popup-content',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages .messages',
            'strategy' => 'css selector',
        ],
        'reviewSummary' => [
            'name' => 'reviewSummary',
            'class' => 'Magento\Review\Test\Block\Product\View\Summary',
            'locator' => '.product-reviews-summary',
            'strategy' => 'css selector',
        ],
        'customerReviewBlock' => [
            'name' => 'customerReviewBlock',
            'class' => 'Magento\Review\Test\Block\Product\View',
            'locator' => '#customer-reviews',
            'strategy' => 'css selector',
        ],
        'reviewFormBlock' => [
            'name' => 'reviewFormBlock',
            'class' => 'Magento\Review\Test\Block\Form',
            'locator' => '#review-form',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'name' => 'titleBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title h1.title .base',
            'strategy' => 'css selector',
        ],
    ];

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
     * @param FixtureInterface $fixture
     * @return void
     */
    public function init(FixtureInterface $fixture)
    {
        $this->_url = $_ENV['app_frontend_url'] . $fixture->getUrlKey() . '.html';
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
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
}
