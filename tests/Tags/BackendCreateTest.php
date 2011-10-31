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
 * Attribute Set creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tags_BackendCreateTest extends Mage_Selenium_TestCase
{

    private static $tagToBeDeleted = array();

    /**
     * <p>Create a simple product for tests</p>
     *
     */
    private function createSimpleProduct()
    {
        $simpleProduct = $this->loadData('simple_product_visible', null, array('general_name','general_sku'));
        $this->navigate('manage_products');
//        $simpleProductData = $this->loadData('simple_product_for_prices_validation_front_1',
//                array('categories' => $category), array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        return $simpleProduct['general_name'];
    }

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Tags -> All tags</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('all_tags');
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->messages);
        $this->addParameter('storeId', '1');
    }

    /**
     * <p>Creating a new tag</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tag"</p>
     * <p>2. Fill in the fields in General Information</p>
     * <p>3. Click button "Save and Continue Edit"</p>
     * <p>4. Click button "Save Tag"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the tag has been saved.</p>
     *
     * @test
     */
    public function createNew()
    {
        //Setup
        $setData = $this->loadData('backend_new_tag', null, 'tag_name');
        //Steps
        $this->tagsHelper()->addTag($setData);
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_tag'), $this->messages);
        //Cleanup
        self::$tagToBeDeleted = array('tag_name' => $setData['tag_name']);
    }

    /**
     * <p>Creating a tag and assign it to a product as administrator</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tag"</p>
     * <p>2. Fill in the fields in General Information</p>
     * <p>3. Click button "Save and Continue Edit"</p>
     * <p>4. Fill in Products Tagged by Administrators</p>
     * <p>5. Click button "Save Tag"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the tag has been saved.</p>
     * <p>Steps:</p>
     * <p>6. Go to the product settings</p>
     * <p>7. Open Product Tags tab</p>
     * <p>Expected result:</p>
     * <p>The assigned tag is displayed.</p>
     *
     * @test
     */
    public function productTaggedByAdministrator()
    {
        //Setup
        $productName = $this->createSimpleProduct();
        $setData = $this->loadData('backend_new_tag_with_product', null, 'tag_name');
        $setData['products_tagged_by_admins']['prod_tag_admin_name'] = $productName;
        //Steps
        $this->navigate('all_tags');
        $this->tagsHelper()->addTag($setData);
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_tag'), $this->messages);
        $tagSearchData = array('tag_name' => $setData['tag_name']);
        $productSearchData = array('general_name' => $productName);
        $this->assertTrue($this->tagsHelper()->verifyTagProduct($tagSearchData, $productSearchData), $this->messages);
        //Cleanup
        self::$tagToBeDeleted = array('tag_name' => $setData['tag_name']);
    }

    protected function tearDown()
    {
        if (!empty(self::$tagToBeDeleted)) {
            $this->tagsHelper()->deleteTag(self::$tagToBeDeleted);
            self::$tagToBeDeleted = array();
        }
    }

}
