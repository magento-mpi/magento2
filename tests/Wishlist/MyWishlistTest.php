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
 * Wishlist tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Wishlist_MyWishlistTest extends Mage_Selenium_TestCase
{

    protected static $productToBeRemoved = array();
//    protected static $categoryName = null;

    /**
     * <p>Login as a registered user</p>
     */
    public function setUpBeforeTests()
    {
        $this->logoutCustomer();
    }

    /**
     * <p>Preconditions:</p>
     * <p>@TODO</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('');
        $this->assertTrue($this->checkCurrentPage(''), $this->messages);
        $this->addParameter('', '0');
    }

    /**
     * <p>Removes a product from My Wishlist</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Remove the product from the wishlist</p>
     * <p>Expected result:</p>
     * <p>The product is not in the wishlist</p>
     *
     * @test
     */
    public function removeProductFromWishlist($productData, $categoryPath)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Removes all products from My Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Add products to the wishlist</p>
     * <p>2. Remove all products from the wishlist</p>
     * <p>Expected result:</p>
     * <p>'You have no items in your wishlist.' is displayed</p>
     *
     * @test
     */
    public function removeAllProductsFromWishlist(array $productDataSet)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Shares My Wishlist</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter a valid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>The success message is displayed</p>
     *
     * @test
     */
    public function shareWishlist($shareData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Shares My Wishlist</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter an invalid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>An error message is displayed</p>
     *
     * @test
     */
    public function shareWishlistWithInvalidEmail($shareData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    protected function tearDown()
    {
        if (!empty(self::$productToBeRemoved)) {
            $this->wishlistHelper()->removeProduct(self::$productToBeRemoved);
            self::$productToBeRemoved = array();
        }
    }

}
