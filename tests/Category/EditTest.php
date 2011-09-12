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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Edit Category
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Category_EditTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Categories</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Adding products to category</p>
     * <p>Steps</p>
     * <p>1. Create product.</p>
     * <p>2. Create category.</p>
     * <p>3. Save category.</p>
     * <p>4. Open newly created category.</p>
     * <p>5. Add products to category.</p>
     * <p>6. Save category.</p>
     * <p>Expected Result:</p>
     * <p>Product is added to category successfully.</p>
     *
     * @test
     */
    public function addProductToCategory()
    {
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $this->navigate('manage_categories');
        $this->assertTrue($this->checkCurrentPage('manage_categories'), 'Wrong page is opened');
        $categoryData = $this->loadData('root_category', null, 'name');
        $this->categoryHelper()->createRootCategory($categoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        $this->clickButton('reset', false);
        $this->categoryHelper()->selectCategory($categoryData['name']);
        $category_products_data['category_products_data'] = array('product_1' => array(
                'category_products_search_category_products_sku' => $productData['general_sku']));
        $this->categoryHelper()->fillCategoryInfo($category_products_data);
        $this->saveForm('save_category');
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
    }
}
