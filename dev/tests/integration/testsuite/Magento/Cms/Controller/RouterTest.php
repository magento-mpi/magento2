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

class Magento_Cms_Controller_RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cms_Controller_Router
     */
    protected $_model;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-3393');
        $this->_model = new Magento_Cms_Controller_Router(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->get('Magento_Core_Controller_Varien_Action_Factory'),
            new Magento_Core_Model_Event_ManagerStub(
                $this->getMockForAbstractClass('Magento_Core_Model_Event_InvokerInterface', array(), '', false),
                $this->getMock('Magento_Core_Model_Event_Config', array(), array(), '', false),
                $this->getMock('Magento_EventFactory', array(), array(), '', false),
                $this->getMock('Magento_Event_ObserverFactory', array(), array(), '', false)
            )
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testMatch()
    {
        $this->markTestIncomplete('MAGETWO-3393');
        $request = Mage::getObjectManager()->create('Magento_Core_Controller_Request_Http');
        //Open Node
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Controller_Response_Http')
            ->headersSentThrowsException = Mage::$headersSentThrowsException;
        $request->setPathInfo('parent_node');
        $controller = $this->_model->match($request);
        $this->assertInstanceOf('Magento_Core_Controller_Varien_Action_Redirect', $controller);
    }
}

/**
 * Event manager stub
 */
class Magento_Core_Model_Event_ManagerStub extends Magento_Core_Model_Event_Manager
{
    /**
     * Stub dispatch event
     *
     * @param string $eventName
     * @param array $params
     * @return Magento_Core_Model_App|null
     */
    public function dispatch($eventName, array $params = array())
    {
        switch ($eventName) {
            case 'cms_controller_router_match_before' :
                $params['condition']->setRedirectUrl('http://www.example.com/');
                break;
        }

        return null;
    }
}
