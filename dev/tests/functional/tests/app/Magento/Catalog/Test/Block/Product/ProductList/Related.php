<?php
/**
 * {license_notice}
 *
 * @spi
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

class Related extends Block
{
    protected $relatedProduct = "//div[normalize-space(div//a)='%s']";

    /**
     * @param string $productName
     * @return bool
     */
    public function isRelatedProductVisible($productName)
    {
        return $this->getProductElement($productName)->isVisible();
    }

    /**
     * @param string $productName
     * @return bool
     */
    public function isRelatedProductSelectable($productName)
    {
        return $this->getProductElement($productName)->find("[name='related_products[]']")->isVisible();
    }

    /**
     * @param string $productName
     */
    public function openRelatedProduct($productName)
    {
        $this->getProductElement($productName)->find('.product.name>a')->click();
    }

    /**
     * @param string $productName
     */
    public function selectProductForAddToCart($productName)
    {
        $this->getProductElement($productName)
            ->find("[name='related_products[]']", Locator::SELECTOR_CSS, 'checkbox')
            ->setValue('Yes');
    }

    /**
     * @param string $productName
     * @return mixed|\Mtf\Client\Element
     */
    private function getProductElement($productName)
    {
        return $this->_rootElement->find(sprintf($this->relatedProduct, $productName), Locator::SELECTOR_XPATH);
    }
}