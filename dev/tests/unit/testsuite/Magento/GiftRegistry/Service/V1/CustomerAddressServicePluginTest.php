<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Service\V1;

class CustomerAddressServicePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $addressRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $addressConverterMock;

    /**
     * @var CustomerAddressServicePlugin
     */
    private $plugin;

    protected function setUp()
    {
        $this->addressRegistryMock = $this->getMock('Magento\Customer\Model\AddressRegistry', [], [], '', false);
        $this->addressConverterMock = $this->getMock('Magento\Customer\Model\Address\Converter', [], [], '', false);
        $this->plugin = new CustomerAddressServicePlugin(
            $this->addressRegistryMock,
            $this->addressConverterMock
        );
    }

    public function testAroundGetAddressCreatesCustomAddressDataObjectIfAddressIdRelatesToGiftRegistry()
    {
        $addressId = 'gr_address_1';
        $addressMock = $this->getMock('Magento\Customer\Model\Address', array(), array(), '', false);
        $this->addressRegistryMock->expects($this->once())->method('retrieve')->with($addressId)
            ->will($this->returnValue($addressMock));
        $addressMock->expects($this->at(0))->method('setData')->with('is_default_shipping', false);
        $addressMock->expects($this->at(1))->method('setData')->with('is_default_billing', false);
        $dataObjectMock = $this->getMock('\Magento\Customer\Service\V1\Data\Address', [], [], '', false);
        $this->addressConverterMock->expects($this->once())->method('createAddressFromModel')
            ->with($addressMock, null, null)->will($this->returnValue($dataObjectMock));

        $serviceMock = $this->getMock('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        $this->assertEquals(
            $dataObjectMock,
            $this->plugin->aroundGetAddress($serviceMock, function () {
            }, $addressId)
        );
    }

    public function testAroundGetAddressProceedsInvocationIfAddressIdDoesNotRelateToGiftRegistry()
    {
        $addressId = 1;
        $dataObjectMock = $this->getMock('\Magento\Customer\Service\V1\Data\Address', [], [], '', false);
        $proceed = function ($id) use ($addressId, $dataObjectMock) {
            if ($id == $addressId) {
                return $dataObjectMock;
            }
            return null;
        };

        $serviceMock = $this->getMock('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        $this->assertEquals(
            $dataObjectMock,
            $this->plugin->aroundGetAddress($serviceMock, $proceed, $addressId)
        );
    }
}
