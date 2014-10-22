<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        foreach ($emails as $key => $email) {
            if ($this->_rootElement->find(
                sprintf($this->invitationRow, $email, $status),
                Locator::SELECTOR_XPATH
            )->isVisible()
            ) {
                $availableEmails[$key] = $email;
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
