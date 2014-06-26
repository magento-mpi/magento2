<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Customer\Helper\View|\PHPUnit_Framework_MockObject_MockObject */
    protected $object;

    /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerMetadataService;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Framework\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerMetadataService = $this->getMockBuilder(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface'
        )->disableOriginalConstructor()->getMock();

        $attributeMetadata = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMetadata->expects($this->any())->method('isVisible')->will($this->returnValue(true));
        $this->customerMetadataService->expects($this->any())
            ->method('getAttributeMetadata')
            ->will($this->returnValue($attributeMetadata));

        $this->object = new View($this->context, $this->customerMetadataService);
    }

    /**
     * @dataProvider getCustomerServiceDataProvider
     */
    public function testGetCustomerName($prefix, $firstName, $middleName, $lastName, $suffix, $result)
    {
        $customerData = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $customerData->expects($this->any())
            ->method('getPrefix')->will($this->returnValue($prefix));
        $customerData->expects($this->any())
            ->method('getFirstname')->will($this->returnValue($firstName));
        $customerData->expects($this->any())
            ->method('getMiddlename')->will($this->returnValue($middleName));
        $customerData->expects($this->any())
            ->method('getLastname')->will($this->returnValue($lastName));
        $customerData->expects($this->any())
            ->method('getSuffix')->will($this->returnValue($suffix));
        $this->assertEquals($result, $this->object->getCustomerName($customerData));
    }

    /**
     * @return array
     */
    public function getCustomerServiceDataProvider()
    {
        return array(
            array(
                'prefix', //prefix
                'first_name', //first_name
                'middle_name', //middle_name
                'last_name', //last_name
                'suffix', //suffix
                'prefix first_name middle_name last_name suffix', //result name
            ),
            array(
                '', //prefix
                'first_name', //first_name
                'middle_name', //middle_name
                'last_name', //last_name
                'suffix', //suffix
                'first_name middle_name last_name suffix', //result name
            ),
            array(
                'prefix', //prefix
                'first_name', //first_name
                '', //middle_name
                'last_name', //last_name
                'suffix', //suffix
                'prefix first_name last_name suffix', //result name
            ),
            array(
                'prefix', //prefix
                'first_name', //first_name
                'middle_name', //middle_name
                'last_name', //last_name
                '', //suffix
                'prefix first_name middle_name last_name', //result name
            ),
            array(
                '', //prefix
                'first_name', //first_name
                '', //middle_name
                'last_name', //last_name
                'suffix', //suffix
                'first_name last_name suffix', //result name
            ),
            array(
                'prefix', //prefix
                'first_name', //first_name
                '', //middle_name
                'last_name', //last_name
                '', //suffix
                'prefix first_name last_name', //result name
            ),
            array(
                '', //prefix
                'first_name', //first_name
                'middle_name', //middle_name
                'last_name', //last_name
                '', //suffix
                'first_name middle_name last_name', //result name
            ),
        );
    }
}
