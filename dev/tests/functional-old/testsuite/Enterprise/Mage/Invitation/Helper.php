<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     Mage_Invitations
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Invitation_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Send Invitation from customer account on frontend
     * Preconditions: Customer is created and logged on website
     *
     * @param int $count
     * @param string $messageType
     * @param string $messageName
     */
    public function sendInvitationFrontend($count, $messageType = 'success', $messageName = 'success_send')
    {
        $this->frontend('my_invitations');
        $this->clickButton('send_invitation');
        $email = array();
        for ($i = 1; $i <= $count; $i++) {
            $data = $this->generate('email', 15, 'valid');
            $this->addParameter('num', $i);
            $this->fillField('email', $data);
            $email[$i] = $data;
        }
        $this->fillField('message_textarea', $this->generate('text'));
        $this->clickButton('send_invitation');
        //Verification
        for ($i = 1; $i <= $count; $i++) {
            $this->addParameter('email', $email[$i]);
            $this->assertMessagePresent($messageType, $messageName);
        }
    }
}

