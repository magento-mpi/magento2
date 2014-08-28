<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Action;

/**
 * Test Class CreditmemoEmailTest for Order Service
 *
 * @package Magento\Sales\Service\V1
 */
class CreditmemoEmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order\CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoNotifier
     */
    protected $notifier;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->creditmemoRepository = $this->getMock(
            '\Magento\Sales\Model\Order\CreditmemoRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->notifier = $this->getMock(
            '\Magento\Sales\Model\Order\CreditmemoNotifier',
            ['notify', '__wakeup'],
            [],
            '',
            false
        );

        $this->service = $objectManager->getObject(
            'Magento\Sales\Service\V1\Action\CreditmemoEmail',
            [
                'creditmemoRepository' => $this->creditmemoRepository,
                'notifier' => $this->notifier
            ]
        );
    }

    public function testInvoke()
    {
        $creditmemoId = 1;
        $creditmemo = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['__wakeup', 'getEmailSent'],
            [],
            '',
            false
        );

        $this->creditmemoRepository->expects($this->once())
            ->method('get')
            ->with($creditmemoId)
            ->will($this->returnValue($creditmemo));
        $this->notifier->expects($this->any())
            ->method('notify')
            ->with($creditmemo)
            ->will($this->returnValue(true));

        $this->assertTrue($this->service->invoke($creditmemoId));
    }
}
 