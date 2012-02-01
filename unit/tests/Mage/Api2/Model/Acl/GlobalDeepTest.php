<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 ACL Global model
 */
class Mage_Api2_Model_Acl_GlobalDeepTest extends Mage_PHPUnit_TestCase
{
    /**#@+
     * Test values
     */
    const ROLE_VALID        = 'role_valid';
    const RESOURCE_VALID    = 'resource_valid';
    const OPERATION_ALLOWED = 'operation_allowed';
    const OPERATION_DENIED  = 'operation_denied';
    const OPERATION_INVALID = 'operation_invalid';
    /**#@- */

    /**
     * @var Mage_Api2_Model_Acl_Global
     */
    protected $_aclGlobal;

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

        $this->_aclGlobal   = Mage::getModel('api2/acl_global');
        $this->_apiUserMock = $this->getMockForAbstractClass(
            'Mage_Api2_Model_Auth_User_Abstract', array(), '', true, true, true, array('getRole')
        );

        /** @var $acl Mage_Api2_Model_Acl */
        $acl = Mage::getSingleton('api2/acl');

        $acl->addRole(self::ROLE_VALID)
            ->addResource(self::RESOURCE_VALID)
            ->allow(self::ROLE_VALID, self::RESOURCE_VALID, self::OPERATION_ALLOWED)
            ->deny(self::ROLE_VALID, self::RESOURCE_VALID, self::OPERATION_DENIED);
    }

    /**
     * Test isAllowed() method
     */
    public function testIsAllowed()
    {
        $this->_apiUserMock->expects($this->any())
            ->method('getRole')
            ->will($this->returnValue(self::ROLE_VALID));

        $this->assertTrue(
            $this->_aclGlobal->isAllowed($this->_apiUserMock, self::RESOURCE_VALID, self::OPERATION_ALLOWED)
        );
        $this->assertFalse(
            $this->_aclGlobal->isAllowed($this->_apiUserMock, self::RESOURCE_VALID, self::OPERATION_DENIED)
        );
        $this->assertFalse(
            $this->_aclGlobal->isAllowed($this->_apiUserMock, self::RESOURCE_VALID, self::OPERATION_INVALID)
        );
    }
}
