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
     * Check if invitation emails and status are available on My invitation grid
     *
     * @param array $emails
     * @param string $status
     * @return string
     */
    public function isInvitationInGrid(array $emails, $status)
    {
        $error = '';
        foreach ($emails as $email) {
            if (!$this->_rootElement->find(
                sprintf($this->invitationRow, $email, $status),
                Locator::SELECTOR_XPATH
            )->isVisible()
            ) {
                $error = "Email: " . $email . " with status: " . $status . " is not available in the grid.\n";
            }
        }
        return $error;
    }

    /**
     * Get invitation elements from invitations grid on frontend
     *
     * @param string $email
     * @param string $status
     * @return \Mtf\Client\Element[]
     */
    public function getInvitationElements($email, $status)
    {
        return $this->_rootElement->find(
            sprintf($this->invitationRow, $email, $status),
            Locator::SELECTOR_XPATH
        )->getElements();
    }
}
