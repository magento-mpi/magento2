<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Mtf\Block\Form;

/**
 * Class Share
 * Form for share gift registry
 */
class Share extends Form
{
    /**
     * Sender message selector
     *
     * @var string
     */
    protected $senderMessage = '[name="sender_message"]';

    /**
     * "Share Gift Registry" button selector
     *
     * @var string
     */
    protected $shareGiftRegistry = '.share';

    /**
     * Set sender message
     *
     * @param string $message
     * @return void
     */
    public function setSenderMessage($message)
    {
        $this->_rootElement->find($this->senderMessage)->setValue($message);
    }

    /**
     * Click "Share Gift Registry" button
     *
     * @return void
     */
    public function shareGiftRegistry()
    {
        $this->_rootElement->find($this->shareGiftRegistry)->click();
    }
}
