<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Search;

use Mtf\Block\Block;
use Magento\GiftRegistry\TEst\Fixture\GiftRegistry;
use Mtf\Client\Element\Locator;

/**
 * Class Results
 * Frontend gift registry search results
 */
class Results extends Block
{
    /**
     * Gift registry event selector in grid
     *
     * @var string
     */
    protected $eventSelector = '//tr[td[contains(@class,"event") and contains(.,"%s")]]';

    /**
     * Gift registry event view action selector
     *
     * @var string
     */
    protected $viewAction = '//tr[td[contains(@class,"event") and contains(.,"%s")]]//a[contains(.,"View")]';

    /**
     * Is visible gift registry in grid
     *
     * @param GiftRegistry $giftRegistry
     * @return bool
     */
    public function isGiftRegistryInGrid(GiftRegistry $giftRegistry)
    {
        return $this->_rootElement->find(
            sprintf($this->eventSelector, $giftRegistry->getTitle()),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }

    /**
     * Click 'View' to appropriate gift registry
     *
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function giftRegistryView(GiftRegistry $giftRegistry)
    {
        $this->_rootElement->find(
            sprintf($this->viewAction, $giftRegistry->getTitle()),
            Locator::SELECTOR_XPATH
        )->click();
    }
}
