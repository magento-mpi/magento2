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
     * @var Enterprise_PageCache_Model_Observer
     */
    protected $_observer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookie;

    protected function setUp()
    {
        /** @var Mage_Core_Model_Cache_StateInterface $cacheState */
        $cacheState = Mage::getObjectManager()->get('Mage_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled('full_page', true);
        $this->_cookie = $this->getMock(
            'Enterprise_PageCache_Model_Cookie',
            array('set', 'delete', 'updateCustomerCookies'),
            array(),
            '',
            false,
            false
        );

        $this->_observer = Mage::getObjectManager()
            ->create('Enterprise_PageCache_Model_Observer', array('cookie' => $this->_cookie));
    }

    public function testProcessPreDispatchCanProcessRequest()
    {
        $request = new Magento_Test_Request();
        $response = new Magento_Test_Response();

        $request->setRouteName('catalog');
        $request->setControllerName('product');
        $request->setActionName('view');

        $observerData = new Magento_Event_Observer();
        $arguments = array('request' => $request, 'response' => $response);
        $context = Mage::getObjectManager()->create('Mage_Core_Controller_Varien_Action_Context', $arguments);
        $observerData->setEvent(new Magento_Event(array(
            'controller_action' => Mage::getModel(
                'Mage_Core_Controller_Front_Action',
                array('context' => $context)
            )
        )));

        $this->_cookie->expects($this->once())->method('updateCustomerCookies');

        /** @var $cacheState Mage_Core_Model_Cache_StateInterface */
        $cacheState = Mage::getObjectManager()->get('Mage_Core_Model_Cache_StateInterface');

        $cacheState->setEnabled(Mage_Core_Block_Abstract::CACHE_GROUP, true);

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        $session->setParamsMemorizeDisabled(false);

        $this->_observer->processPreDispatch($observerData);

        $this->assertFalse($cacheState->isEnabled(Mage_Core_Block_Abstract::CACHE_GROUP));
        $this->assertTrue(Mage::getSingleton('Mage_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessPreDispatchCannotProcessRequest()
    {
        /** @var $restriction Enterprise_PageCache_Model_Processor_RestrictionInterface */
        $restriction = Mage::getSingleton('Enterprise_PageCache_Model_Processor_RestrictionInterface');
        $restriction->setIsDenied();

        $observerData = new Magento_Event_Observer();
        $arguments = array('request' => new Magento_Test_Request(), 'response' => new Magento_Test_Response());
        $context = Mage::getObjectManager()->create('Mage_Core_Controller_Varien_Action_Context', $arguments);
        $observerData->setEvent(new Magento_Event(array(
            'controller_action' => Mage::getModel(
                'Mage_Core_Controller_Front_Action',
                array('context' => $context)
            )
        )));
        $this->_cookie
            ->expects($this->once())
            ->method('updateCustomerCookies');

        Mage::getSingleton('Mage_Catalog_Model_Session')->setParamsMemorizeDisabled(true);
        $this->_observer->processPreDispatch($observerData);
        $this->assertFalse(Mage::getSingleton('Mage_Catalog_Model_Session')->getParamsMemorizeDisabled());
    }

    public function testSetNoCacheCookie()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('set')
            ->with(Enterprise_PageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
        ;
        $this->_observer->setNoCacheCookie(new Magento_Event_Observer());
    }

    public function testDeleteNoCacheCookie()
    {
        $this->_cookie
            ->expects($this->once())
            ->method('delete')
            ->with(Enterprise_PageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
        ;
        $this->_observer->deleteNoCacheCookie(new Magento_Event_Observer());
    }
}
