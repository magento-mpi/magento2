<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Frontend gift registry items
 */
class Items extends Block
{
    /**
     * Product name selector in registry items grid
     *
     * @var string
     */
    protected $productName = '//td[contains(@class,"product") and a[contains(text(), "%s")]]';

    /**
     * Is visible product in gift registry items grid
     *
     * @param string $name
     * @return bool
     */
    public function isProductInGrid($name)
    {
        return $this->_rootElement->find(sprintf($this->productName, $name), Locator::SELECTOR_XPATH)->isVisible();
    }
}
