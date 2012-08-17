<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full Page Cache
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Tags_PageCacheTest extends Mage_Selenium_TestCase
{
    static protected $_isFpcEnabledBeforeTests;
    static protected $_isFpcEnabledCurrently;
    static protected $_customerData;
    static protected $_productData;

    /**
     * Log in to backend
     * Set current full page cache status
     * Create customer
     * Create product
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('cache_storage_management');
        self::$_isFpcEnabledBeforeTests = $this->cacheStorageManagementHelper()->isFullPageCacheEnabled();
        self::$_isFpcEnabledCurrently = self::$_isFpcEnabledBeforeTests;


        //Create customer
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:'),
        ));
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer(self::$_customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Create product
        self::$_productData = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_name' => $this->generate('string', 8, ':lower:'),
        ));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct(self::$_productData);
        $this->assertMessagePresent('success', 'success_saved_product');

    }

    /**
     * Enable/disable full page cache if it was enabled/disabled before
     */
    protected function tearDownAfterTestClass()
    {
        if (self::$_isFpcEnabledBeforeTests != self::$_isFpcEnabledCurrently) {
            $this->loginAdminUser();
            $this->navigate('cache_storage_management');
            if (self::$_isFpcEnabledBeforeTests) {
                $this->cacheStorageManagementHelper()->enableFullPageCache();
            } else {
                $this->cacheStorageManagementHelper()->disableFullPageCache();
            }
        }
    }

    /**
     * "Flush Magento Cache" and "Flush Cache Storage" buttons behavior
     * Precondition: FPC ("Page Cache") is enabled, customer is created, product is created
     * Steps:
     * 1. Go to frontend
     * 2. Browse product view page
     * 3. Submit new tag for a product
     * 4. Go to backend, open Catalog - Tags - Pending Tags and approve newly submitted tag
     * 5. Go back to product view page on frontend again, refresh it in browser several times to verify that it is
     * cached into FPC
     * Expected: Page is refreshed,  newly approved tag IS NOT shown
     * 6. Go to backend , System - Cache Management
     * 7. Click on "Flush Magento Cache"/"Flush Cache Storage" button
     * 8. Go back to product view page on frontend again, refresh it in browser
     * Expected: Page is refreshed, newly approved tag IS shown
     *
     * @param string $buttonName
     *
     * @test
     * @dataProvider cacheButtonNameDataProvider
     * @TestlinkId TL-MAGE-1815, TL-MAGE-1816
     */
    public function cacheButtonsBehavior($buttonName)
    {
        //Precondition
        if (!self::$_isFpcEnabledCurrently) {
            $this->navigate('cache_storage_management');
            $this->assertTrue($this->cacheStorageManagementHelper()->enableFullPageCache(),
                'Unable to enable Full Page Cache');
            self::$_isFpcEnabledCurrently = true;
        }

        //Step 1
        $this->customerHelper()->frontLoginCustomer(array(
                'email' => self::$_customerData['email'],
                'password' => self::$_customerData['password'])
        );
        //Step 2
        $this->productHelper()->frontOpenProduct(self::$_productData['general_name']);
        //Step 3
        $tag = $this->generate('string', 4, ':alpha:');
        $this->tagsHelper()->frontendAddTag($tag);
        //Step 4
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');
        //Step 5
        $this->productHelper()->frontOpenProduct(self::$_productData['general_name']);
        //Verifying
        $this->addParameter('tagName', $tag);
        $this->assertFalse($this->controlIsPresent('link', 'tag_name'), "Tag $tag is found: page was not cached");
        //Step 6
        $this->loginAdminUser();
        $this->navigate('cache_storage_management');
        //Step 7
        switch ($buttonName) {
            case 'flush_magento_cache':
                $this->clickButton($buttonName);
                $this->assertMessagePresent('success', 'success_flushed_cache');
                break;
            case 'flush_cache_storage':
                $this->flushCache();
                break;
        }
        //Step 8
        $this->productHelper()->frontOpenProduct(self::$_productData['general_name']);
        //Verifying
        $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $tag");

    }

    public function cacheButtonNameDataProvider()
    {
        return array(
            array('flush_magento_cache'),
            array('flush_cache_storage'),
        );
    }
}