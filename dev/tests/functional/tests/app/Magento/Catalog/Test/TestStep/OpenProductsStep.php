<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Mtf\Client\Browser;
use Mtf\TestStep\TestStepInterface;

/**
 * Open products.
 */
class OpenProductsStep implements TestStepInterface
{
    /**
     * Products fixtures.
     *
     * @var array
     */
    protected $products = [];

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Preparing step properties.
     *
     * @constructor
     * @param array $products
     * @param Browser $browser
     */
    public function __construct(array $products, Browser $browser)
    {
        $this->products = $products;
        $this->browser = $browser;
    }

    /**
     * Open products.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        }
    }
}
