<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class CreditmemoGetTest
 */
class CreditmemoGetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoGet
     */
    protected $creditmemoGet;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoRepositoryMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\CreditmemoMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoMapperMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * SetUp
     *
     * @return void
     */
    protected function setUp()
    {
        $this->creditmemoRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\CreditmemoRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->creditmemoMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\CreditmemoMapper',
            ['extractDto'],
            [],
            '',
            false
        );
        $this->creditmemoMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo',
            [],
            [],
            '',
            false
        );
        $this->dataObjectMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\Creditmemo',
            [],
            [],
            '',
            false
        );

        $this->creditmemoGet = new CreditmemoGet(
            $this->creditmemoRepositoryMock,
            $this->creditmemoMapperMock
        );
    }

    /**
     * Test creditmemo get service
     */
    public function testInvoke()
    {
        $this->creditmemoRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->creditmemoMock));
        $this->creditmemoMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->creditmemoMock))
            ->will($this->returnValue($this->dataObjectMock));
        $this->assertEquals($this->dataObjectMock, $this->creditmemoGet->invoke(1));
    }
}
