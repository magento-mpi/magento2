<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\ObjectManager;
use Mtf\Client\Browser;
use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractAssertForm;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductCustomOptionsOnProductPage
 */
class AssertProductCustomOptionsOnProductPage extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped field for custom options
     *
     * @var array
     */
    protected $skippedFieldOptions = [
        'Field' => [
            'price_type',
            'sku',
        ],
        'Area' => [
            'price_type',
            'sku',
        ],
        'Drop-down' => [
            'price_type',
            'sku',
        ],
        'File' => [
            'price_type',
            'sku',
        ],
        'Radio Buttons' => [
            'price_type',
            'sku',
        ],
        'Checkbox' => [
            'price_type',
            'sku',
        ],
        'Multiple Select' => [
            'price_type',
            'sku',
        ],
        'Date' => [
            'price_type',
            'sku',
        ],
        'Date & Time' => [
            'price_type',
            'sku',
        ],
        'Time' => [
            'price_type',
            'sku',
        ]
    ];

    /**
     * Flag for verify price data
     *
     * @var bool
     */
    protected $isPrice = true;

    /**
     * Class name of catalog product view page
     *
     * @var string
     */
    protected $catalogProductViewClass = 'Magento\Catalog\Test\Page\Product\CatalogProductView';

    /**
     * Catalog product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Assertion that commodity options are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product, Browser $browser)
    {
        $this->openProductPage($product, $browser);
        // Prepare data
        $formCustomOptions = $this->catalogProductView->getViewBlock()->getCustomOptionsBlock()->getOptions($product);
        $actualPrice = $this->isPrice ? $this->getProductPrice() : null;
        $fixtureCustomOptions = $this->prepareOptions($product, $actualPrice);

        $error = $this->verifyData($fixtureCustomOptions, $formCustomOptions);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Get price from product page
     *
     * @return string
     */
    protected function getProductPrice()
    {
        $prices = $this->catalogProductView->getViewBlock()->getPriceBlock()->getPrice();
        $actualPrice = isset($prices['price_special_price'])
            ? $prices['price_special_price']
            : $prices['price_regular_price'];

        return $actualPrice;
    }

    /**
     * Open product view page
     *
     * @param FixtureInterface $product
     * @param Browser $browser
     * @return void
     */
    protected function openProductPage(
        FixtureInterface $product,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
    }

    /**
     * Preparation options before comparing
     *
     * @param FixtureInterface $product
     * @param int|null $actualPrice
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function prepareOptions(FixtureInterface $product, $actualPrice = null)
    {
        $result = [];
        $customOptions = $product->hasData('custom_options')
            ? $product->getDataFieldConfig('custom_options')['source']->getCustomOptions()
            : null;
        $actualPrice = $actualPrice ? $actualPrice : $product->getPrice();
        foreach ($customOptions as $customOption) {
            $skippedField = isset($this->skippedFieldOptions[$customOption['type']])
                ? $this->skippedFieldOptions[$customOption['type']]
                : [];
            foreach ($customOption['options'] as &$option) {
                // recalculate percent price
                if ('Percent' == $option['price_type']) {
                    $option['price'] = ($actualPrice * $option['price']) / 100;
                    $option['price'] = round($option['price'], 2);
                }

                $option = array_diff_key($option, array_flip($skippedField));
            }

            $result[$customOption['title']] = $customOption;
        }

        return $result;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Value of custom option on the page is correct.';
    }
}
