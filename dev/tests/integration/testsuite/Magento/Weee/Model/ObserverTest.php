<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Weee\Model\Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Weee\Model\Observer'
        );
    }

    /**
     * @magentoConfigFixture current_store tax/weee/enable 1
     * @magentoDataFixture Magento/Weee/_files/product_with_fpt.php
     */
    public function testUpdateProductOptions()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Framework\Registry')->unregister('current_product');
        $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
        $this->_model->updateProductOptions($eventObserver);
        $this->assertEquals([], $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions());

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $objectManager->get('Magento\Framework\Registry')->register('current_product', $product->load(1));
    }

    /**
     * @return \Magento\Framework\Event\Observer
     */
    protected function _createEventObserverForUpdateConfigurableProductOptions()
    {
        $response = new \Magento\Framework\Object(['additional_options' => []]);
        $event = new \Magento\Framework\Event(['response_object' => $response]);
        return new \Magento\Framework\Event\Observer(['event' => $event]);
    }
}
