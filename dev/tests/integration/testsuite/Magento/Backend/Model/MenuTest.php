<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Backend_Model_Auth.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_MenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Menu
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        Mage::app()->loadArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_model = Mage::getModel('Magento_Backend_Model_Auth');
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
    }

    public function testMenuItemManipulation()
    {
        /* @var $menu Magento_Backend_Model_Menu */
        $menu = Mage::getSingleton('Magento_Backend_Model_Menu_Config')->getMenu();
        /* @var $itemFactory Magento_Backend_Model_Menu_Item_Factory */
        $itemFactory = Mage::getModel('Magento_Backend_Model_Menu_Item_Factory');

        // Add new item in top level
        $menu->add($itemFactory->create(array(
            'id' => 'Magento_Backend::system2',
            'title' => 'Extended System',
            'module' => 'Magento_Backend',
            'resource' => 'Magento_Backend::system2'
        )));

         //Add submenu
        $menu->add($itemFactory->create(array(
            'id' => 'Magento_Backend::system2_acl',
            'title' => 'Acl',
            'module' => 'Magento_Backend',
            'action' => 'admin/backend/acl/index',
            'resource' => 'Magento_Backend::system2_acl',
        )), 'Magento_Backend::system2');

        // Modify existing menu item
        $menu->get('Magento_Backend::system2')->setTitle('Base system')
            ->setAction('admin/backend/system/base'); // remove dependency from config

        // Change sort order
        $menu->reorder('Magento_Backend::system', 40);

        // Remove menu item
        $menu->remove('Magento_Backend::catalog_attribute');

        // Move menu item
        $menu->move('Magento_Catalog::catalog_products', 'Magento_Backend::system2');
    }
}
