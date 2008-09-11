<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

/**
 * WebServices Category test case
 */
class WebService_Catalog_CategoryTest extends WebService_TestCase_Abstract
{
    /**
     * catalog_category.currentStore
     *
     * @dataProvider connectorProvider
     */
    public function testCurrentStore(WebService_Connector_Interface $connector)
    {
        // get current store
        $currentStoreId = (int)$connector->call('catalog_category.currentStore');

        // create a store
        Mage::register('isSecureArea', true, true);
        $storeCode = 'c' . uniqid();
        $store = Mage::getModel('core/store')
            ->setData(array (
                'name'       => __CLASS__,
                'code'       => $storeCode,
                'is_active'  => 1,
                'sort_order' => 0,
                'is_default' => '',
                'website_id' => 0,
                'group_id'   => 0,
                'store_id'   => 0,
            ))
            ->setId(null)
            ->save();

        // check if we can set store by code
        $this->assertEquals((int)$store->getId(), (int)$connector->call('catalog_category.currentStore', $storeCode));

        // set store back
        $connector->call('catalog_category.currentStore', $currentStoreId);

        // delete the store
        $store->delete();
    }

    /**
     * catalog_category.create
     *
     * @dataProvider connectorProvider
     */
    public function testCreate(WebService_Connector_Interface $connector)
    {
        // create a category via API
        $newCategoryId = $connector->call('catalog_category.create', array(
            Mage_Catalog_Model_Category::TREE_ROOT_ID,
            array('name' => 'New Category Through Soap', 'is_active' => 1, 'is_anchor' => 1)
        ));

        // delete created category manually
        $this->_deleteCategoryById($newCategoryId);
    }

    /**
     * catalog_category.delete
     *
     * @dataProvider connectorProvider
     */
    public function testDelete(WebService_Connector_Interface $connector)
    {
        // create a category manually
        $category = $this->_createCategory();

        // delete the category via API
        $connector->call('catalog_category.delete', $category->getId());
    }

    /**
     * catalog_category.info
     *
     * @dataProvider connectorProvider
     */
    public function testInfo(WebService_Connector_Interface $connector)
    {
        // create a category manually
        $category = $this->_createCategory();

        // load it via API
        $categoryRemote = $connector->call('catalog_category.info', array($category->getId(), $category->getStoreId(), array('category_id', 'name', 'url_key')));
        $this->assertTrue(
            is_array($categoryRemote)
            && isset($categoryRemote['category_id'])
            && isset($categoryRemote['name'])
            && isset($categoryRemote['url_key']),
            'Failed to load appropriate category structure.'
        );
        $this->assertEquals($category->getId(), $categoryRemote['category_id'], 'Failed to verify category ID.');
        $this->assertEquals($category->getName(), $categoryRemote['name'], 'Failed to verify category name.');
        $this->assertEquals($category->getUrlKey(), $categoryRemote['url_key'], 'Failed to verify category URL.');

        // delete it manually
        Mage::register('isSecureArea', true, true);
        $category->delete();
    }

    /**
     * catalog_category.level
     *
     * @dataProvider connectorProvider
     */
    public function testLevel(WebService_Connector_Interface $connector)
    {
        // create a categories tree
        $tree = $this->_createCategoriesPlainTree();

        // test root level - see if our root category will be loaded
        $level = $connector->call('catalog_category.level');
        $this->assertTrue(is_array($level));
        $this->assertTrue(count($level) > 0);
        $isFound = false;
        foreach ($level as $category) {
            $this->assertTrue(
                is_array($category)
                && isset($category['category_id'])
            );
            if ($category['category_id'] == $tree[0]->getId()) {
                $isFound = true;
                break;
            }
        }
        $this->assertTrue($isFound);
        $this->assertEquals(Mage_Catalog_Model_Category::TREE_ROOT_ID, (int)$category['parent_id'],
            'Parent id of category level 1 is not correct.'
        );

        // get c1 and c2 and check if they are, and in appropriate order
        $level = $connector->call('catalog_category.level', array(null, 0, $tree[0]->getId()));
        $this->assertTrue(2 === count($level));
        $this->assertTrue(
            $level[0]['category_id'] == $tree[1]->getId()
            && $level[1]['category_id'] == $tree[2]->getId(),
            'Categories of level 2 have been loaded in wrong order, or wrong categories have been loaded.'
        );

        // get c11 and c12, perform the same check
        $level = $connector->call('catalog_category.level', array(null, 0, $tree[1]->getId()));
        $this->assertTrue(2 === count($level));
        $this->assertTrue(
            $level[0]['category_id'] == $tree[11]->getId()
            && $level[1]['category_id'] == $tree[12]->getId(),
            'Categories of level 3 have been loaded in wrong order, or wrong categories have been loaded.'
        );

        // to test root categories of a website:
        /**
         * 1) create website
         * 2) create store group, assigned to the tree; create store
         * 3) perform tests
         * 4) delete the website / store group / store
         */

        // delete the categories
        $this->_deleteCategories($tree);
    }

    /**
     * catalog_category.tree
     *
     * @dataProvider connectorProvider
     */
    public function testTree(WebService_Connector_Interface $connector)
    {
        $plainTree = $this->_createCategoriesPlainTree();

        // load and check created tree by id
        $tree = $connector->call('catalog_category.tree', array($plainTree[0]->getId()));
        $this->assertTrue(
            isset($tree['children'])
            && isset($tree['children'][0])
            && isset($tree['children'][1])
            && isset($tree['children'][0]['children'])
            && isset($tree['children'][1]['children'])
            && isset($tree['children'][0]['children'][0])
            && isset($tree['children'][0]['children'][1])
            && isset($tree['children'][1]['children'][0])
        );
        $this->assertTrue($this->_compareArrayToCategory($tree,                               $plainTree[0]));
        $this->assertTrue($this->_compareArrayToCategory($tree['children'][0],                $plainTree[1]));
        $this->assertTrue($this->_compareArrayToCategory($tree['children'][0]['children'][0], $plainTree[11]));
        $this->assertTrue($this->_compareArrayToCategory($tree['children'][0]['children'][1], $plainTree[12]));
        $this->assertTrue($this->_compareArrayToCategory($tree['children'][1],                $plainTree[2]));
        $this->assertTrue($this->_compareArrayToCategory($tree['children'][1]['children'][0], $plainTree[21]));

        // to test with store id, it is required a website / store group / store, assigned to the category tree

        $this->_deleteCategories($plainTree);
    }

    /**
     * catalog_category.update
     *
     * @dataProvider connectorProvider
     */
    public function testUpdate(WebService_Connector_Interface $connector)
    {
        $category  = $this->_createCategory();
        $newUrlKey = md5(uniqid());
        $newName   = __CLASS__ . '_updated';

        $result = $connector->call('catalog_category.update', array(
            $category->getId(),
            array('url_key' => $newUrlKey, 'name' => $newName)
        ));
        $this->assertTrue($result);

        $category = Mage::getModel('catalog/category')->load($category->getId());
        $this->assertEquals($newUrlKey, $category->getUrlKey());
        $this->assertEquals($newName, $category->getName());

        Mage::register('isSecureArea', true, true);
        $category->delete();
    }

    /**
     * catalog_category.move
     *
     * @dataProvider connectorProvider
     */
    public function testMove(WebService_Connector_Interface $connector)
    {
        $this->markTestSkipped();
//catalog_category.move
//
//Move category in tree
//
//Return: boolean
//
//Arguments:
//
//      int $categoryId - category ID for moving
//      int $parentId - new category parent
//      int $afterId - category ID after what position it will be moved (optional)
    }

    /**
     * Create a category manually
     *
     * @param int $parentId
     * @return Mage_Catalog_Model_Category
     */
    private function _createCategory($parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID)
    {
        $c = Mage::getModel('catalog/category')
            ->setStoreId(0)
            ->addData(array (
                'name'          => __CLASS__ . uniqid(),
                'is_active'     => '1',
                'url_key'       => '',
                'description'   => '',
                'meta_title'    => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'display_mode'  => 'PRODUCTS',
                'landing_page'  => '',
                'is_anchor'     => '1',
                'custom_design' => '',
                'custom_design_apply'  => '1',
                'custom_design_from'   => '',
                'custom_design_to'     => '',
                'page_layout'          => '',
                'custom_layout_update' => '',
            ));
        $parent = Mage::getModel('catalog/category')->load($parentId);
        $c->setPath($parent->getPath())
          ->setAttributeSetId($c->getDefaultAttributeSetId())
          ->save();
        return $c;
    }

    /**
     * Delete category manually
     *
     * @param int $categoryId
     */
    private function _deleteCategoryById($categoryId)
    {
        Mage::register('isSecureArea', true, true);
        Mage::getModel('catalog/category')->load($categoryId)->delete();
    }

    /**
     * Create a plain array of categories, that actually are a tree in database
     * The tree:
     * c0
     *   - c1
     *     - c11
     *     - c12
     *   - c2
     *     - c21
     *
     * @return array
     */
    private function _createCategoriesPlainTree()
    {
        $tree = array(0 => $this->_createCategory());
        $tree[1]  = $this->_createCategory($tree[0]->getId());
        $tree[11] = $this->_createCategory($tree[1]->getId());
        $tree[12] = $this->_createCategory($tree[1]->getId());
        $tree[2]  = $this->_createCategory($tree[0]->getId());
        $tree[21] = $this->_createCategory($tree[2]->getId());
        return $tree;
    }

    /**
     * Delete specified array of categories
     * Array must contain categories objects
     *
     * @param array $categories
     */
    private function _deleteCategories($categories)
    {
        Mage::register('isSecureArea', true, true);
        foreach ($categories as $category) {
            $category->delete();
        }
    }

    /**
     * Check if category-array, given by .level or .tree conforms category object
     *
     * @param array $array
     * @param Mage_Catalog_Model_Category $category
     * @return unknown
     */
    private function _compareArrayToCategory(array $array, Mage_Catalog_Model_Category $category)
    {
        return (
            isset($array['category_id'])
            && isset($array['parent_id'])
            && isset($array['name'])
            && isset($array['is_active'])
            && isset($array['position'])
            && isset($array['level'])
            && $array['category_id'] == $category->getId()
            && $array['parent_id']   == $category->getParentId()
            && $array['name']        == $category->getName()
            && $array['is_active']   == $category->getIsActive()
            && $array['position']    == $category->getPosition()
            && $array['level']       == $category->getLevel()
        );
    }
}
