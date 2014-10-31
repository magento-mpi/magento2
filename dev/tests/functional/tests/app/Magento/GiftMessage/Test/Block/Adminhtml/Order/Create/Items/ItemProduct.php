<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Items;

use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class ItemProduct
 * Item product block on backend create order page.
 */
class ItemProduct extends \Magento\Sales\Test\Block\Adminhtml\Order\Create\Items\ItemProduct
{
    /**
     * Selector for GiftOptions link.
     *
     * @var string
     */
    protected $giftOptionsLink = '[id^="gift_options_link"]';

    /**
     * Selector for order item GiftMessage form.
     *
     * @var string
     */
    protected $giftMessageForm = '//*[@role="dialog"][*[@id="gift_options_configure"]]';

    /**
     * Magento varienLoader.js loader.
     *
     * @var string
     */
    protected $loadingMask = '//*[@id="loading-mask"]/*[@id="loading_mask_loader"]';

    /**
     * Fill GiftMessage form.
     *
     * @param GiftMessage $giftMessage
     * @return void
     */
    public function fillGiftMessageForm(GiftMessage $giftMessage)
    {
        $giftOptionsLink = $this->_rootElement->find($this->giftOptionsLink);
        $giftOptionsLink->click();
        /** @var \Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Form $giftMessageForm */
        $giftMessageForm = $this->blockFactory->create(
            'Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Form',
            ['element' => $this->browser->find($this->giftMessageForm, Locator::SELECTOR_XPATH)]
        );
        $giftMessageForm->fill($giftMessage);
        $loadingMask = $this->browser->find($this->loadingMask, Locator::SELECTOR_XPATH);
        $this->browser->waitUntil(
            function () use ($loadingMask) {
                return !$loadingMask->isVisible() ? true : null;
            }
        );
    }
}
