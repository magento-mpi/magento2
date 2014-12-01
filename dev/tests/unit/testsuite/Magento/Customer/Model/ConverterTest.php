<?php
/**
 * Unit test for converter \Magento\Customer\Model\Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject | AttributeMetadataInterface */
    private $_attributeMetadata;

    /** @var  \PHPUnit_Framework_MockObject_MockObject | CustomerMetadataInterface */
    private $_customerMetadata;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $customerBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_customerMetadata = $this->getMockForAbstractClass(
            'Magento\Customer\Api\CustomerMetadataInterface',
            array(),
            '',
            false
        );

        $this->_customerMetadata->expects(
            $this->any()
        )->method(
            'getAttributeMetadata'
        )->will(
            $this->returnValue($this->_attributeMetadata)
        );

        $this->_customerMetadata->expects(
            $this->any()
        )->method(
            'getCustomAttributesMetadata'
        )->will(
            $this->returnValue(array())
        );

        $this->_attributeMetadata = $this->getMock(
            'Magento\Customer\Api\Data\AttributeMetadataInterface',
            array(),
            array(),
            '',
            false
        );

        $this->customerBuilderMock = $this->getMock(
            'Magento\Customer\Api\Data\CustomerDataBuilder',
            array('populateWithArray','setId','setFirstname','setLastname','setEmail','create'),
            array(),
            '',
            false
        );
        $this->customerFactoryMock = $this->getMock(
            'Magento\Customer\Model\CustomerFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->storeManagerMock = $this->getMock(
            'Magento\Framework\StoreManagerInterface',
            array(),
            array(),
            '',
            false
        );
        $this->extensibleDataObjectConverter = $this->getMock(
            'Magento\Framework\Api\ExtensibleDataObjectConverter',
            array(),
            array(),
            '',
            false
        );
    }

    public function testCreateCustomerFromModel()
    {
        $this->markTestSkipped("Will need to fix");
        $customerModelMock = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getFirstname', 'getLastname', 'getEmail', 'getAttributes', 'getData', '__wakeup'])
            ->getMock();

        $customerMock = $this->getMockBuilder('Magento\Customer\Api\Data\CustomerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $attributeModelMock = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->disableOriginalConstructor()
            ->getMock();

        $attributeModelMock->expects($this->at(0))
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code'));

        $attributeModelMock->expects($this->at(1))
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code2'));

        $attributeModelMock->expects($this->at(2))
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code3'));

        $attributeModelMock1 = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->disableOriginalConstructor()
            ->getMock();

        $attributeModelMock1->expects($this->once())
            ->method('getAttribute')
            ->with('attribute_code')
            ->will($this->returnValue('attributeValue'));

        $this->_mockReturnValue(
            $customerModelMock,
            array(
                'getId' => 1,
                'getFirstname' => 'Tess',
                'getLastname' => 'Tester',
                'getEmail' => 'ttester@example.com',
                'getAttributes' => array($attributeModelMock1, $attributeModelMock, $attributeModelMock)
            )
        );

        $this->_mockReturnValue(
            $customerMock,
            array(
                'getId' => 1,
                'getFirstname' => 'Tess',
                'getLastname' => 'Tester',
                'getEmail' => 'ttester@example.com',
                'getAttributes' => array($attributeModelMock1, $attributeModelMock, $attributeModelMock)
            )
        );

        $map = array(
            array('attribute_code', null, 'attributeValue'),
            array('attribute_code2', null, 'attributeValue2'),
            array('attribute_code3', null, null)
        );
        $customerModelMock->expects($this->any())->method('getData')->will($this->returnValueMap($map));
        $customerMock->expects($this->any())->method('getData')->will($this->returnValueMap($map));

        $this->customerBuilderMock->expects($this->any())->method('populateWithArray')->will($this->returnSelf());
        $this->customerBuilderMock->expects($this->any())->method('setId')->will($this->returnSelf());
        $this->customerBuilderMock->expects($this->any())->method('setFirstname')->will($this->returnSelf());
        $this->customerBuilderMock->expects($this->any())->method('setLastname')->will($this->returnSelf());
        $this->customerBuilderMock->expects($this->any())->method('setEmail')->will($this->returnSelf());
        $this->customerBuilderMock->expects($this->any())->method('create')->will($this->returnValue($customerMock));

        $converter = new Converter(
            $this->customerBuilderMock,
            $this->customerFactoryMock,
            $this->storeManagerMock,
            $this->extensibleDataObjectConverter
        );
        $customerDataObject = $converter->createCustomerFromModel($customerModelMock);

        $expectedCustomerData = array(
            'firstname' => 'Tess',
            'email' => 'ttester@example.com',
            'lastname' => 'Tester',
            'id' => 1,
            'attribute_code' => 'attributeValue',
            'attribute_code2' => 'attributeValue2'
        );

        $this->assertEquals('Tess', $customerDataObject->getFirstname());
        $this->assertEquals('Tester', $customerDataObject->getLastname());
        $this->assertEquals('ttester@example.com', $customerDataObject->getEmail());
        $this->assertEquals(1, $customerDataObject->getId());
        $this->assertEquals('attributeValue', $customerDataObject->getCustomAttribute('attribute_code'));
        $this->assertEquals('attributeValue2', $customerDataObject->getCustomAttribute('attribute_code2'));
        /** @var \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor */
        $dataObjectProcessor = $this->_objectManager->getObject('Magento\Framework\Reflection\DataObjectProcessor');
        $this->assertEquals(
            $expectedCustomerData,
            $dataObjectProcessor
                ->buildOutputDataArray($customerDataObject, 'Magento\Customer\Api\Data\CustomerInterface'));
    }

    protected function prepareGetCustomerModel($customerId)
    {
        $customerMock = $this->getMock('Magento\Customer\Model\Customer', array(), array(), '', false);
        $customerMock->expects($this->once())
            ->method('load')
            ->with($customerId)
            ->will($this->returnSelf());
        $customerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($customerId));

        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerMock));

        $converter = new Converter(
            $this->customerBuilderMock,
            $this->customerFactoryMock,
            $this->storeManagerMock,
            $this->extensibleDataObjectConverter
        );
        return $converter;
    }

    public function testGetCustomerModel()
    {
        $customerId = 1;
        $converter = $this->prepareGetCustomerModel($customerId);
        $this->assertInstanceOf('Magento\Customer\Model\Customer', $converter->getCustomerModel($customerId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with customerId
     */
    public function testGetCustomerModelException()
    {
        $customerId = 0;
        $converter = $this->prepareGetCustomerModel($customerId);
        $this->assertInstanceOf('Magento\Customer\Model\Customer', $converter->getCustomerModel($customerId));
    }

    /**
     * @param $websiteId
     * @param $customerEmail
     * @param $customerId
     */
    protected function prepareGetCustomerModelByEmail($websiteId, $customerEmail, $customerId)
    {
        $customerMock = $this->getMock(
            'Magento\Customer\Model\Customer',
            array('setWebsiteId', 'loadByEmail', 'getId', '__wakeup'),
            array(),
            '',
            false
        );
        $customerMock->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->will($this->returnSelf());
        $customerMock->expects($this->once())
            ->method('loadByEmail')
            ->with($customerEmail)
            ->will($this->returnSelf());
        $customerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($customerId));

        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerMock));
    }

    public function testGetCustomerModelByEmail()
    {
        $customerId = 1;
        $websiteId = 1;
        $customerEmail = 'test@example.com';
        $this->prepareGetCustomerModelByEmail($websiteId, $customerEmail, $customerId);

        $storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->will($this->returnValue($websiteId));

        $this->storeManagerMock->expects($this->once())
            ->method('getDefaultStoreView')
            ->will($this->returnValue($storeMock));

        $converter = new Converter(
            $this->customerBuilderMock,
            $this->customerFactoryMock,
            $this->storeManagerMock,
            $this->extensibleDataObjectConverter
        );
        $this->assertInstanceOf(
            'Magento\Customer\Model\Customer',
            $converter->getCustomerModelByEmail('test@example.com')
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with email
     */
    public function testGetCustomerModelByEmailException()
    {
        $customerId = 0;
        $websiteId = 1;
        $customerEmail = 'test@example.com';
        $this->prepareGetCustomerModelByEmail($websiteId, $customerEmail, $customerId);

        $this->storeManagerMock->expects($this->never())->method('getDefaultStoreView');

        $converter = new Converter(
            $this->customerBuilderMock,
            $this->customerFactoryMock,
            $this->storeManagerMock,
            $this->extensibleDataObjectConverter
        );
        $this->assertInstanceOf(
            'Magento\Customer\Model\Customer',
            $converter->getCustomerModelByEmail('test@example.com', $websiteId)
        );
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue(\PHPUnit_Framework_MockObject_MockObject $mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())->method($method)->will($this->returnValue($value));
        }
    }
}
