<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Block\Customer;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class ListCustomer
 * List customer details block in "My invitations"
 */
class ListCustomer extends Block
{
    /**
     * Locator for 'Send Invitations' button
     *
     * @var string
     */
    protected $sendInvitationsButton = '.action.send';

    /**
     * Locator for email and its status
     *
     * @var string
     */
    protected $invitationRow = '//tr[td[contains(.,"%s")]][td[contains(.,"%s")]]';

    /**
     * Click 'Send Invitations' button
     *
     * @return void
     */
    public function sendInvitations()
    {
        $this->_rootElement->find($this->sendInvitationsButton)->click();
    }

    /**
     * Get available emails on My invitation grid
     *
     * @param array $emails
     * @param string $status
     * @return array
     */
    public function getAvailableEmails(array $emails, $status)
    {
        $availableEmails = [];
        foreach ($emails as $key => $value) {
            $email = $this->_rootElement->find(sprintf($this->invitationRow, $value, $status), Locator::SELECTOR_XPATH);
            if ($email->isVisible()) {
                $availableEmails[$key] = $value;
            }
        }
        return $availableEmails;
    }

    /**
     * Get number of invitations from invitations grid on frontend
     *
     * @param string $email
     * @param string $status
     * @return int
     */
    public function countInvitations($email, $status)
    {
        return count($this->_rootElement->find(
            sprintf($this->invitationRow, $email, $status),
            Locator::SELECTOR_XPATH
        )->getElements());
    }
}
