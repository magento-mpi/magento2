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

namespace Magento\Catalog\Test\Block\Product\ProductList;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

class Upsell extends Block
{
    protected $upsellProduct = "//div[normalize-space(div//a)='%s']";

    /**
     * @param string $productName
     * @return bool
     */
    public function isUpsellProductVisible($productName)
    {
        return $this->getProductElement($productName)->isVisible();
    }

    /**
     * @param string $productName
     */
    public function openUpsellProduct($productName)
    {
        $this->getProductElement($productName)->find('.product.name>a')->click();
    }

    /**
     * @param string $productName
     * @return mixed|\Mtf\Client\Element
     */
    private function getProductElement($productName)
    {
        return $this->_rootElement->find(sprintf($this->upsellProduct, $productName), Locator::SELECTOR_XPATH);
    }
}