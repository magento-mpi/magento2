<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of block injection classes
     *
     * @var array
     */
    protected $_blockInjections = array(
        'Mage_Core_Model_Event_Manager',
        'Mage_Core_Model_Cache'
    );

    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Enterprise/GiftCard/_files/gift_card.php
     * @magentoDataFixture Enterprise/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new Varien_Event(array('order' => $order));
        $observer = new Varien_Event_Observer(array('event' => $event));

        $zendMailMock = $this->getMock('Zend_Mail', array('send')
        );
        $zendMailMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));

        $emailTemplateMock = $this->getMock('Mage_Core_Model_Email_Template', array('_getMail'),
            $this->_prepareConstructorArguments());
        $emailTemplateMock->expects($this->once())
            ->method('_getMail')
            ->will($this->returnValue($zendMailMock));

        $arguments = $this->_prepareConstructorArguments();
        $arguments[] = null;
        $arguments[] = null;
        $arguments[] = array('email_template_model' => $emailTemplateMock);
        $model = Mage::getModel('Enterprise_GiftCard_Model_Observer', $arguments);
        $model->generateGiftCardAccounts($observer);
        $this->assertEquals(
            array('area' => Mage_Core_Model_App_Area::AREA_FRONTEND, 'store' => 1),
            $emailTemplateMock->getDesignConfig()->getData()
        );

        $this->_checkOrderItemProductOptions($order, false);
    }

    /**
     * Check email sending related gift card product options
     *
     * @param Mage_Sales_Model_Order $order
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
            $arguments[] = Mage::getModel($injectionClass);
        }
        return $arguments;
    }
}
