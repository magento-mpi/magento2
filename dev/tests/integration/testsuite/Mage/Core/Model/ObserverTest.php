<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme observer
 */
class Mage_Core_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * Theme registration test
     *
     * @magentoDbIsolation enabled
     */
    public function testThemeRegistration()
    {
        $pathPattern = implode(DS, array(__DIR__, '_files', 'design', 'frontend', 'default', '*', 'theme.xml'));

        $eventObserver = $this->_createEventObserverForThemeRegistration();
        $eventObserver->getEvent()->setPathPattern($pathPattern);

        $observer = new Mage_Core_Model_Observer();
        $observer->themeRegistration($eventObserver);

        $defaultModel = $this->_getThemeModel();
        $defaultModel->load('default/demo', 'theme_path');

        $iphoneModel = $this->_getThemeModel();
        $iphoneModel->load('default/iphone', 'theme_path');

        $this->assertEquals('Magento Demo', $defaultModel->getThemeTitle());
        $this->assertEquals(null, $defaultModel->getParentId());

        $this->assertEquals('Magento Iphone', $iphoneModel->getThemeTitle());
        $this->assertEquals($defaultModel->getId(), $iphoneModel->getParentId());
    }

    /**
     * Get theme model
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _getThemeModel()
    {
        return Mage::getModel('Mage_Core_Model_Theme');
    }

    /**
     * Create event observer for theme registration
     *
     * @return Varien_Event_Observer
     */
    protected function _createEventObserverForThemeRegistration()
    {
        $response = new Varien_Object(array('additional_options' => array()));
        $event = new Varien_Event(array('response_object' => $response));
        return new Varien_Event_Observer(array('event' => $event));
    }
}
