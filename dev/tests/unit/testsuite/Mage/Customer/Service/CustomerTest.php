<?php
/**
 * Unit test for customer service layer Mage_Customer_Service_Customer
 *
 * @copyright {}
 */
class Mage_Customer_Service_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Service_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_service;

    /**
     * @var Mage_Customer_Model_Customer_Factory
     */
    protected $_customerFactory;

    /**
     * @var Mage_Customer_Model_Address_Factory
     */
    protected $_addressFactory;

    protected function setUp()
    {
        $helper = $this->getMockBuilder('Mage_Customer_Helper_Data')
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $this->_customerFactory = $this->getMockBuilder('Mage_Customer_Model_Customer_Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $this->_addressFactory = $this->getMockBuilder('Mage_Customer_Model_Address_Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $this->_service = new Mage_Customer_Service_Customer($helper, $this->_customerFactory, $this->_addressFactory);
    }

    protected function tearDown()
    {
        unset($this->_service);
    }

    /**
     * Test beforeSave and afterSave callback are set correctly
     */
    public function testSetBeforeSaveCallback()
    {
        $this->assertInstanceOf('Mage_Customer_Service_Customer', $this->_service->setBeforeSaveCallback('intval'));
        $this->assertAttributeEquals('intval', '_beforeSaveCallback', $this->_service);
    }

    /**
     * Test beforeSave and afterSave callback are set correctly
     */
    public function testSetAfterSaveCallback()
    {
        $this->assertInstanceOf('Mage_Customer_Service_Customer', $this->_service->setAfterSaveCallback('intval'));
        $this->assertAttributeEquals('intval', '_afterSaveCallback', $this->_service);
    }

    /**
     * Test setIsAdminStore setter
     */
    public function testSetIsAdminStore()
    {
        $this->assertInstanceOf('Mage_Customer_Service_Customer', $this->_service->setIsAdminStore(true));
        $this->assertAttributeEquals(true, '_isAdminStore', $this->_service);
    }

    /**
     * @param bool $isAdminStore
     * @param array $customerData
     * @param array $expectedData
     * @dataProvider createDataProvider
     */
    public function testCreate($isAdminStore, $customerData, $expectedData)
    {
        $customerMock = $this->getMockBuilder('Mage_Customer_Model_Customer')
            ->setMethods(array('save', 'generatePassword'))
            ->disableOriginalConstructor()
            ->getMock();
        $customerMock->expects($this->once())
            ->method('save');
        if (array_key_exists('autogenerate_password', $customerData)) {
            $customerMock->expects($this->once())
                ->method('generatePassword')
                ->will($this->returnValue('generated_password'));
        }
        $this->_customerFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($customerMock));

        $this->_service->setIsAdminStore($isAdminStore);
        $this->assertInstanceOf('Mage_Customer_Model_Customer',
            $this->_service->create($customerData));
        $this->assertEquals($expectedData, $customerMock->toArray(array_keys($expectedData)));
    }

    public function createDataProvider()
    {
        return array(
            'force confirmed not set #1' => array(
                'isAdminStore' => false,
                'customerData' => array(
                    'password' => '123123q'
                ),
                'expectedData' => array(
                    'password' => '123123q',
                    'force_confirmed' => null
                ),
            ),
            'force confirmed not set #2' => array(
                'isAdminStore' => true,
                'customerData' => array(),
                'expectedData' => array(
                    'force_confirmed' => null
                ),
            ),
            'force confirmed is set' => array(
                'isAdminStore' => true,
                'customerData' => array(
                    'password' => '123123q'
                ),
                'expectedData' => array(
                    'password' => '123123q',
                    'force_confirmed' => true
                ),
            ),
            'auto generated password' => array(
                'isAdminStore' => true,
                'customerData' => array(
                    'autogenerate_password' => true
                ),
                'expectedData' => array(
                    'password' => 'generated_password',
                    'force_confirmed' => true
                ),
            )
        );
    }
}
