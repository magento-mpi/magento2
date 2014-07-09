<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Block\Catalog\Product\View\Type;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class GiftCard
 * Catalog gift card product info block
 */
class GiftCard extends Block
{
    /**
     * Selector for price
     *
     * @var string
     */
    protected $price = '.price-box .regular-price > .price';

    /**
     * Gift Card Amount field
     *
     * @var string
     */
    protected $amountInput = '[name="custom_giftcard_amount"]';

    /**
     * Selector for giftcard amount
     *
     * @var string
     */
    protected $amountSelect = '[name="giftcard_amount"]';

    /**
     * Selector for "Custom amount" option of select
     *
     * @var string
     */
    protected $amountCustomOption = 'Other Amount...';

    /**
     * Gift Card Sender Name field
     *
     * @var string
     */
    protected $senderName = '[name="giftcard_sender_name"]';

    /**
     * Gift Card Sender Email field
     *
     * @var string
     */
    protected $senderEmail = '[name="giftcard_sender_email"]';

    /**
     * Gift Card Recipient Name field
     *
     * @var string
     */
    protected $recipientName = '[name="giftcard_recipient_name"]';

    /**
     * Gift Card Recipient Email field
     *
     * @var string
     */
    protected $recipientEmail = '[name="giftcard_recipient_email"]';

    /**
     * Selector for Gift card message
     *
     * @var string
     */
    protected $message = '[name="giftcard_message"]';

    /**
     * Get amount values
     *
     * @return array
     */
    public function getAmountValues()
    {
        $values = [];
        $giftcardAmount = $this->_rootElement->find($this->amountSelect, Locator::SELECTOR_CSS);
        $priceElement = $this->_rootElement->find($this->price);
        $optionSelector = './/option[%d]';

        if (!$giftcardAmount->isVisible() && !$priceElement->isVisible()) {
            return $values;
        }

        // Return price if product has one amount
        if (!$giftcardAmount->isVisible()) {
            $price = $priceElement->getText();
            $values[] = floatval(preg_replace('/[^0-9.]/', '', $price));
            return $values;
        }

        /* Skip option #1("Choose amount...") */
        $count = 2;
        $option = $giftcardAmount->find(sprintf($optionSelector, $count), Locator::SELECTOR_XPATH);
        while ($option->isVisible()) {
            $values[] = $option->getValue();
            ++$count;
            $option = $giftcardAmount->find(sprintf($optionSelector, $count), Locator::SELECTOR_XPATH);
        }
        return $values;
    }

    /**
     * Select "Custom amount" option
     *
     * @return void
     */
    public function selectCustomAmount()
    {
        $amountSelect = $this->_rootElement->find($this->amountSelect, Element\Locator::SELECTOR_CSS, 'select');
        $amountSelect->setValue($this->amountCustomOption);
    }

    /**
     * Verify that text field for Gift Card amount is present
     *
     * @return bool
     */
    public function isAmountInputVisible()
    {
        return $this->_rootElement->find($this->amountInput)->isVisible();
    }

    public function isAmountSelectVisible()
    {
        return $this->_rootElement->find($this->amountSelect)->isVisible();
    }

    public function isSenderNameVisible()
    {
        return $this->_rootElement->find($this->senderName)->isVisible();
    }

    public function isSenderEmailVisible()
    {
        return $this->_rootElement->find($this->senderEmail)->isVisible();
    }

    public function isRecipientNameVisible()
    {
        return $this->_rootElement->find($this->recipientName)->isVisible();
    }

    public function isRecipientEmailVisible()
    {
        return $this->_rootElement->find($this->recipientEmail)->isVisible();
    }

    public function isMessageVisible()
    {
        return $this->_rootElement->find($this->message)->isVisible();
    }

    /**
     * Verifying that Gift Card fields on fronted correspond to Gift Card type:
     * Virtual and Combined - Sender Name, Sender Email, Recipient Name, Recipient Email
     *
     * @return bool
     */
    public function isGiftCardNotPhysical()
    {
        return $this->isSenderNameVisible()
        && $this->isSenderEmailVisible()
        && $this->isRecipientNameVisible()
        && $this->isRecipientEmailVisible();
    }
}
