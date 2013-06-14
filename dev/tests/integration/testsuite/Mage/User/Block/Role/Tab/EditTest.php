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

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Block_Role_Tab_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Block_Role_Tab_Edit
     */
    protected $_block;

    public function setUp()
    {
        $roleAdmin = Mage::getModel('Mage_User_Model_Role');
        $roleAdmin->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        Mage::app()->getRequest()->setParam('rid', $roleAdmin->getId());

        $this->_block = Mage::getObjectManager()->create('Mage_User_Block_Role_Tab_Edit');
    }

    public function testConstructor()
    {
        $this->assertNotEmpty($this->_block->getSelectedResources());
        $this->assertContains(
            'Mage_Adminhtml::all',
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
