<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Reward_Block_Tooltip_CheckoutTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareLayout()
    {
        $store = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $rewardAction = $this->getMockBuilder('Magento\Reward\Model\Action\AbstractAction')
            ->disableOriginalConstructor()
            ->getMock();
        $rewardHelper = $this->getMockBuilder('Magento\Reward\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabledOnFront'))
            ->getMock();
        $customerSession = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $rewardInstance = $this->getMockBuilder('Magento\Reward\Model\Reward')
            ->disableOriginalConstructor()
            ->setMethods(array('setWebsiteId', 'setCustomer', 'getActionInstance'))
            ->getMock();
        $storeManager = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore', 'getWebsiteId'))
            ->getMock();

        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);

        /** @var $block \Magento\Reward\Block\Tooltip */
        $block = $objectManager->getObject(
            'Magento\Reward\Block\Tooltip\Checkout',
            array(
                'data' => array('reward_type' => 'Magento\Reward\Model\Action\Salesrule'),
                'customerSession' => $customerSession,
                'rewardHelper' => $rewardHelper,
                'rewardInstance' => $rewardInstance,
                'storeManager' => $storeManager
            )
        );
        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);

        $rewardHelper->expects($this->any())
            ->method('isEnabledOnFront')
            ->will($this->returnValue(true));

        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));
        $storeManager->getStore()->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue(1));

        $rewardInstance->expects($this->any())
            ->method('setCustomer')
            ->will($this->returnValue($rewardInstance));
        $rewardInstance->expects($this->any())
            ->method('setWebsiteId')
            ->will($this->returnValue($rewardInstance));
        $rewardInstance->expects($this->any())
            ->method('getActionInstance')
            ->with('Magento\Reward\Model\Action\Salesrule')
            ->will($this->returnValue($rewardAction));

        $block->setLayout($layout);
    }
}
