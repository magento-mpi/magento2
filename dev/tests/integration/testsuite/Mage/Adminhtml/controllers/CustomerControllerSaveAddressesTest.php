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

class Mage_Adminhtml_CustomerControllerSaveAddressesTest extends Mage_Adminhtml_CustomerControllerSaveSetup
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerAddressNewMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerAddressOldMock;

    public function setUp()
    {
        /** Initialize customer address mock */
        $this->_initializeCustomerAddressMock();

        parent::setUp();
    }

    protected function _initializeRegistryMock()
    {
        parent::_initializeRegistryMock();
        $this->_registryMock->expects($this->once())->method('register');
    }

    protected function _initializeCustomerFormMock()
    {
        $this->_customerFromMock = $this->getMock('Mage_Customer_Model_Form',
            array('setEntity', 'setFormCode', 'ignoreInvisible', 'extractData', 'validateData', 'compactData'),
            array(),
            '',
            false,
            false
        );

        $this->_customerFromMock->expects($this->any())
            ->method('setEntity')->will($this->returnSelf());

        $this->_customerFromMock->expects($this->any())
            ->method('setFormCode')
            ->with($this->logicalOr('adminhtml_customer', 'adminhtml_customer_address'))
            ->will($this->returnSelf());

        $this->_customerFromMock->expects($this->any())
            ->method('ignoreInvisible')->with(false)->will($this->returnSelf());

        $this->_customerFromMock->expects($this->any())
            ->method('extractData')->will($this->returnValue($this->_formData));
    }

    protected function _getObjectFactoryMap()
    {
        $objectFactoryMap = array(
            array('Mage_Customer_Model_Customer', array(), $this->_customerMock),
            array('Mage_Customer_Model_Form', array(), $this->_customerFromMock),
            array('Mage_Customer_Model_Address' , array(), $this->_customerAddressOldMock),
        );

        return $objectFactoryMap;
    }

    protected function _initializeCustomerMock()
    {
        parent::_initializeCustomerMock();

        $this->_customerMock->expects($this->any())->method('compactData');
        $addressIdMap = array(
            array(0, null),
            array(1, $this->_customerAddressNewMock),
        );
        $this->_customerMock->expects($this->any())
            ->method('getAddressItemById')->will($this->returnValueMap($addressIdMap));
    }

    protected function _initializePostData()
    {
        $this->_postData = array(
            'account' => $this->_formData,
            'subscription' => array(),
            'address' => array(
                '_template_' => 'test template',
                '0' => 'address 1',
                '1' => 'address 2',
            )
        );
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

    protected function _initializeCustomerAddressMock()
    {
        $this->_customerAddressNewMock = $this->getMock('Mage_Customer_Model_Address', array(), array(), '', false);
        $this->_customerAddressNewMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->_customerAddressNewMock->expects($this->any())->method('setData')->with('_deleted', true);

        $this->_customerAddressOldMock = $this->getMock('Mage_Customer_Model_Address', array(), array(), '', false);
        $this->_customerAddressOldMock->expects($this->any())->method('getId')->will($this->returnValue(2));
        $this->_customerAddressOldMock->expects($this->any())->method('setData')->with('_deleted', true);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_customerAddressOldMock);
        unset($this->_customerAddressNewMock);
    }

    public function testSaveActionCustomerAddresses()
    {
        $this->_customerFromMock->expects($this->any())->method('validateData')->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())
            ->method('getAddressesCollection')
            ->will($this->returnValue(array($this->_customerAddressNewMock, $this->_customerAddressOldMock)));
        /* Call action */
        $this->_model->saveAction();
    }
}
