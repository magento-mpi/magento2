<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme observer
 */
class Magento_Core_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Event_Observer
     */
    protected $_eventObserver;

    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_eventObserver = $this->_createEventObserverForThemeRegistration();
    }

    /**
     * Theme registration test
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testThemeRegistration()
    {
        $baseDir = 'base_dir';
        $pattern = 'path_pattern';

        $this->_eventObserver->getEvent()->setBaseDir($baseDir);
        $this->_eventObserver->getEvent()->setPathPattern($pattern);

        $themeRegistration = $this->getMock(
            'Magento_Core_Model_Theme_Registration',
            array('register'),
            array(
                $this->_objectManager->create('Magento_Core_Model_Resource_Theme_CollectionFactory'),
                $this->_objectManager->create('Magento_Core_Model_Theme_Collection')
            )
        );
        $themeRegistration->expects($this->once())
            ->method('register')
            ->with($baseDir, $pattern);
        $this->_objectManager->addSharedInstance($themeRegistration, 'Magento_Core_Model_Theme_Registration');

        /** @var $observer Magento_Core_Model_Observer */
        $observer = $this->_objectManager->create('Magento_Core_Model_Observer');
        $observer->themeRegistration($this->_eventObserver);
    }

    /**
     * Create event observer for theme registration
     *
     * @return Magento_Event_Observer
     */
    protected function _createEventObserverForThemeRegistration()
    {
        $response = $this->_objectManager->create('Magento_Object', array(
            'data' => array('additional_options' => array())
        ));
        $event = $this->_objectManager->create('Magento_Event', array('data' => array('response_object' => $response)));
        return $this->_objectManager->create('Magento_Event_Observer', array('data' => array('event' => $event)));
    }
}
