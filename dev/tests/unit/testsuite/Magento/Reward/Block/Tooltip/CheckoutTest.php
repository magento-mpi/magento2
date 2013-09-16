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
        $store = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();
        $rewardAction = $this->getMockBuilder('Magento_Reward_Model_Action_Abstract')
            ->disableOriginalConstructor()
            ->getMock();
        $rewardHelper = $this->getMockBuilder('Magento_Reward_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isEnabledOnFront'))
            ->getMock();
        $customerSession = $this->getMockBuilder('Magento_Customer_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        $rewardInstance = $this->getMockBuilder('Magento_Reward_Model_Reward')
            ->disableOriginalConstructor()
            ->setMethods(array('setWebsiteId', 'setCustomer', 'getActionInstance'))
            ->getMock();
        $storeManager = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore', 'getWebsiteId'))
            ->getMock();

        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);

        /** @var $block Magento_Reward_Block_Tooltip */
        $block = $objectManager->getObject(
            'Magento_Reward_Block_Tooltip_Checkout',
            array(
                'data' => array('reward_type' => 'Magento_Reward_Model_Action_Salesrule'),
                'customerSession' => $customerSession,
                'rewardHelper' => $rewardHelper,
                'rewardInstance' => $rewardInstance,
                'storeManager' => $storeManager
            )
        );
        $layout = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);

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
            ->with('Magento_Reward_Model_Action_Salesrule')
            ->will($this->returnValue($rewardAction));

        $block->setLayout($layout);
    }
}
