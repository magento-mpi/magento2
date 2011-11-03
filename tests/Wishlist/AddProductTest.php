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
class Wishlist_AddProductTest extends Mage_Selenium_TestCase
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
     * <p>Remove all products from My Wishlist</p>
     * <p>@TODO</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('');
        $this->assertTrue($this->checkCurrentPage(''), $this->messages);
        $this->addParameter('', '0');
    }

    /**
     * <p>Adds a product to Wishlist from Product Details page. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @test
     */
    public function addProductToWishlistFromProductPage($productData, $categoryPath)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Adds a simple product to Wishlist from Catalog page.</p>
     * <p>Steps:</p>
     * <p>1. Open category</p>
     * <p>2. Find product</p>
     * <p>3. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @test
     */
    public function addProductToWishlistFromCatalog($productData, $categoryPath)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Adds a simple product to Wishlist from Shopping Cart.</p>
     * <p>Steps:</p>
     * <p>1. Add the product to the shopping cart</p>
     * <p>2. Move the product to wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>Expected result:</p>
     * <p>The product is in the wishlist</p>
     *
     * @test
     */
    public function addProductToWishlistFromShoppingCart($productData, $categoryPath)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Adds a product to Shopping Cart from Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add a product to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Add the product to the shopping cart</p>
     * <p>Expected result:</p>
     * <p>The product is in the shopping cart</p>
     *
     * @test
     */
    public function addProductToShoppingCartFromWishlist($productData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
        self::$productToBeRemoved = array();
    }

    /**
     * <p>Adds all products to Shopping Cart from Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add products to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Add all products to the shopping cart</p>
     * <p>Expected result:</p>
     * <p>The products are in the shopping cart</p>
     *
     * @test
     */
    public function addAllProductsToShoppingCartFromWishlist(array $productDataSet)
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
