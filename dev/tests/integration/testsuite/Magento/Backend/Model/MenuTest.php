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
 * Test class for \Magento\Backend\Model\Auth.
 *
 * @magentoAppArea adminhtml
 */
namespace Magento\Backend\Model;

class MenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        \Mage::app()->loadArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_model = \Mage::getModel('Magento\Backend\Model\Auth');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
    }

    public function testMenuItemManipulation()
    {
        /* @var $menu \Magento\Backend\Model\Menu */
        $menu = \Mage::getSingleton('Magento\Backend\Model\Menu\Config')->getMenu();
        /* @var $itemFactory \Magento\Backend\Model\Menu\Item\Factory */
        $itemFactory = \Mage::getModel('Magento\Backend\Model\Menu\Item\Factory');

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
