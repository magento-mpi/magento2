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
        'Magento\Model\Context',
        'Magento\Registry',
        'Magento\App\Filesystem',
        'Magento\View\Url',
        'Magento\View\FileSystem',
        'Magento\Core\Model\View\Design',
        'Magento\Core\Model\Store\Config',
        'Magento\Email\Model\Template\Config',
    );

    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     * @magentoDataFixture Magento/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\App')->loadArea(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new \Magento\Event(array('order' => $order));
        $observer = new \Magento\Event\Observer(array('event' => $event));

        /** @var $model \Magento\GiftCard\Model\Observer */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftCard\Model\Observer');
        $model->generateGiftCardAccounts($observer);

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
