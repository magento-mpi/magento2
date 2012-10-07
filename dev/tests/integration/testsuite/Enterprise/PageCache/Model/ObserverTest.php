<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_Observer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observer;

    /**
     * @var Enterprise_PageCache_Model_Cookie|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookie;

    protected function setUp()
    {
        Mage::app()->getCacheInstance()->allowUse('full_page');
        $this->_cookie = $this->getMock(
            'Enterprise_PageCache_Model_Cookie', array('set', 'delete', 'updateCustomerCookies')
        );
        $this->_observer = $this->getMock('Enterprise_PageCache_Model_Observer', array('_getCookie'));
        $this->_observer
            ->expects($this->any())
            ->method('_getCookie')
            ->will($this->returnValue($this->_cookie))
        ;
    }

    protected function tearDown()
    {
        $this->_cookie = null;
        $this->_observer = null;
    }

    public function testProcessPreDispatchCanProcessRequest()
    {
        $request = new Magento_Test_Request();
        $request->setRouteName('catalog');
        $request->setControllerName('product');
        $request->setActionName('view');
        $observerData = new Varien_Event_Observer();
        $observerData->setEvent(new Varien_Event(array(
            'controller_action' => new Mage_Core_Controller_Front_Action($request, new Magento_Test_Response())
        )));
        $this->_cookie
            ->expects($this->once())
            ->method('updateCustomerCookies')
        ;
        Mage::app()->getCacheInstance()->allowUse(Mage_Core_Block_Abstract::CACHE_GROUP);
        Mage::getSingleton('Mage_Catalog_Model_Session')->setParamsMemorizeDisabled(false);
        $this->_observer->processPreDispatch($observerData);
        $this->assertFalse(Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP));
        $this->assertTrue(Mage::getSingleton('Mage_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    public function testProcessPreDispatchCannotProcessRequest()
    {
        $request = new Magento_Test_Request();
        $request->setParam('no_cache', '1');
        $observerData = new Varien_Event_Observer();
        $observerData->setEvent(new Varien_Event(array(
            'controller_action' => new Mage_Core_Controller_Front_Action($request, new Magento_Test_Response())
        )));
        $this->_cookie
            ->expects($this->once())
            ->method('updateCustomerCookies')
        ;
        Mage::getSingleton('Mage_Catalog_Model_Session')->setParamsMemorizeDisabled(true);
        $this->_observer->processPreDispatch($observerData);
        $this->assertFalse(Mage::getSingleton('Mage_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    public function testDesignEditorSessionActivate()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('set')
            ->with(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE)
        ;
        $this->_observer->designEditorSessionActivate(new Varien_Event_Observer());
    }

    public function testDesignEditorSessionDeactivate()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('delete')
            ->with(Enterprise_PageCache_Model_Processor::NO_CACHE_COOKIE)
        ;
        $this->_observer->designEditorSessionDeactivate(new Varien_Event_Observer());
    }
}
