<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Community2_Mage_Tags_TagsFixtureAbstract extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to Catalog -> Tags -> All tags
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
    }
    /**
     * Tear down:
     * Navigate to Catalog -> Tags -> All tags
     * Delete all tags
     */
    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
    }
    /**
     * @return array
     */
    protected function _preconditionsForAllTagsTests()
    {
        //Data
        $userData = array();
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        return array('user'     => $userData,
            'simple'   => $simple['simple']['product_name'],
            'category' => $simple['category']['path']);
    }
    /**
     * @return array
     */
    protected function _preconditionsForTaggedProductTests()
    {
        //Data
        $userData = array();
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $userData[2] = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->createCustomer($userData[2]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        $userData[2] = array('email' => $userData[2]['email'], 'password' => $userData[2]['password']);
        return array('user'     => $userData,
            'simple'   => $simple['simple']['product_name'],
            'category' => $simple['category']['path']);
    }
    /**
     * @return array
     */
    protected function _preconditionsForMassActionsTests()
    {
        $tagData = array();
        //Precondition
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
        for ($i = 0; $i < 21; $i++) {
            $tagData[$i] = array(
                'tag_name' => 'tag_' . str_pad($i, 2, 0, STR_PAD_LEFT),
                'tag_status' => 'Pending',
            );
            $this->tagsHelper()->addTag($tagData[$i]);
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
        return $tagData;
    }
    /**
     * @return array
     */
    protected function _preconditionsForReportEntriesTest()
    {
        //Create a customer
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:'),
        ));
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Create a product
        $simple = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_name' => $this->generate('string', 8, ':lower:'),
        ));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('customer' => $customerData,
            'product' => $simple['general_name']);
    }
    /**
     * @return array
     */
    protected function _preconditionsForReportsTests()
    {
        //Create two customers
        $customerData = array(
            $this->loadDataSet('Customers', 'generic_customer_account', array(
                'first_name' => $this->generate('string', 5, ':lower:'),
                'last_name' => $this->generate('string', 5, ':lower:'),
            )),
            $this->loadDataSet('Customers', 'generic_customer_account', array(
                'first_name' => $this->generate('string', 5, ':lower:'),
                'last_name' => $this->generate('string', 5, ':lower:'),
            )),
        );
        foreach ($customerData as $customer) {
            $this->navigate('manage_customers');
            $this->customerHelper()->createCustomer($customer);
            $this->assertMessagePresent('success', 'success_saved_customer');
        }

        //Create two products
        $productData[0] = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_name' => $this->generate('string', 8, ':lower:'),
        ));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData[0]);
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData[1] = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_name' => $this->generate('string', 8, ':lower:'),
        ));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData[1]);
        $this->assertMessagePresent('success', 'success_saved_product');

        //Submit one tag (first customer, first product)
        $this->customerHelper()->frontLoginCustomer(array(
                'email' => $customerData[0]['email'],
                'password' => $customerData[0]['password'])
        );
        $this->productHelper()->frontOpenProduct($productData[0]['general_name']);
        $tags[0] = $this->generate('string', 4, ':lower:');
        $this->tagsHelper()->frontendAddTag($tags[0]);

        //Submit two tags (second customer, second product)
        $this->customerHelper()->frontLoginCustomer(array(
                'email' => $customerData[1]['email'],
                'password' => $customerData[1]['password'])
        );
        $this->productHelper()->frontOpenProduct($productData[1]['general_name']);
        $tags[1] = $this->generate('string', 4, ':lower:');
        $this->tagsHelper()->frontendAddTag($tags[1]);
        $tags[2] = $this->generate('string', 4, ':lower:');
        $this->tagsHelper()->frontendAddTag($tags[2]);

        //Change tags status to approved
        $this->loginAdminUser();
        foreach ($tags as $tag) {
            $this->navigate('all_tags');
            $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');
        }
        return array(
            array(
                'customer' => $customerData[0],
                'product' => $productData[0]['general_name'],
                'tags' => array($tags[0]),
            ),
            array(
                'customer' => $customerData[1],
                'product' => $productData[1]['general_name'],
                'tags' => array($tags[1], $tags[2]),
            ),
        );
    }
    /**
     * @return array
     */
    protected function _preconditionsForRssTests()
    {
        //Enable Rss Feeds System Configuration - Enable RSS and Tags Products
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Catalog/enable_tag_rss');
        //Data
        $userData = array();
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $simple = array();
        $category = array();
        for ($i = 0; $i < 3; $i++) {
            $productData = $this->productHelper()->createSimpleProduct(true);
            $simple[] = $productData['simple']['product_name'];
            $category[] = $productData['category']['path'];
        }
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        return array('user'     => $userData,
            'simple'   => $simple,
            'category' => $category);
    }
}