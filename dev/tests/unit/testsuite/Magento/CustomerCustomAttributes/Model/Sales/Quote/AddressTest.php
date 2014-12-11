<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Model\Sales\Quote;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address
     */
    protected $address;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry');
        $this->resourceMock = $this->getMock(
            'Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote\Address',
            [],
            [],
            '',
            false
        );

        $this->address = new \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address(
            $this->contextMock,
            $this->registryMock,
            $this->resourceMock
        );
    }

    public function testAttachDataToEntities()
    {
        $entities = ['entity' => 'value'];

        $this->resourceMock->expects($this->once())
            ->method('attachDataToEntities')
            ->with($entities);

        $this->assertEquals($this->address, $this->address->attachDataToEntities($entities));
    }
}
