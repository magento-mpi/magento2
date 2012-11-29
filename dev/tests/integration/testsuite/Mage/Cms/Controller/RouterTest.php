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

class Mage_Cms_Controller_RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Cms_Controller_Router
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Cms_Controller_Router(
            Mage::getObjectManager()->get('Mage_Core_Controller_Varien_Action_Factory'),
            new Mage_Core_Model_Event_ManagerStub()
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testMatch()
    {
        $request = new Mage_Core_Controller_Request_Http();
        //Open Node
        $request->setPathInfo('parent_node');
        $controller = $this->_model->match($request);
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Action_Redirect', $controller);
    }
}

/**
 * Event manager stub
 */
class Mage_Core_Model_Event_ManagerStub extends Mage_Core_Model_Event_Manager
{
    /**
     * Stub dispatch event
     *
     * @param string $eventName
     * @param array $params
     * @return Mage_Core_Model_App|null
     */
    public function dispatch($eventName, $params)
    {
        switch ($eventName) {
            case 'cms_controller_router_match_before' :
                $params['condition']->setRedirectUrl('http://www.example.com/');
                break;
        }

        return null;
    }
}
