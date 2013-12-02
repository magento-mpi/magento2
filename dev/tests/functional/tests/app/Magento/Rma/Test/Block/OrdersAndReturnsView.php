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

namespace Magento\Rma\Test\Block;

use Mtf\Fixture;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Orders and Returns view block
 *
 * @package Magento\Rma\Test\Block
 */
class OrdersAndReturnsView extends Block
{
    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '//*[@class="order toolbar"]//a[contains(text(), "%s")]';

    /**
     * Click link on this page.
     */
    public function clickLink($linkName)
    {
        $link = $this->_rootElement->find(sprintf($this->linkSelector, $linkName), Locator::SELECTOR_XPATH);
        if (!$link->isVisible()) {
            throw new \Exception(sprintf('"%s" link is not visible', $linkName));
        }
        $link->click();
    }
}
