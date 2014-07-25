<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Frontend gift registry grid
 */
class Grid extends AbstractGrid
{
    /**
     * Gift registry event selector in grid
     *
     * @var string
     */
    protected $eventSelector = '//td[contains(@class,"col event") and contains(text(),"%s")]';

    /**
     * Gift registry event action selector in grid
     *
     * @var string
     */
    protected $eventActionSelector = '//tr[td[contains(.,"%s")]]//a[contains(.,"%s")]';

    /**
     * Is visible gift registry in grid
     *
     * @param string $event
     * @return bool
     */
    public function isInGrid($event)
    {
        return $this->_rootElement->find(sprintf($this->eventSelector, $event), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Click to action in appropriate event
     *
     * @param string $event
     * @param string $action
     * @return void
     */
    public function eventAction($event, $action)
    {
        $selector = sprintf($this->eventActionSelector, $event, $action);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
    }
}
