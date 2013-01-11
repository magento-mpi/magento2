<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Block_Role_Tab_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Block_Role_Tab_Edit
     */
    protected $_block;

    /**
     * Mage_User_Model_Role
     *
     * @magentoAppIsolation enabled
     */
    public function setUp()
    {
        Mage::getConfig()->setCurrentAreaCode('adminhtml');

        $roleAdmin = Mage::getModel('Mage_User_Model_Role');
        $roleAdmin->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        Mage::app()->getRequest()->setParam('rid', $roleAdmin->getId());

        $aclMock = $this->getMock('Magento_Acl', array(), array(), '', false);
        $aclMock->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));

        $this->_block = Mage::getObjectManager()->create(
            'Mage_User_Block_Role_Tab_Edit', array('data' => array('acl' => $aclMock)));
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->_block->getSelectedResources());
        $this->assertContains(
            Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL,
            $this->_block->getSelectedResources()
        );
    }

    public function testGetResTreeJson()
    {
        $encodedTree = $this->_block->getResTreeJson();
        $this->assertNotEmpty($encodedTree);

        $decodedTree = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($encodedTree);
        $this->assertNotEmpty($decodedTree);
    }
}
