<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Block;

class TooltipTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareLayout()
    {
        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $rewardAction = $this->getMockBuilder(
            'Magento\Reward\Model\Action\AbstractAction'
        )->disableOriginalConstructor()->getMock();
        $rewardHelper = $this->getMockBuilder(
            'Magento\Reward\Helper\Data'
        )->disableOriginalConstructor()->setMethods(
            ['isEnabledOnFront']
        )->getMock();
        $customerSession = $this->getMockBuilder(
            'Magento\Customer\Model\Session'
        )->disableOriginalConstructor()->getMock();
        $rewardInstance = $this->getMockBuilder(
            'Magento\Reward\Model\Reward'
        )->disableOriginalConstructor()->setMethods(
            ['setWebsiteId', 'setCustomer', 'getActionInstance', '__wakeup']
        )->getMock();
        $storeManager = $this->getMockBuilder(
            'Magento\Store\Model\StoreManager'
        )->disableOriginalConstructor()->setMethods(
            ['getStore', 'getWebsiteId']
        )->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var $block \Magento\Reward\Block\Tooltip */
        $block = $objectManager->getObject(
            'Magento\Reward\Block\Tooltip',
            [
                'data' => ['reward_type' => 'Magento\Reward\Model\Action\OrderExtra'],
                'customerSession' => $customerSession,
                'rewardHelper' => $rewardHelper,
                'rewardInstance' => $rewardInstance,
                'storeManager' => $storeManager
            ]
        );
        $layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);

        $rewardHelper->expects($this->any())->method('isEnabledOnFront')->will($this->returnValue(true));

        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $storeManager->getStore()->expects($this->any())->method('getWebsiteId')->will($this->returnValue(1));

        $rewardInstance->expects($this->any())->method('setCustomer')->will($this->returnValue($rewardInstance));
        $rewardInstance->expects($this->any())->method('setWebsiteId')->will($this->returnValue($rewardInstance));
        $rewardInstance->expects(
            $this->any()
        )->method(
            'getActionInstance'
        )->with(
            'Magento\Reward\Model\Action\OrderExtra'
        )->will(
            $this->returnValue($rewardAction)
        );

        $block->setLayout($layout);
    }
}
