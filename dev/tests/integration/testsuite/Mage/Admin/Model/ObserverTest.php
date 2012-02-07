<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Admin
 */
class Mage_Admin_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Admin_Model_Observer
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Admin_Model_Observer();
    }

    public function testActionPreDispatchAdmin()
    {
        $request = Mage::app()->getRequest();
        $this->assertEmpty($request->getRouteName());
        $this->assertEmpty($request->getControllerName());
        $this->assertEmpty($request->getActionName());

        $observer = new Varien_Event_Observer();
        $this->_model->actionPreDispatchAdmin($observer);

        $this->assertEquals('adminhtml', $request->getRouteName());
        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('login', $request->getActionName());
    }
}
