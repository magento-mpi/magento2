<?php

class Mage_User_Block_Tab_RoleseditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Block_Tab_Rolesedit
     */
    protected $_block;

    public function setUp()
    {
        $roleAdmin = new Mage_User_Model_Role();
        $roleAdmin->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        Mage::app()->getRequest()->setParam('rid', $roleAdmin->getId());

        $this->_block = new Mage_User_Block_Tab_Rolesedit();
    }

    public function testConstructor()
    {
        $this->assertNotEmpty($this->_block->getSelectedResources());
        $this->assertContains('all', $this->_block->getSelectedResources());
    }

    public function testGetResTreeJson()
    {
        $encodedTree = $this->_block->getResTreeJson();
        $this->assertNotEmpty($encodedTree);

        $decodedTree = Mage::helper('Mage_Core_Helper_Data')->jsonDecode($encodedTree);
        $this->assertNotEmpty($decodedTree);
    }
}
