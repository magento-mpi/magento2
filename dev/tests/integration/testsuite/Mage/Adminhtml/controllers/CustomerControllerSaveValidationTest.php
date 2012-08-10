<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__) . '/CustomerControllerSaveSetup.php';

class Mage_Adminhtml_CustomerControllerSaveValidationTest extends Mage_Adminhtml_CustomerControllerSaveSetup
{
    protected function _initializeCustomerFormMock()
    {
        parent::_initializeCustomerFormMock();
        $this->_customerFromMock->expects($this->once())
            ->method('setEntity')->will($this->returnSelf());
    }

    protected function _initializeRegistryMock()
    {
        parent::_initializeRegistryMock();
        $this->_registryMock->expects($this->once())->method('register');
    }

    protected function _initializeFormData()
    {
        $this->_formData = array(
            'website_id' => 0,
            'group_id' => 1,
            'disable_auto_group_change' => 'test',
            'default_billing' => 'test default_billing',
            'default_shipping' => 'test default_shipping',
            'confirmation' => 'test confirmation',
            'sendemail_store_id' => 'test sendemail_store_id',
            'password' => 'auto',
            'sendemail' => 'test sendemail',
            'new_password' => 'auto',
        );
    }

    protected function _initializeCustomerMock()
   {
        parent::_initializeCustomerMock();
        $this->_customerMock->expects($this->any())->method('compactData');
    }

    public function testSaveActionPasswordGenerationAndSendEmails()
    {
        $expectedFormData = $this->_formData;
        $expectedPostData = $this->_postData;

        $expectedFormData['disable_auto_group_change'] = '1'; //check Handle 'disable auto_group_change' attribute
        unset($expectedPostData['address']['_template_']); //test Unset template data

        /** Prepare mocks */

        $this->_customerFromMock->expects($this->once())
            ->method('validateData')->with($expectedFormData)->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())
            ->method('getAddressesCollection')->will($this->returnValue(array()));

        $this->_customerMock->expects($this->once())
            ->method('getConfirmation')->will($this->returnValue(false));

        $this->_customerMock->expects($this->once())->method('setIsSubscribed')->with(true);

        $this->_customerMock->expects($this->once())
            ->method('setSendemailStoreId')->with($expectedPostData['account']['sendemail_store_id']);

        $this->_customerMock->expects($this->once())
            ->method('isObjectNew')->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())->method('save');

        $this->_customerMock->expects($this->once())
            ->method('getWebsiteId')->will($this->returnValue(1));

        $this->_customerMock->expects($this->any())->method('setForceConfirmed')->with(true);

        $this->_customerMock->expects($this->exactly(2))->method('setPassword')->with(
            $this->logicalOr($this->equalTo('auto'), $this->equalTo('auto generated'))
        );

        $this->_customerMock->expects($this->once())->method('getPassword')->will($this->returnValue('auto'));
        $this->_customerMock->expects($this->any())
            ->method('generatePassword')->will($this->returnValue('auto generated'));

        $this->_customerMock->expects($this->once())->method('getSendemailStoreId')->will($this->returnValue(1));
        $this->_customerMock->expects($this->once())->method('sendNewAccountEmail')->with('registered', '', 1);

        $this->_customerMock->expects($this->once())->method('changePassword')->with('auto generated');
        $this->_customerMock->expects($this->once())->method('sendPasswordReminderEmail');

        $this->_sessionMock->expects($this->once())->method('addSuccess')->with('The customer has been saved.');

        $this->_helperMock->expects($this->once())
            ->method('__')->with('The customer has been saved.')->will($this->returnArgument(0));

        $eventParams = array(
            'customer' => $this->_customerMock,
            'request' => $this->_requestMock
        );
        $this->_eventManagerMock->expects($this->at(0))
            ->method('dispatch')->with('adminhtml_customer_prepare_save', $eventParams);
        $this->_eventManagerMock->expects($this->at(1))
            ->method('dispatch')->with('adminhtml_customer_save_after', $eventParams);

        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)->will($this->returnValue(true));

        /* Call action */
        $this->_model->saveAction();
    }
}
