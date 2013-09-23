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

class Magento_Weee_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Weee_Model_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Weee_Model_Observer');
    }

    /**
     * @magentoConfigFixture current_store tax/weee/enable 1
     * @magentoDataFixture Magento/Weee/_files/product_with_fpt.php
     */
    public function testUpdateConfigurableProductOptions()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('current_product');
        $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
        $this->_model->updateConfigurableProductOptions($eventObserver);
        $this->assertEquals(array(), $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions());

        $product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
        $objectManager->get('Magento_Core_Model_Registry')->register('current_product', $product->load(1));

        foreach (array(Magento_Weee_Model_Tax::DISPLAY_INCL, Magento_Weee_Model_Tax::DISPLAY_INCL_DESCR) as $mode) {
            Mage::app()->getStore()->setConfig('tax/weee/display', $mode);
            $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
            $this->_model->updateConfigurableProductOptions($eventObserver);
            $this->assertEquals(
                array('oldPlusDisposition' => 0.07, 'plusDisposition' => 0.07),
                $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions()
            );
        }

        foreach (array(
                Magento_Weee_Model_Tax::DISPLAY_EXCL, Magento_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL) as $mode) {
            Mage::app()->getStore()->setConfig('tax/weee/display', $mode);
            $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
            $this->_model->updateConfigurableProductOptions($eventObserver);
            $this->assertEquals(
                array('oldPlusDisposition' => 0.07, 'plusDisposition' => 0.07, 'exclDisposition' => true),
                $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions()
            );
        }
    }

    /**
     * @return Magento_Event_Observer
     */
    protected function _createEventObserverForUpdateConfigurableProductOptions()
    {
        $response = new Magento_Object(array('additional_options' => array()));
        $event = new Magento_Event(array('response_object' => $response));
        return new Magento_Event_Observer(array('event' => $event));
    }
}
