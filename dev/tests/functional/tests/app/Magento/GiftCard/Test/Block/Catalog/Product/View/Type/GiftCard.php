<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
 *
 * @package Magento\GiftCard\Test\Block\Catalog\Product\View\Type
 */
class GiftCard extends Block
{

    /**
     * Gift Card Amount field
     *
     * @var string
     */
    protected $openAmount = '#giftcard-amount-input';

    /**
     * Gift Card Sender Name field
     *
     * @var string
     */
    protected $senderName = '#giftcard_sender_name';

    /**
     * Gift Card Sender Email field
     *
     * @var string
     */
    protected $senderEmail = '#giftcard_sender_email';

    /**
     * Gift Card Recipient Name field
     *
     * @var string
     */
    protected $recipientName = '#giftcard_recipient_name';

    /**
     * Gift Card Recipient Email field
     *
     * @var string
     */
    protected $recipientEmail = '#giftcard_recipient_email';

    /**
     * Verify that text field for Gift Card amount is present
     *
     * @return bool
     */
    public function isOpenAmount()
    {
        return $this->_rootElement->find($this->openAmount)->isVisible();
    }

    /**
     * Verifying that Gift Card fields on fronted correspond to Gift Card type:
     * Virtual and Combined - Sender Name, Sender Email, Recipient Name, Recipient Email
     *
     * @return bool
     */
    public function isGiftCardNotPhysical()
    {
        return $this->_rootElement->find($this->senderName)->isVisible()
            && $this->_rootElement->find($this->senderEmail)->isVisible()
            && $this->_rootElement->find($this->recipientName)->isVisible()
            && $this->_rootElement->find($this->recipientEmail)->isVisible();
    }
}
