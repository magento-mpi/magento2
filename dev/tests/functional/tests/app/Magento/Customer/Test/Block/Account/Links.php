<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Account;


use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class Links extends Block
{
    /**
     * @var string $giftCard
     */
    private $giftCard = '//*[contains(@class,"item")]/a[contains(.,"Gift Card")]';

    /**
     * Select gift card in menu
     */
    public function selectGiftCard()
    {
        $this->_rootElement->find($this->giftCard, Locator::SELECTOR_XPATH)->click();
    }
} 