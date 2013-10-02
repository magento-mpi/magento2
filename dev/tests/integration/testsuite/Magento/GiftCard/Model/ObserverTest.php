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

namespace Magento\GiftCard\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
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
        'Magento\Core\Model\Email\Template\Config',
    );

    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     * @magentoDataFixture Magento/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\App')->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new \Magento\Event(array('order' => $order));
        $observer = new \Magento\Event\Observer(array('event' => $event));

        $emailTemplateMock = $this->getMock(
            'Magento\Core\Model\Email\Template',
            array('_getMail'),
            array(
                $objectManager->get('Magento\Core\Model\Context'),
                $objectManager->get('Magento\Core\Model\Registry'),
                $objectManager->get('Magento\Core\Model\App\Emulation'),
                $objectManager->get('Magento\Filesystem'),
                $objectManager->get('Magento\Core\Model\View\Url'),
                $objectManager->get('Magento\Core\Model\View\FileSystem'),
                $objectManager->get('Magento\Core\Model\View\DesignInterface'),
                $objectManager->get('Magento\Core\Model\Store\Config'),
                $objectManager->get('Magento\Core\Model\Config'),
                $objectManager->get('Magento\Core\Model\Email\Template\FilterFactory'),
                $objectManager->get('Magento\Core\Model\StoreManager'),
                $objectManager->get('Magento\Core\Model\Dir'),
                $objectManager->get('Magento\Core\Model\Email\Template\Config'),
            )
        );
        $emailTemplateMock->expects($this->once())
            ->method('_getMail')
            ->will($this->returnValue($this->getMock('Zend_Mail', array('send'), array('utf-8'))));

        /** @var $model \Magento\GiftCard\Model\Observer */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftCard\Model\Observer', array(
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
            $arguments[] = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create($injectionClass);
        }
        return $arguments;
    }
}
