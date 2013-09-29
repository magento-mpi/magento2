<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of block injection classes
     *
     * @var array
     */
    protected $_blockInjections = array(
        'Magento_Core_Model_Context',
        'Magento_Core_Model_Registry',
        'Magento_Filesystem',
        'Magento_Core_Model_View_Url',
        'Magento_Core_Model_View_FileSystem',
        'Magento_Core_Model_View_Design',
        'Magento_Core_Model_Store_Config',
        'Magento_Core_Model_Email_Template_Config',
    );

    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     * @magentoDataFixture Magento/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_App')->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new Magento_Event(array('order' => $order));
        $observer = new Magento_Event_Observer(array('event' => $event));

        $emailTemplateMock = $this->getMock(
            'Magento_Core_Model_Email_Template',
            array('_getMail'),
            array(
                $objectManager->get('Magento_Core_Model_Context'),
                $objectManager->get('Magento_Core_Model_Registry'),
                $objectManager->get('Magento_Core_Model_App_Emulation'),
                $objectManager->get('Magento_Filesystem'),
                $objectManager->get('Magento_Core_Model_View_Url'),
                $objectManager->get('Magento_Core_Model_View_FileSystem'),
                $objectManager->get('Magento_Core_Model_View_DesignInterface'),
                $objectManager->get('Magento_Core_Model_Store_Config'),
                $objectManager->get('Magento_Core_Model_Config'),
                $objectManager->get('Magento_Core_Model_Email_Template_FilterFactory'),
                $objectManager->get('Magento_Core_Model_StoreManager'),
                $objectManager->get('Magento_Core_Model_Dir'),
                $objectManager->get('Magento_Core_Model_Email_Template_Config'),
            )
        );
        $emailTemplateMock->expects($this->once())
            ->method('_getMail')
            ->will($this->returnValue($this->getMock('Zend_Mail', array('send'), array('utf-8'))));

        /** @var $model Magento_GiftCard_Model_Observer */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_GiftCard_Model_Observer', array(
                'data' => array('email_template_model' => $emailTemplateMock)
            ));
        $model->generateGiftCardAccounts($observer);
        $this->assertEquals(
            array('area' => Magento_Core_Model_App_Area::AREA_FRONTEND, 'store' => 1),
            $emailTemplateMock->getDesignConfig()->getData()
        );

        $this->_checkOrderItemProductOptions($order, false);
    }

    /**
     * Check email sending related gift card product options
     *
     * @param Magento_Sales_Model_Order $order
     * @param bool $expectedEmpty
     */
    protected function _checkOrderItemProductOptions($order, $expectedEmpty)
    {
        $order->loadByIncrementId('100000001');
        $orderItems = $order->getAllItems();
        $orderItem = reset($orderItems);
        $options = $orderItem->getProductOptions();
        $this->assertEquals($expectedEmpty, empty($options['email_sent']));
        $this->assertEquals($expectedEmpty, empty($options['giftcard_created_codes']));
    }

    /**
     * List of block constructor arguments
     *
     * @return array
     */
    protected function _prepareConstructorArguments()
    {
        $arguments = array();
        foreach ($this->_blockInjections as $injectionClass) {
            $arguments[] = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create($injectionClass);
        }
        return $arguments;
    }
}
