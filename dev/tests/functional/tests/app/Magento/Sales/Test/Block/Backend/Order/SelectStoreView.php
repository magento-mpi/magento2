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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Block for selection store view for creating order
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class SelectStoreView extends Block
{
    public function selectStoreView(Order $fixture)
    {
        $selector = '//label[text()="' . $fixture->getStoreViewName() . '"]/preceding-sibling::*';
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH, 'checkbox')->setValue('Yes');
    }
}
