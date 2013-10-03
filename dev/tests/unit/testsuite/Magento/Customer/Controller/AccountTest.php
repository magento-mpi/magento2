<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test customer account controller
 */
namespace Magento\Customer\Controller;

class AccountTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Customer\Controller\Account
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $arguments = array(
            'urlFactory' => $this->getMock('Magento\Core\Model\UrlFactory', array(), array(), '', false),
            'customerFactory' => $this->getMock('Magento\Customer\Model\CustomerFactory', array(), array(), '', false),
            'formFactory' => $this->getMock('Magento\Customer\Model\FormFactory', array(), array(), '', false),
            'addressFactory' => $this->getMock('Magento\Customer\Model\AddressFactory', array(), array(), '', false),
        );
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Magento\Customer\Controller\Account',
            $arguments
        );
        $this->_model = $objectManagerHelper->getObject('Magento\Customer\Controller\Account', $constructArguments);
    }

    /**
     * @covers \Magento\Customer\Controller\Account::_getAllowedActions
     */
    public function testGetAllowedActions()
    {
        $this->assertAttributeEquals($this->_openActions, '_openActions', $this->_model);

        $method = new \ReflectionMethod('Magento\Customer\Controller\Account', '_getAllowedActions');
        $method->setAccessible(true);
        $this->assertEquals($this->_openActions, $method->invoke($this->_model));
    }
}
