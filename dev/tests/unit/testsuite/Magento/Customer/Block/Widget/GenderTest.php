<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Widget;

use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Exception\NoSuchEntityException;

class GenderTest extends \PHPUnit_Framework_TestCase
{
    /** Constants used in the unit tests */
    const CUSTOMER_ENTITY_TYPE = 'customer';
    const GENDER_ATTRIBUTE_CODE = 'gender';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    private $_attributeMetadata;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata */
    private $_attribute;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Session */
    private $_customerSession;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\CustomerServiceInterface */
    private $_customerService;

    /** @var Gender */
    private $_block;

    public function setUp()
    {
        $this->_attribute = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()->getMock();

        $this->_attributeMetadata =
            $this->getMockBuilder('Magento\Customer\Service\V1\CustomerMetadataServiceInterface')
            ->getMockForAbstractClass();
        $this->_attributeMetadata->expects($this->any())->method('getCustomerAttributeMetadata')
            ->with(self::GENDER_ATTRIBUTE_CODE)
            ->will($this->returnValue($this->_attribute));

        $this->_customerService = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerAccountServiceInterface')
            ->getMockForAbstractClass();
        $this->_customerSession = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);

        $this->_block = new Gender(
            $this->getMock('Magento\View\Element\Template\Context', [], [], '', false),
            $this->getMock('Magento\Customer\Helper\Address', [], [], '', false),
            $this->_attributeMetadata,
            $this->_customerService,
            $this->_customerSession
        );
    }

    /**
     * Test the Gender::isEnabled() method.
     *
     * @param bool $isVisible Determines whether the 'gender' attribute is visible or enabled
     * @param bool $expectedValue The value we expect from Gender::isEnabled()
     * @return void
     *
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($isVisible, $expectedValue)
    {
        $this->_attribute->expects($this->once())->method('isVisible')->will($this->returnValue($isVisible));
        $this->assertSame($expectedValue, $this->_block->isEnabled());
    }

    /**
     * The testIsEnabled data provider.
     * @return array
     */
    public function isEnabledDataProvider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }

    public function testIsEnabledWithException()
    {
        $this->_attributeMetadata
            ->expects($this->any())
            ->method('getAttributeMetadata')
            ->will($this->throwException(new NoSuchEntityException('field', 'value')));
        $this->assertSame(false, $this->_block->isEnabled());
    }

    /**
     * Test the Gender::isRequired() method.
     *
     * @param bool $isRequired Determines whether the 'gender' attribute is required
     * @param bool $expectedValue The value we expect from Gender::isRequired()
     * @return void
     *
     * @dataProvider isRequiredDataProvider
     */
    public function testIsRequired($isRequired, $expectedValue)
    {
        $this->_attribute->expects($this->once())->method('isRequired')->will($this->returnValue($isRequired));
        $this->assertSame($expectedValue, $this->_block->isRequired());
    }

    /**
     * The testIsRequired data provider.
     * @return array
     */
    public function isRequiredDataProvider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }

    public function testIsRequiredWithException()
    {
        $this->_attributeMetadata
            ->expects($this->any())
            ->method('getAttributeMetadata')
            ->will($this->throwException(new NoSuchEntityException('field', 'value')));
        $this->assertSame(false, $this->_block->isRequired());
    }

    /**
     * Test the Gender::getCustomer() method.
     * @return void
     */
    public function testGetCustomer()
    {
        $data = ['firstname' => 'John', 'lastname' => 'Doe'];
        $customerDto = new Customer($data);

        $this->_customerSession
            ->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));
        $this->_customerService
            ->expects($this->once())->method('getCustomer')->with(1)->will($this->returnValue($customerDto));

        $customer = $this->_block->getCustomer();
        $this->assertSame($customerDto, $customer);

        $this->assertEquals('John', $customer->getFirstname());
        $this->assertEquals('Doe', $customer->getLastname());
    }

    /**
     * Test the Gender::getGenderOptions() method.
     * @return void
     */
    public function testGetGenderOptions()
    {
        $options = [
            ['label' => __('Male'), 'value' => 'M'],
            ['label' => __('Female'), 'value' => 'F']
        ];

        $this->_attribute->expects($this->once())->method('getOptions')->will($this->returnValue($options));
        $this->assertSame($options, $this->_block->getGenderOptions());
    }
}
