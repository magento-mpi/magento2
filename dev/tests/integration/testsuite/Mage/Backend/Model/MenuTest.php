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

    public function testMenuItemManipulation()
    {
        /* @var $menu Mage_Backend_Model_Menu */
        $menu = Mage::getSingleton('Mage_Backend_Model_Menu_Config')->getMenu();
        /* @var $itemFactory Mage_Backend_Model_Menu_Item_Factory */
        $itemFactory = Mage::getModel('Mage_Backend_Model_Menu_Item_Factory');

        // Add new item in top level
        $menu->addChild($itemFactory->createFromArray(array(
            'id' => 'Mage_Backend::system2',
            'title' => 'Extended System',
            'module' => 'Mage_Backend',
        )));

        // Add submenu
        $menu->getChildById('Mage_Backend::system2')->addChild($itemFactory->createFromArray(array(
            'id' => 'Mage_Backend::system2_acl',
            'title' => 'Acl',
            'module' => 'Mage_Backend',
            'action' => 'admin/backend/acl/index'
        )));

        // Modify existing menu item
        $menu->getChildById('system')->setTitle('Base system')
            ->setAction('admin/backends/system/base')
            ->setModuleDependency('Mage_User')
            ->setConfigDependency(null); // remove dependency from config

        // Change sort order
        $menu->moveChildById('Mage_Backend::system', 40);

        // Remove menu item
        $menu->removeChildById('Mage_Backend::catalog_attribute');

        // Move menu item
        $catalogProductItem = $menu->getChildById('Mage_Catalog::catalog_product');
        $catalogProductItem = $menu->removeChildById('Mage_Catalog::catalog_product');
        $menu->getChildById('Mage_Backend::system')->addChild($catalogProductItem);
    }
}
