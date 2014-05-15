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
 * Frontend product view page
 */
class CatalogProductView extends FrontendPage
{
    /**
     * URL for catalog product grid
     */
    const MCA = 'catalog/product/view';

    protected $_blocks = [
        'viewBlock' => [
            'name' => 'viewBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
        'optionsBlock' => [
            'name' => 'optionsBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\View\Options',
            'locator' => '#product-options-wrapper',
            'strategy' => 'css selector',
        ],
        'relatedProductSelector' => [
            'name' => 'relatedProductSelector',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Related',
            'locator' => '.block.related',
            'strategy' => 'css selector',
        ],
        'upsellSelector' => [
            'name' => 'upsellSelector',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Upsell',
            'locator' => '.block.upsell',
            'strategy' => 'css selector',
        ],
        'giftCardBlockSelector' => [
            'name' => 'giftCardBlockSelector',
            'class' => 'Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard',
            'locator' => '[data-container-for=giftcard_info]',
            'strategy' => 'css selector',
        ],
        'crosssellSelector' => [
            'name' => 'crosssellSelector',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Crosssell',
            'locator' => '.block.crosssell',
            'strategy' => 'css selector',
        ],
        'downloadableLinksSelector' => [
            'name' => 'downloadableLinksSelector',
            'class' => 'Magento\Downloadable\Test\Block\Catalog\Product\Links',
            'locator' => '[data-container-for=downloadable-links]',
            'strategy' => 'css selector',
        ],
        'customOptions' => [
            'name' => 'customOptions',
            'class' => 'Magento\Catalog\Test\Block\Product\View\CustomOptions',
            'locator' => '#product-options-wrapper',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * Review form
     *
     * @var string
     */
    protected $reviewFormBlock = '#review-form';

    /**
     * Customer reviews block
     *
     * @var string
     */
    protected $customerReviewBlock = '#customer-reviews';

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
    protected $optionsBlock = '#product-options-wrapper';

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
     * Gift Card Amount Block selector
     *
     * @var string
     */
    protected $giftCardBlockAmountSelector = '.fieldset.giftcard.amount';

    /**
     * Cross-sell selector
     *
     * @var string
     */
    protected $crosssellSelector = '.block.crosssell';

    /**
     * MAP popup
     *
     * @var string
     */
    protected $mapBlock = '#map-popup';

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
     * Get product view block
     *
     * @return \Magento\Catalog\Test\Block\Product\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\View\Options
     */
    public function getOptionsBlock()
    {
        return $this->getBlockInstance('optionsBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Related
     */
    public function getRelatedProductSelector()
    {
        return $this->getBlockInstance('relatedProductSelector');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Upsell
     */
    public function getUpsellSelector()
    {
        return $this->getBlockInstance('upsellSelector');
    }

    /**
     * @return \Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard
     */
    public function getGiftCardBlockSelector()
    {
        return $this->getBlockInstance('giftCardBlockSelector');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Crosssell
     */
    public function getCrosssellSelector()
    {
        return $this->getBlockInstance('crosssellSelector');
    }

    /**
     * @return \Magento\Downloadable\Test\Block\Catalog\Product\Links
     */
    public function getDownloadableLinksSelector()
    {
        return $this->getBlockInstance('downloadableLinksSelector');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\View\CustomOptions
     */
    public function getCustomOptions()
    {
        return $this->getBlockInstance('customOptions');
    }
}
