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


namespace Magento\Catalog\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use \Magento\Catalog\Test\Fixture\Product;

class Upsell extends Block {


    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '//*[@class="block upsell"]//*/a[contains(text(), "%s")]';

    /**
     * Verify upsell item
     *
     * @param Product $upsell
     * @return bool
     */
    public function verifyProductUpsell(Product $upsell)
    {
        $match = $this->_rootElement->find(
            '//ol[@class="products list items upsell"]//*/div/strong/a[@title="' . $upsell->getProductName() . '"]',
            Locator::SELECTOR_XPATH);

        if (!$match->isVisible()) {
            return false;
        };
        return true;
    }

    /**
     * Click on upsell product link
     *
     * @param Product $product
     * @return \Mtf\Client\Element
     * @throws \Exception
     */
    public function clickLink($product)
    {
        $link = $this->_rootElement->find(sprintf($this->linkSelector, $product->getProductName()), Locator::SELECTOR_XPATH);
        if (!$link->isVisible()) {
            throw new \Exception(sprintf('"%s" link is not visible', $product->getProductName()));
        }
        $link->click();
    }
}