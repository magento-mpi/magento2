<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order\Info;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Order view block.
 */
class Buttons extends Block
{
    /**
     * Selector for "Print Order" link.
     *
     * @var string
     */
    protected $printOrderSelector = '.action.print';

    /**
     * Link selector.
     *
     * @var string
     */
    protected $linkSelector = '//div[contains(@class, "order-actions-toolbar")]//span[contains(text(), "%s")]';

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

    /**
     * Click on "Print Order" link.
     *
     * @return void
     */
    public function clickPrintOrder()
    {
        $this->_rootElement->find($this->printOrderSelector)->click();
    }
}
