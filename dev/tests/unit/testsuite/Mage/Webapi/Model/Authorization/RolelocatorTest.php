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

class Mage_Webapi_Model_Authorization_RoleLocatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test for Mage_Backend_Model_Authorization_RoleLocator::getAclRoleId()
     *
     */
    public function testGetAclRoleId()
    {
        /** @var $sessionMock Mage_Core_Model_Session */
        $sessionMock = $this->getMock('Mage_Core_Model_Session',
            array('getData', 'hasData'), array(), '', false);
        $model = new Mage_Backend_Model_Authorization_RoleLocator(array('session' => $sessionMock));
        $sessionMock->expects($this->any())
            ->method('hasData')
            ->with('webapi_user')
            ->will($this->returnValue(true));
        $sessionMock->expects($this->any())
            ->method('getData')
            ->with('webapi_user')
            ->will($this->returnValue(new Varien_Object(array(
                'role_id' => 225
            ))));
        $this->assertEquals(255, $model->getAclRoleId());
    }
}
