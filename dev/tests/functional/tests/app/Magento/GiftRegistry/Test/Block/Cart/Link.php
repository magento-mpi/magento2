<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Cart;

use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Block\Cart;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;

/**
 * Class Link
 * Frontend gift registry shopping cart block
 */
class Link extends Cart
{
    /**
     * Gift registry input selector
     *
     * @var string
     */
    protected $giftRegistry = '[name="entity"]';

    /**
     * Gift registry option selector
     *
     * @var string
     */
    protected $giftRegistryOption = '//select[@id="giftregistry_entity"]/option[contains(text(), "%s")]';

    /**
     * 'Add To Gift Registry' button selector
     *
     * @var string
     */
    protected $addToGiftRegistryButton = '.giftregistry .add';

    /**
     * Add to gift registry
     *
     * @param string $giftRegistry
     * @return void
     */
    public function addToGiftRegistry($giftRegistry)
    {
        $this->_rootElement->find($this->giftRegistry, Locator::SELECTOR_CSS, 'select')->setValue($giftRegistry);
        $this->_rootElement->find($this->addToGiftRegistryButton)->click();
    }

    /**
     * Check that gift registry available in shopping cart
     *
     * @param GiftRegistry $giftRegistry
     * @return bool
     */
    public function isGiftRegistryAvailable(GiftRegistry $giftRegistry)
    {
        $optionSelector = sprintf($this->giftRegistryOption, $giftRegistry->getTitle());
        return $this->_rootElement->find($optionSelector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
