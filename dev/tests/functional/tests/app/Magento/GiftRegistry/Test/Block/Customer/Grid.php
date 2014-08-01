<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;
use Mtf\Client\Element\Locator;
use Magento\GiftRegistry\TEst\Fixture\GiftRegistry;

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
    protected $eventSelector = '.event[title*="%s"]';

    /**
     * Gift registry event action selector in grid
     *
     * @var string
     */
    protected $eventActionSelector = '//tr[td[contains(.,"%s")]]//a[contains(.,"%s")]';

    /**
     * Is visible gift registry in grid
     *
     * @param GiftRegistry $giftRegistry
     * @return bool
     */
    public function isGiftRegistryInGrid(GiftRegistry $giftRegistry)
    {
        return $this->_rootElement->find(sprintf($this->eventSelector, $giftRegistry->getTitle()))->isVisible();
    }

    /**
     * Click to action in appropriate event
     *
     * @param string $event
     * @param string $action
     * @param bool $acceptAlert [optional]
     * @return void
     */
    public function eventAction($event, $action, $acceptAlert = false)
    {
        $selector = sprintf($this->eventActionSelector, $event, $action);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        if ($acceptAlert) {
            $this->_rootElement->acceptAlert();
        }
    }
}
