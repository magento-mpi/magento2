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
 * Compare Products tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CompareProducts_CompareProductsTest extends Mage_Selenium_TestCase
{

    protected $productsList = array();
    /**
     * <p>Create products for test</p>
     */
    public function setUpBeforeTests()
    {
        $this->createProducts();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Remove all products from CompareProducts(Home page?)</p>
     * <p>@TODO</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('');
        $this->assertTrue($this->checkCurrentPage(''), $this->messages);
        $this->addParameter('', '0');
        $this->compareProductsHelper()->clearAll();
    }

    /**
     * <p>Adds a product to Compare Products from Product Details page.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to Compare Products</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed.Product displayed in Compare Products pop-up window</p>
     *
     * @dataProvider getSingleProductInfo
     * @test
     */
    public function addProductToCompareListFromProductPage($productData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
    }

     /**
     * <p>Adds a products to Compare Products from Product Details page.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add first product to Compare Products</p>
     * <p>2. Add second product to Compare Products</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed.Products displayed in Compare Products pop-up window</p>
     *
     * @dataProvider getProductsInfo
     * @test
     */
    public function addProductsToCompareListFromProductPage($productsData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
    }

     /**
     * <p>Remove a product from CompareProducts block from Product Details page.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add two products to CompareProducts</p>
     * <p>2. Remove one product from Compare Products</p>
     * <p>Expected result:</p>
     * <p>Product should disappear from the Compare Products block.</p>
     * <p>Product should not be dispalyed in the Compare Products pop-up</p>
     *
     * @dataProvider getProductsInfo
     * @test
     */
    public function removeProductFromCompareList($productData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
    }

     /**
     * <p>Compare Products block is not dispalyed without products</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to Compare Products</p>
     * <p>2. Remove product from Compare Products</p>
     * <p>Expected result:</p>
     * <p>Compare Products block should disappear</p>
     *
     * @dataProvider getSingleProductInfo
     * @test
     */
    public function emptyCompareListIsNotAvailable($productData)
    {
        //Setup
        //Steps
        //Verify
        //Cleanup
    }

    /*
     * Create products for test
     */
    protected function createProducts()
    {
        //load products data array
        //create products
        //$productsList[] = products data
    }

    public function getSingleProductInfo()
    {
        return array($productsList[1]);
    }


    public function getProductsInfo()
    {
        return array($productsList);
    }

}
