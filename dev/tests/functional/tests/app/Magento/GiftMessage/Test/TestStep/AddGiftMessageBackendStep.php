<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\TestStep;

use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Mtf\TestStep\TestStepInterface;

/**
 * Class AddGiftMessageBackendStep
 * Add gift message to order or item on backend
 */
class AddGiftMessageBackendStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Gift message fixture.
     *
     * @var GiftMessage
     */
    protected $giftMessage;

    /**
     * Array with products.
     *
     * @var array
     */
    protected $products;

    /**
     * Fixturefactory object.
     *
     * @var array
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param GiftMessage $giftMessage
     * @param array $products
     */
    public function __construct(
        OrderCreateIndex $orderCreateIndex,
        GiftMessage $giftMessage,
        array $products = []
    ) {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->giftMessage = $giftMessage;
        $this->products = $products;

    }

    /**
     * Add gift message to backend order.
     *
     * @return array
     */
    public function run()
    {
        if ($this->giftMessage->getAllowGiftMessagesForOrder()) {
            $this->orderCreateIndex->getGiftMessageForOrderBlock()->fill($this->giftMessage);
        }
        if ($this->giftMessage->getAllowGiftOptionsForItems()) {
            $this->orderCreateIndex->getCreateGiftMessageBlock()
                ->fillGiftMessageForItems($this->products, $this->giftMessage);
        }

        return ['giftMessage' => $this->giftMessage];
    }
}
