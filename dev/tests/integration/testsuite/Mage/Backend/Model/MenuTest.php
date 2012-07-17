<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Model_Auth.
 *
 * @group module:Mage_Backend
 */
class Mage_Backend_Model_MenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Auth();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testMenuItemManipulation()
    {
        /* @var $menu Mage_Backend_Model_Menu */
        $menu = Mage::getSingleton('Mage_Backend_Model_Menu_Config')->getMenu();
        /* @var $itemFactory Mage_Backend_Model_Menu_Item_Factory */
        $itemFactory = Mage::getModel('Mage_Backend_Model_Menu_Item_Factory');

        // Add new item in top level
        $menu->add($itemFactory->createFromArray(array(
            'id' => 'Mage_Backend::system2',
            'title' => 'Extended System',
            'module' => 'Mage_Backend',
        )));

         //Add submenu
        $menu->add($itemFactory->createFromArray(array(
            'id' => 'Mage_Backend::system2_acl',
            'title' => 'Acl',
            'module' => 'Mage_Backend',
            'action' => 'admin/backend/acl/index'
        )), 'Mage_Backend::system2');

        // Modify existing menu item
        $menu->get('Mage_Backend::system2')->setTitle('Base system')
            ->setAction('admin/backend/system/base'); // remove dependency from config

        // Change sort order
        $menu->reorder('Mage_Backend::system', 40);

        // Remove menu item
        $menu->remove('Mage_Backend::catalog_attribute');

        // Move menu item
        $menu->move('Mage_Catalog::catalog_products', 'Mage_Backend::system2');
    }
}
