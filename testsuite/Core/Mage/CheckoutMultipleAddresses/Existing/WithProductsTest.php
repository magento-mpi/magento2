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
 * Checkout Multiple Addresses tests with different product types
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutMultipleAddresses_Existing_WithProductsTest extends Mage_Selenium_TestCase
{
    private static $productTypes = array('simple', 'virtual', 'downloadable',
                                         'bundle', 'configurable', 'grouped');

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create all types of products</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        foreach (self::$productTypes as $product) {
            $method = 'create' . ucfirst($product) . 'Product';
            $products[$product] = $this->productHelper()->$method();
        }
        return array($products,
                     'email' => $userData['email']);
    }

    /**
     * <p>Checkout with multiple addresses simple and virtual/downloadable products</p>
     * <p>Preconditions:</p>
     * <p>1.Products are created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $product
     * @param array $testData
     *
     * @test
     * @dataProvider productTypesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5234
     */
    public function withVirtualTypeOfProducts($product, $testData)
    {
        //Data
        $simple = $testData[0]['simple']['simple']['product_name'];
        $virtual = $testData[0]['configurable'][$product]['product_name'];
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
                                           array('email' => $testData['email']),
                                           array('product_1'=> $simple,
                                                'product_2' => $virtual));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function productTypesDataProvider()
    {
        return array(
            array('virtual'),
            array('downloadable')
        );
    }

    /**
     * <p>Checkout with multiple addresses simple/virtual/downloadable products with custom options</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $product
     * @param array $testData
     *
     * @test
     * @dataProvider withCustomOptionsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5235
     */
    public function withCustomOptions($product, $testData)
    {
    }

    public function withCustomOptionsDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable')
        );
    }

    /**
     * <p>Checkout with multiple addresses grouped products</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5236
     */
    public function withGroupedProduct($testData)
    {
    }

    /**
     * <p>Checkout with multiple addresses bundle products</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $product
     * @param array $testData
     *
     * @test
     * @dataProvider withBundleProductDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5237
     */
    public function withBundleProduct($product, $testData)
    {
    }

    /**
     * <p>Data provider for createBundleProducts</p>
     *
     * @return array
     */
    public function withBundleProductDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
        );
    }

    /**
     * <p>Checkout with multiple addresses configurable product with associated simple</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $type
     * @param array $testData
     *
     * @test
     * @dataProvider withConfigurableDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5238
     */
    public function withConfigurable($type, $testData)
    {
    }

    public function withConfigurableDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
        );
    }
}
