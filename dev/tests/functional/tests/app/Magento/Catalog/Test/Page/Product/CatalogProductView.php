<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\FrontendPage;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CatalogProductView
 */
class CatalogProductView extends FrontendPage
{
    const MCA = 'catalog/product/view';

    protected $_blocks = [
        'viewBlock' => [
            'name' => 'viewBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
        'customOptionsBlock' => [
            'name' => 'customOptionsBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\View\CustomOptions',
            'locator' => '#product-options-wrapper',
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
            'class' => 'Magento\Downloadable\Test\Block\Catalog\Product\Links',
            'locator' => '[data-container-for=downloadable-links]',
            'strategy' => 'css selector',
        ]
    ];

    /**
     * Custom constructor
     *
     * @return void
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
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\Links
     */
    public function getDownloadableLinksBlock()
    {
        return $this->getBlockInstance('downloadableLinksBlock');
    }
}
