<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Block_Adminhtml_Role_Edit block
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs
     */
    protected $_block = null;

    /**
     * Initialize block
     */
    protected function setUp()
    {
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $layout->getUpdate()->load('adminhtml_webapi_role_edit');
        $layout->generateXml()->generateElements();
        $this->_block = $layout->createBlock('Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs');
    }

    /**
     * Clear clock
     */
    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * Test setApiRole
     */
    public function testSetApiRole()
    {
        $apiRole = new Varien_Object(array('id' => 1, 'role_name' => 'test role'));
        $this->_block->setApiRole($apiRole);
        $this->_block->toHtml();

        $layout = $this->_block->getLayout();

        $this->assertEquals($apiRole, $layout->getBlock('webapi.role.edit.tab.main')->getApiRole());
        $this->assertEquals($apiRole, $layout->getBlock('webapi.role.edit.tab.resource')->getApiRole());
    }
}
