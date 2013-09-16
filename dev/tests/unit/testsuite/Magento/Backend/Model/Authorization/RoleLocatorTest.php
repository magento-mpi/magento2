<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Authorization_RoleLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Authorization_RoleLocator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock = array();

    protected function setUp()
    {
        $this->_sessionMock = $this->getMock(
            'Magento_Backend_Model_Auth_Session',
            array('getUser', 'getAclRole', 'hasUser'),
            array(),
            '',
            false
        );
        $this->_model = new Magento_Backend_Model_Authorization_RoleLocator($this->_sessionMock);
    }

    public function testGetAclRoleIdReturnsCurrentUserAclRoleId()
    {
        $this->_sessionMock->expects($this->once())->method('hasUser')->will($this->returnValue(true));
        $this->_sessionMock->expects($this->once())->method('getUser')->will($this->returnSelf());
        $this->_sessionMock->expects($this->once())->method('getAclRole')->will($this->returnValue('some_role'));
        $this->assertEquals('some_role', $this->_model->getAclRoleId());
    }
}
