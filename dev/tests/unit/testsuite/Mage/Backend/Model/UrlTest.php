<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Model_Url
 */
class Mage_Backend_Model_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url
     */
    protected  $_model;

    public function setUp()
    {
        $fileName = __DIR__ . '/_files/adminhtml.xml';
        $config = new Varien_Simplexml_Config($fileName);

        $adminConfig = $this->getMock('Mage_Admin_Model_Config', array(), array(), '', false);

        $adminConfig->expects($this->any())
            ->method('getAdminhtmlConfig')
            ->will($this->returnValue($config));

        $this->_model = new Mage_Backend_Model_Url(array(
                'adminConfig' => $adminConfig,
                'startupPageUrl' => 'system/acl/roles',
            )
        );
    }

    public function testFindFirstAvailableMenuDenied()
    {
        /**
         * Test to find denied action
         */
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $user->expects($this->once())
            ->method('setHasAvailableResources')
            ->with($this->equalTo(false));

        $mockSession = $this->getMock('Mage_Backend_Model_Auth_Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );
        $mockSession->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $mockSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->_model->setSession($mockSession);
        $this->assertEquals('*/*/denied', $this->_model->findFirstAvailableMenu());
    }

    public function testFindFirstAvailableMenu()
    {
        /**
         * Test to find first available menu path
         */
        $user = $this->getMock('Mage_User_Model_User', array(), array(), '', false);
        $mockSession = $this->getMock('Mage_Backend_Model_Auth_Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );
        $mockSession->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $mockSession->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->_model->setSession($mockSession);

        $this->assertEquals('adminhtml/user', $this->_model->findFirstAvailableMenu());
    }

    public function testGetStartupPageUrl()
    {
        $mockSession = $this->getMock('Mage_Backend_Model_Auth_Session',
            array('getUser', 'isAllowed'),
            array(),
            '',
            false
        );
        $mockSession->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(true));
        $this->_model->setSession($mockSession);
        $this->assertEquals('adminhtml/user_role', (string)$this->_model->getStartupPageUrl());
    }
}
