<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__) . '/CustomerControllerSaveSetup.php';

class Mage_Adminhtml_CustomerControllerSaveGeneralTest extends Mage_Adminhtml_CustomerControllerSaveSetup
{
    protected function _initializeCustomerFormMock()
    {
        parent::_initializeCustomerFormMock();
        $this->_customerFromMock->expects($this->once())
            ->method('setEntity')->with($this->_customerMock)->will($this->returnSelf());
    }

    protected function _initializeRegistryMock()
    {
        parent::_initializeRegistryMock();
        $this->_registryMock->expects($this->once())
            ->method('register')->with('current_customer', $this->_customerMock);
    }

    protected function _initializePostData()
    {
        $this->_postData = array(
            'account' => $this->_formData,
        );
    }

    public function testSaveActionRequestedCustomerIsLoadedToRegistryAndInvalidFormData()
    {
        $errors = array('error1', 'error2');

        /** Prepare mocks */
        $this->_customerFromMock->expects($this->once())
            ->method('validateData')->with($this->_formData)->will($this->returnValue($errors));

        $this->_sessionMock->expects($this->exactly(2))->method('addError');
        $this->_sessionMock->expects($this->once())
            ->method('setCustomerData')->with($this->_postData);

        /* Call action */
        $this->_model->saveAction();
    }

    public function testSaveActionValidFormDataAndRedirectedBack()
    {
        /** Prepare mocks */
        $this->_customerFromMock->expects($this->once())
            ->method('validateData')->with($this->_formData)->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())
            ->method('getAddressesCollection')->will($this->returnValue(array()));

        $this->_customerMock->expects($this->once())
            ->method('getConfirmation')->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())
            ->method('isObjectNew')->will($this->returnValue(false));

        $this->_prepareMocksForSuccessCustomerSave();

        /* Call action */
        $this->_model->saveAction();
    }

    public function testSaveActionThrowCoreException()
    {
        /** Prepare mocks */
        $this->_customerMock->expects($this->once())
            ->method('getAddressesCollection')->will($this->returnValue(array()));

        $this->_customerFromMock->expects($this->once())
            ->method('validateData')->with($this->_formData)->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())
            ->method('isObjectNew')->will($this->returnValue(false));

        $this->_customerMock->expects($this->once())
            ->method('save')->will($this->throwException(new Mage_Core_Exception('error')));

        $this->_sessionMock->expects($this->once())->method('addError')->with('error');
        $this->_sessionMock->expects($this->once())
            ->method('setCustomerData')->with($this->_postData);

        /* Call action */
        $this->_model->saveAction();
    }

    public function testSaveActionThrowGeneralException()
    {
        /** Prepare mocks */
        $this->_customerMock->expects($this->once())
            ->method('getAddressesCollection')->will($this->returnValue(array()));

        $this->_customerFromMock->expects($this->once())
            ->method('validateData')->with($this->_formData)->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())
            ->method('isObjectNew')->will($this->returnValue(false));

        $this->_customerMock->expects($this->once())
            ->method('save')->will($this->throwException(new Exception('error')));

        $this->_sessionMock->expects($this->once())->method('addException');
        $this->_sessionMock->expects($this->once())
            ->method('setCustomerData')->with(array('account' => $this->_formData));

        /* Call action */
        $this->_model->saveAction();
    }
}
