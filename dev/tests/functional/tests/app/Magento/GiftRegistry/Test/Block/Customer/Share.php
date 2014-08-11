<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Mtf\Block\Form;
use Magento\Customer\Test\Fixture\CustomerInjectable;

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
     * Add recipient button selector
     *
     * @var string
     */
    protected $addRecipient = '#add-recipient-button';

    /**
     * Recipient first name selector
     *
     * @var string
     */
    protected $recipientFirstName = '[name="recipients[%d][name]"]';

    /**
     * Recipient email selector
     *
     * @var string
     */
    protected $recipientEmail = '[name="recipients[%d][email]"]';

    /**
     * Click "Share Gift Registry" button
     *
     * @return void
     */
    public function shareGiftRegistry()
    {
        $this->_rootElement->find($this->shareGiftRegistry)->click();
    }

    /**
     * Fill share gift registry form
     *
     * @param string $message
     * @param array $recipients
     * @throws \Exception
     * @return void
     */
    public function fillForm($message, array $recipients)
    {
        $this->_rootElement->find($this->senderMessage)->setValue($message);
        if (count($recipients) > 3) {
            throw new \Exception('Maximum 3 email addresses.');
        }
        foreach ($recipients as $key => $recipient) {
            if ($key !== 0) {
                $this->_rootElement->find($this->addRecipient)->click();
            }
            /** @var CustomerInjectable $recipient */
            $this->_rootElement->find(sprintf($this->recipientFirstName, $key))->setValue($recipient->getFirstname());
            $this->_rootElement->find(sprintf($this->recipientEmail, $key))->setValue($recipient->getEmail());
        }
    }
}
