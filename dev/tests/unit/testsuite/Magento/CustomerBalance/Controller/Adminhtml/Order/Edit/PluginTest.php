<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Controller\Adminhtml\Order\Edit;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var \Magento\CustomerBalance\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBalanceData;

    /**
     * @var \Magento\Backend\Model\Session\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionQuote;

    /**
     * @var \Magento\GiftCardAccount\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardAccountData;

    protected function setUp()
    {
        $this->messageManager = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->customerBalanceData = $this->getMockBuilder('Magento\CustomerBalance\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionQuote = $this->getMockBuilder('Magento\Backend\Model\Session\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftCardAccountData = $this->getMockBuilder('Magento\GiftCardAccount\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->plugin = $objectManagerHelper->getObject(
            'Magento\CustomerBalance\Controller\Adminhtml\Order\Edit\Plugin',
            [
                'sessionQuote' => $this->sessionQuote,
                'messageManager' => $this->messageManager,
                'customerBalanceData' => $this->customerBalanceData,
                'giftCardAccountData' => $this->giftCardAccountData
            ]
        );
    }

    /**
     * @dataProvider beforeIndexActionDataProvider
     */
    public function testBeforeIndexAction($giftCards, $storeCredit, $notice, $error)
    {
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionQuote->expects($this->atLeastOnce())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));

        $this->giftCardAccountData->expects($this->atLeastOnce())
            ->method('getCards')
            ->with($orderMock)
            ->will($this->returnValue($giftCards));

        if ($giftCards) {
            $this->customerBalanceData->expects($this->atLeastOnce())
                ->method('isEnabled')
                ->will($this->returnValue($storeCredit));
        }

        if ($notice) {
            $this->messageManager->expects($this->at(0))
                ->method('addNotice')
                ->with('We will refund the gift card amount to your customer’s store credit');
        }

        if ($error) {
            $this->messageManager->expects($this->at(1))
                ->method('addError')
                ->with('Please enable Store Credit to refund the gift card amount to your customer');
        }


        $this->plugin->beforeIndexAction();
    }

    /**
     * @return array
     */
    public function beforeIndexActionDataProvider()
    {
        return [
            [
                'giftCards' => [],
                'storeCredit' => false,
                'notice' => false,
                'error' => false
            ], [
                'giftCards' => [],
                'storeCredit' => true,
                'notice' => false,
                'error' => false
            ], [
                'giftCards' => [['ba' => 50, 'a' => 50, 'c' => 'someCode']],
                'storeCredit' => true,
                'notice' => true,
                'error' => false
            ], [
                'giftCards' => [['ba' => 50, 'a' => 50, 'c' => 'someCode']],
                'storeCredit' => false,
                'notice' => true,
                'error' => true
            ]
        ];
    }
}
