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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Category_RenameTest extends Mage_Selenium_TestCase
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
        $this->navigate('manage_categories');
        $this->assertTrue($this->checkCurrentPage('manage_categories'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Rename root category.</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>4. Open newly created category.</p>
     * <p>5. Rename category.</p>
     * <p>Expected Result:</p>
     * <p>Root Category is renamed.</p>
     *
     * @test
     */
    public function renameRootCategory()
    {
        $categoryData = $this->loadData('root_category', null, 'name');
        $this->categoryHelper()->createRootCategory($categoryData);
        $this->pleaseWait();
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        $this->clickButton('reset', false);
        $this->categoryHelper()->selectCategory($categoryData['name']);
        $categoryData = $this->loadData('root_category_rename', null, 'name');
        $this->categoryHelper()->fillCategoryInfo($categoryData);
        $this->saveForm('save_category');
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
    }

    /**
     * <p>Rename subcategory.</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>4. Choose newly created root category</p>
     * <p>5. Create subcategory for newly created root category.</p>
     * <p>6. Open newly created subcategory.</p>
     * <p>7. Rename subcategory.</p>
     * <p>Expected Result:</p>
     * <p>Subategory is renamed.</p>
     *
     * @test
     */
    public function renameSubCategory()
    {
        $categoryRoot = $this->loadData('root_category', null, 'name');
        $this->categoryHelper()->createRootCategory($categoryRoot);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        $this->clickButton('reset', false);
        $categorySub = $this->loadData('sub_category_required', null, 'name');
        $this->categoryHelper()->createSubCategory($categoryRoot['name'], $categorySub);
        $this->pleaseWait();
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        $categoryData = $this->loadData('sub_category_required_rename', null, 'name');
        $this->categoryHelper()->fillCategoryInfo($categoryData);
        $this->clickButton('save_category', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
    }
}
