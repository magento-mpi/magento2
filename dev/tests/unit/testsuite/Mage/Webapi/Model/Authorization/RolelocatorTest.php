<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Webapi_Model_Authorization_RoleLocator
 */
class Mage_Webapi_Model_Authorization_RoleLocatorTest extends PHPUnit_Framework_TestCase
{

    /** @var Mage_Core_Model_Session */
    protected $_sessionMock;

    /** @var Mage_Backend_Model_Authorization_RoleLocator */
    protected $_model;

    public function setUp()
    {
        $this->_sessionMock = $this->getMock('Mage_Core_Model_Session',
            array('getData', 'hasData'), array(), '', false);
        $this->_model = new Mage_Webapi_Model_Authorization_RoleLocator(array('session' => $this->_sessionMock));
    }

    /**
     * Test for Mage_Backend_Model_Authorization_RoleLocator::getAclRoleId()
     */
    public function testGetAclRoleIdReturnNullOnWrongSession()
    {
        $this->_sessionMock->expects($this->once())->method('hasData')->will($this->returnValue(false));
        $this->assertNull($this->_model->getAclRoleId());
    }

    /**
     * Test for Mage_Backend_Model_Authorization_RoleLocator::getAclRoleId()
     */
    public function testGetAclRoleIdReturnNullOnBrokenSession()
    {
        $this->_sessionMock->expects($this->once())->method('hasData')->will($this->returnValue(true));
        $this->_sessionMock->expects($this->once())->method('getData')->will($this->returnValue(12));
        $this->assertNull($this->_model->getAclRoleId());
    }

    /**
     * Test for Mage_Backend_Model_Authorization_RoleLocator::getAclRoleId()
     */
    public function testGetAclRoleIdReturnRoleIdOnRightSession()
    {
        $user = new Varien_Object(array(
            'role_id' => 255
        ));
        $this->_sessionMock->expects($this->once())->method('hasData')->will($this->returnValue(true));
        $this->_sessionMock->expects($this->once())->method('getData')->will($this->returnValue($user));
        $this->assertEquals(255, $this->_model->getAclRoleId());
    }
}
