<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @subpackage  integration_tests
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
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Weee\Model\Observer');
    }

    /**
     * @magentoConfigFixture current_store tax/weee/enable 1
     * @magentoDataFixture Magento/Weee/_files/product_with_fpt.php
     */
    public function testUpdateConfigurableProductOptions()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('current_product');
        $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
        $this->_model->updateConfigurableProductOptions($eventObserver);
        $this->assertEquals(array(), $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions());

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', $product->load(1));

        foreach (array(\Magento\Weee\Model\Tax::DISPLAY_INCL, \Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR) as $mode) {
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
                ->getStore()->setConfig('tax/weee/display', $mode);
            $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
            $this->_model->updateConfigurableProductOptions($eventObserver);
            $this->assertEquals(
                array('oldPlusDisposition' => 0.07, 'plusDisposition' => 0.07),
                $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions()
            );
        }

        foreach (array(
                \Magento\Weee\Model\Tax::DISPLAY_EXCL, \Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL) as $mode) {
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
                ->getStore()->setConfig('tax/weee/display', $mode);
            $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
            $this->_model->updateConfigurableProductOptions($eventObserver);
            $this->assertEquals(
                array('oldPlusDisposition' => 0.07, 'plusDisposition' => 0.07, 'exclDisposition' => true),
                $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions()
            );
        }
    }

    /**
     * @return \Magento\Event\Observer
     */
    protected function _createEventObserverForUpdateConfigurableProductOptions()
    {
        $response = new \Magento\Object(array('additional_options' => array()));
        $event = new \Magento\Event(array('response_object' => $response));
        return new \Magento\Event\Observer(array('event' => $event));
    }
}
