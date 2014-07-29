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

/**
 * Class Link
 * Frontend gift registry chopping cart block
 */
class Link extends Cart
{
    /**
     * Gift registry input selector
     *
     * @var string
     */
    protected $giftRegistry = '#giftregistry_entity';

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
     * Check that gift registry visible in shopping cart
     *
     * @param string $giftRegistry
     * @return bool
     */
    public function giftRegistryIsVisible($giftRegistry)
    {
        $optionSelector = sprintf($this->giftRegistryOption, $giftRegistry);
        return $this->_rootElement->find($optionSelector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
