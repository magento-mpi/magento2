<?php
/**
 * Test for Mage_Webapi_Block_Adminhtml_User_Edit_Form block
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Mage_Webapi_Block_Adminhtml_User_Edit_Tabs
     */
    protected $_block;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_layout = $this->_objectManager->get('Mage_Core_Model_Layout');
        $this->_blockFactory = $this->_objectManager->get('Mage_Core_Model_BlockFactory');
        $this->_block = $this->_layout->createBlock('Mage_Webapi_Block_Adminhtml_User_Edit_Tabs',
            'webapi.user.edit.tabs');
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Mage_Core_Model_Layout');
        unset($this->_objectManager, $this->_layout, $this->_block);
    }

    /**
     * Test _beforeToHtml method
     */
    public function testBeforeToHtml()
    {
        // TODO Move to unit tests after MAGETWO-4015 complete
        /** @var Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Main $mainTabBlock */
        $mainTabBlock = $this->_layout->addBlock(
            'Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Main',
            'webapi.user.edit.tab.main',
            'webapi.user.edit.tabs',
            'main'
        );

        /** @var Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Roles $rolesTabBlock */
        $rolesTabBlock = $this->_layout->addBlock(
            'Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Roles',
            'webapi.user.edit.tab.roles',
            'webapi.user.edit.tabs',
            'roles'
        );

        $apiUser = new Varien_Object();
        $this->_block->setApiUser($apiUser);
        $this->_block->toHtml();

        $this->assertSame($apiUser, $mainTabBlock->getApiUser());
        $this->assertSame($apiUser, $rolesTabBlock->getApiUser());
    }
}
