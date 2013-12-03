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
    protected $openAmount = '#giftcard-amount-input';

    protected $senderName = '#giftcard_sender_name';

    protected $senderEmail = '#giftcard_sender_email';

    protected $recipientName = '#giftcard_recipient_name';

    protected $recipientEmail = '#giftcard_recipient_email';



    public function isOpenAmount()
    {
        return $this->_rootElement->find($this->openAmount)->isVisible();
    }

    public function isGiftCardNotPhysical()
    {
        return $this->_rootElement->find($this->senderName)->isVisible()
            && $this->_rootElement->find($this->senderEmail)->isVisible()
            && $this->_rootElement->find($this->recipientName)->isVisible()
            && $this->_rootElement->find($this->recipientEmail)->isVisible();
    }
}
