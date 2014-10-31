<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

class CustomerDetailsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Customer builder mock
     *
     * @var CustomerBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerBuilderMock;

    /**
     * Address builder mock
     *
     * @var AddressBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_addressBuilderMock;

    /**
     * Customer mock
     *
     * @var Customer | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerMock;

    /**
     * Address mock
     *
     * @var Address | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_addressMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->_customerBuilderMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\CustomerBuilder'
        )->disableOriginalConstructor()->getMock();
        $this->_addressBuilderMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\AddressBuilder'
        )->disableOriginalConstructor()->getMock();
        $this->_customerMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\Customer'
        )->disableOriginalConstructor()->getMock();
        $this->_addressMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\Address'
        )->disableOriginalConstructor()->getMock();

        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testConstructor()
    {
        $this->_customerBuilderMock->expects($this->once())->method('create')->will($this->returnValue('customer'));
        $customerDetailsBuilder = $this->objectManager->getObject(
            '\Magento\Customer\Service\V1\Data\CustomerDetailsBuilder',
            ['customerBuilder' => $this->_customerBuilderMock, 'addressBuilder' => $this->_addressBuilderMock]
        );
        $customerDetails = $customerDetailsBuilder->create();
        $this->assertEquals('customer', $customerDetails->getCustomer());
        $this->assertEquals(null, $customerDetails->getAddresses());
    }

    public function testSetCustomer()
    {
        $this->_customerBuilderMock->expects($this->never())->method('create')->will($this->returnValue('customer'));
        $customerDetailsBuilder = $this->objectManager->getObject(
            '\Magento\Customer\Service\V1\Data\CustomerDetailsBuilder',
            ['customerBuilder' => $this->_customerBuilderMock, 'addressBuilder' => $this->_addressBuilderMock]
        );
        $customerDetails = $customerDetailsBuilder->setCustomer($this->_customerMock)->create();
        $this->assertEquals($this->_customerMock, $customerDetails->getCustomer());
        $this->assertEquals(null, $customerDetails->getAddresses());
    }

    public function testSetAddresses()
    {
        $this->_customerBuilderMock->expects($this->once())->method('create')->will($this->returnValue('customer'));
        $customerDetailsBuilder = $this->objectManager->getObject(
            '\Magento\Customer\Service\V1\Data\CustomerDetailsBuilder',
            ['customerBuilder' => $this->_customerBuilderMock, 'addressBuilder' => $this->_addressBuilderMock]
        );
        $customerDetails = $customerDetailsBuilder->setAddresses(
            array($this->_addressMock, $this->_addressMock)
        )->create();
        $this->assertEquals('customer', $customerDetails->getCustomer());
        $this->assertEquals(array($this->_addressMock, $this->_addressMock), $customerDetails->getAddresses());
    }

    /**
     * @param array $data
     * @param Customer $expectedCustomer
     * @param Address[] $expectedAddresses
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulateWithArray($data, $expectedCustomerStr, $expectedAddressesStr)
    {
        $expectedCustomer = $expectedCustomerStr == 'customerMock' ? $this->_customerMock : $expectedCustomerStr;
        $expectedAddresses = null;
        if (isset($expectedAddressesStr)) {
            $expectedAddresses = array();
            foreach ($expectedAddressesStr as $addressStr) {
                $expectedAddresses[] = $addressStr == 'addressMock' ? $this->_addressMock : $addressStr;
            }
        }
        $this->_customerBuilderMock->expects(
            $this->any()
        )->method(
            'populateWithArray'
        )->will(
            $this->returnValue($this->_customerBuilderMock)
        );
        $this->_customerBuilderMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_customerMock)
        );

        $this->_addressBuilderMock->expects(
            $this->any()
        )->method(
            'populateWithArray'
        )->will(
            $this->returnValue($this->_addressBuilderMock)
        );
        $this->_addressBuilderMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_addressMock)
        );

        $customerDetailsBuilder = $this->objectManager->getObject(
            '\Magento\Customer\Service\V1\Data\CustomerDetailsBuilder',
            ['customerBuilder' => $this->_customerBuilderMock, 'addressBuilder' => $this->_addressBuilderMock]
        );
        $customerDetails = $customerDetailsBuilder->populateWithArray($data)->create();
        $this->assertEquals($expectedCustomer, $customerDetails->getCustomer());
        $this->assertEquals($expectedAddresses, $customerDetails->getAddresses());
    }

    public function populateWithArrayDataProvider()
    {
        return array(
            array(array('customer' => array('customerData')), 'customerMock', null),
            array(array('customer' => array('customerData'), 'addresses' => array()), 'customerMock', array()),
            array(array('customer' => array('customerData'), 'addresses' => null), 'customerMock', null),
            array(
                array('customer' => array('customerData'), 'addresses' => array(array('addressData'))),
                'customerMock',
                array('addressMock')
            ),
            array(
                array(
                    'customer' => array('customerData'),
                    'addresses' => array(array('addressData'), array('addressData'))
                ),
                'customerMock',
                array('addressMock', 'addressMock')
            ),
            array(array('addresses' => array(array('addressData'))), 'customerMock', array('addressMock')),
            array(
                array('customer' => null, 'addresses' => array(array('addressData'))),
                'customerMock',
                array('addressMock')
            ),
            array(array(), 'customerMock', null)
        );
    }
}
