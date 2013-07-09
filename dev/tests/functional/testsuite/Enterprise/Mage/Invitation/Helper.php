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
     * @param $count
     * @param string $messageType
     * @param $messageName
     */
    public function sendInvitationFrontend($count, $messageType = 'success', $messageName)
    {
        $this->navigate('my_invitations');
        $this->clickButton('send_invitation');
        $email = array();
        $this->addParameter('num', $count);
        if ($count >= 1 && $this->controlIsVisible('field', 'email')) {
            for ($i = 1; $i <= $count; $i++) {
                $data = $this->generate('email', 15, 'valid');
                $this->addParameter('num', $i);
                $this->fillField('email', $data);
                $email[$i] = $data;
            }
        } else {
            $this->fail('Enter valid qty for invitations');
        }
        $this->fillField('message_textarea', $this->generate('text'));
        $this->clickButton('send_invitation');
        //Verification
        for ($i = 1; $i <= $count; $i++) {
            $this->addParameter('email', $email[$i]);
            $this->assertMessagePresent($messageType, $messageName);
        }
    }

    /**
     * Send Invitation from customer account on frontend with newly created customer on backend
     * @param $count
     */
    public function sendInvitationWithNewlyCreatedCustomer($count)
    {
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->sendInvitationFrontend($count, $messageType = 'success', $messageName = 'success_send');
    }
}

