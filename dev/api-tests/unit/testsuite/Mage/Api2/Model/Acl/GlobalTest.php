<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Api2 ACL Global model
 */
class Mage_Api2_Model_Acl_GlobalTest extends Mage_PHPUnit_TestCase
{
    /**#@+
     * Test values
     */
    const ROLE_VALID        = 'role_valid';
    const ROLE_INVALID      = 'role_invalid';
    const ROLE_IS_ABSENT    = null;
    const RESOURCE_VALID    = 'resource_valid';
    const RESOURCE_INVALID  = 'resource_invalid';
    const OPERATION_ALLOWED = 'operation_allowed';
    /**#@-*/

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclGlobal;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_apiUserMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_aclGlobal   = Mage::getModel('Mage_Api2_Model_Acl_Global');
        $this->_apiUserMock = $this->getMockForAbstractClass(
            'Mage_Api2_Model_Auth_User_Abstract', array(), '', true, true, true, array('getRole')
        );

        $this->_aclMock = $this->getModelMockBuilder('Mage_Api2_Model_Acl')
            ->disableOriginalConstructor()
            ->setMethods(array('has', 'hasRole', 'isAllowed'))
            ->getMock();
    }

    /**
     * Test isAllowed() method
     */
    public function testIsAllowed()
    {
        $this->_apiUserMock->expects($this->any())
            ->method('getRole')
            ->will($this->returnValue(self::ROLE_VALID));

        $this->_aclMock->expects($this->once())
            ->method('hasRole')
            ->with(self::ROLE_VALID)
            ->will($this->returnValue(true));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with(self::RESOURCE_VALID)
            ->will($this->returnValue(true));

        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with(self::ROLE_VALID, self::RESOURCE_VALID, self::OPERATION_ALLOWED)
            ->will($this->returnValue(true));

        $this->assertTrue(
            $this->_aclGlobal->isAllowed($this->_apiUserMock, self::RESOURCE_VALID, self::OPERATION_ALLOWED)
        );
    }

    /**
     * Test isAllowed() method
     */
    public function testIsAllowedResourceNotFound()
    {
        $this->_apiUserMock->expects($this->exactly(2))
            ->method('getRole')
            ->will($this->returnValue(self::ROLE_VALID));

        $this->_aclMock->expects($this->once())
            ->method('hasRole')
            ->with(self::ROLE_VALID)
            ->will($this->returnValue(true));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with(self::RESOURCE_INVALID)
            ->will($this->returnValue(false));

        $this->setExpectedException(
            'Mage_Api2_Exception', 'Resource not found', Mage_Api2_Controller_Front_Rest::HTTP_NOT_FOUND
        );

        $this->_aclGlobal->isAllowed($this->_apiUserMock, self::RESOURCE_INVALID, 'any_operation');
    }

    /**
     * Test isAllowed() method
     */
    public function testIsAllowedRoleNotFound()
    {
        $this->_apiUserMock->expects($this->atLeastOnce())
            ->method('getRole')
            ->will($this->returnValue(self::ROLE_INVALID));

        $this->_aclMock->expects($this->once())
            ->method('hasRole')
            ->with(self::ROLE_INVALID)
            ->will($this->returnValue(false));

        $this->setExpectedException('Mage_Api2_Exception', 'Role not found', Mage_Api2_Controller_Front_Rest::HTTP_UNAUTHORIZED);

        $this->_aclGlobal->isAllowed($this->_apiUserMock, 'any_resource', 'any_operation');
    }

    /**
     * Test isAllowed() method
     */
    public function testIsAllowedUserWithoutRole()
    {
        $this->_apiUserMock->expects($this->once())
            ->method('getRole')
            ->will($this->returnValue(self::ROLE_IS_ABSENT));

        $this->assertTrue($this->_aclGlobal->isAllowed($this->_apiUserMock, 'any_resource', 'any_operation'));
    }
}
