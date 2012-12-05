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
     * @magentoAppIsolation enabled
     */
    public function testThemeRegistration()
    {
        $baseDir = 'base_dir';
        $pattern = 'path_pattern';

        $eventObserver = $this->_createEventObserverForThemeRegistration();
        $eventObserver->getEvent()->setBaseDir($baseDir);
        $eventObserver->getEvent()->setPathPattern($pattern);

        /** @var $objectManager Magento_Test_ObjectManager */
        $objectManager = Mage::getObjectManager();
        $themeRegistration = $this->getMock('Mage_Core_Model_Theme_Registration', array('register'));
        $themeRegistration->expects($this->any())
            ->method('register')
            ->with($baseDir, $pattern);
        $objectManager->addSharedInstance($themeRegistration, 'Mage_Core_Model_Theme_Registration');

        /** @var $observer Mage_Core_Model_Observer */
        $observer = Mage::getModel('Mage_Core_Model_Observer');
        $observer->themeRegistration($eventObserver);
    }

    /**
     * Get theme model
     *
     * @return Mage_Core_Model_Theme
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
        $response = Mage::getModel('Varien_Object', array('additional_options' => array()));
        $event = Mage::getModel('Varien_Event', array('response_object' => $response));
        return Mage::getModel('Varien_Event_Observer', array('event' => $event));
    }
}
