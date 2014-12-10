<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\TestStep;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Mtf\TestStep\TestStepInterface;

/**
 * Apply Gift Card Account on backend order creation.
 */
class ApplyGiftCardAccountOnBackendStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Gift card account.
     *
     * @var GiftCardAccount
     */
    protected $giftCardAccount;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param GiftCardAccount $giftCardAccount
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, GiftCardAccount $giftCardAccount)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->giftCardAccount = $giftCardAccount;
    }

    /**
     * Apply gift card.
     *
     * @return void
     */
    public function run()
    {
        if ($this->giftCardAccount !== '-') {
            $this->orderCreateIndex->getGiftCardAccountBlock()->applyGiftCardAccount($this->giftCardAccount);
        }
    }
}
