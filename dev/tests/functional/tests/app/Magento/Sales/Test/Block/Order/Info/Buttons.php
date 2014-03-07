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

namespace Magento\Sales\Test\Block\Order\Info;

use Mtf\Fixture\FixtureInterface;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Order view block
 *
 * @package Magento\Sales\Test\Block\Order\Info
 */
class Buttons extends Block
{
    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '//div[@class="order toolbar"]//span[contains(text(), "%s")]';

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
