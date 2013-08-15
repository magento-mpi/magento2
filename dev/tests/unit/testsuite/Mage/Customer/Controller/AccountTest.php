<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test customer account controller
 */
class Mage_Customer_Controller_AccountTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of actions that are allowed for not authorized users
     *
     * @var array
     */
    protected $_openActions = array(
        'create',
        'login',
        'logoutsuccess',
        'forgotpassword',
        'forgotpasswordpost',
        'resetpassword',
        'resetpasswordpost',
        'confirm',
        'confirmation',
        'createpassword',
        'createpost',
        'loginpost'
    );

    /**
     * @var Mage_Customer_Controller_Account
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments('Mage_Customer_Controller_Account');
        $this->_model = $objectManagerHelper->getObject('Mage_Customer_Controller_Account', $constructArguments);
    }

    /**
     * @covers Mage_Customer_Controller_Account::_getAllowedActions
     */
    public function testGetAllowedActions()
    {
        $this->assertAttributeEquals($this->_openActions, '_openActions', $this->_model);

        $method = new ReflectionMethod('Mage_Customer_Controller_Account', '_getAllowedActions');
        $method->setAccessible(true);
        $this->assertEquals($this->_openActions, $method->invoke($this->_model));
    }
}
