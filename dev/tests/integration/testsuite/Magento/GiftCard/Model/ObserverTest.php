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
        'Magento\Core\Model\Context',
        'Magento\Core\Model\Registry',
        'Magento\Filesystem',
        'Magento\Core\Model\View\Url',
        'Magento\Core\Model\View\FileSystem',
        'Magento\Core\Model\View\Design',
        'Magento\Core\Model\Store\Config',
        'Magento\Core\Model\Config'
    );

    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     * @magentoDataFixture Magento/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $order = Mage::getModel('Magento\Sales\Model\Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new \Magento\Event(array('order' => $order));
        $observer = new \Magento\Event\Observer(array('event' => $event));

        $zendMailMock = $this->getMock('Zend_Mail', array('send'));
        $zendMailMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));

        $emailTemplateMock = $this->getMock('Magento\Core\Model\Email\Template', array('_getMail'),
            $this->_prepareConstructorArguments()
        );
        $emailTemplateMock->expects($this->once())
            ->method('_getMail')
            ->will($this->returnValue($zendMailMock));
        /** @var $model \Magento\GiftCard\Model\Observer */
        $model = Mage::getModel('Magento\GiftCard\Model\Observer', array(
            'data' => array('email_template_model' => $emailTemplateMock)
        ));
        $model->generateGiftCardAccounts($observer);
        $this->assertEquals(
            array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => 1),
            $emailTemplateMock->getDesignConfig()->getData()
        );

        $this->_checkOrderItemProductOptions($order, false);
    }

    /**
     * Check email sending related gift card product options
     *
     * @param \Magento\Sales\Model\Order $order
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
