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
        'Magento_Core_Model_View_Design'
    );

    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     * @magentoDataFixture Magento/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
            ->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new Magento_Event(array('order' => $order));
        $observer = new Magento_Event_Observer(array('event' => $event));

        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array('getStore'), array(), '', false);
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);;
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $emailTemplateMock = $this->getMock('Magento_Core_Model_Email_Template',
            array('sendTransactional', 'getSentSuccess'),
            array(
                $this->getMock('Magento_Core_Model_Context', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_App_Emulation', array(), array(), '', false),
                $this->getMock('Magento_Filesystem', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_View_Url', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_View_FileSystem', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_View_DesignInterface', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_Email_Template_FilterFactory', array(), array(), '', false),
                $storeManager,
                $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false)
            )
        );

        $emailTemplateMock->expects($this->once())
            ->method('_getMail')
            ->will($this->returnValue($zendMailMock));
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
