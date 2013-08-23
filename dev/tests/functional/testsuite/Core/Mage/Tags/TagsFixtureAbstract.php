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
abstract class Core_Mage_Tags_TagsFixtureAbstract extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
    }

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
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'user' => array(1 => array('email' => $userData['email'], 'password' => $userData['password'])),
            'simple' => $simple['simple']['product_name'],
            'category' => $simple['category']['path']
        );
    }

    /**
     * @return array
     */
    protected function _preconditionsForTaggedProductTests()
    {
        //Data
        $userData[1] = $this->loadDataSet('Customers', 'customer_account_register');
        $userData[2] = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData[2]);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'user' => array(
                1 => array('email' => $userData[1]['email'], 'password' => $userData[1]['password']),
                2 => array('email' => $userData[2]['email'], 'password' => $userData[2]['password'])
            ),
            'simple' => $simple['simple']['product_name'],
            'category' => $simple['category']['path']
        );
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
            $tagData[$i] = array('tag_name' => 'tag_' . str_pad($i, 2, 0, STR_PAD_LEFT), 'tag_status' => 'Pending');
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
        //Data
        $customerData = $this->loadDataSet('Customers', 'customer_account_register', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:')
        ));
        $simple = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_name' => $this->generate('string', 8, ':lower:')));
        //Create a product and customer
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($customerData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array('customer' => $customerData, 'product' => $simple['general_name']);
    }

    /**
     * @return array
     */
    protected function _preconditionsForReportsTests()
    {
        //Data
        $customerData[] = $this->loadDataSet('Customers', 'customer_account_register', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:')
        ));
        $customerData[] = $this->loadDataSet('Customers', 'customer_account_register', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:')
        ));
        $productData[0] = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_name' => $this->generate('string', 8, ':lower:')));
        $productData[1] = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_name' => $this->generate('string', 8, ':lower:')));
        //Create two products
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData[0]);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($productData[1]);
        $this->assertMessagePresent('success', 'success_saved_product');

        //Submit one tag (first customer, first product)
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($customerData[0]);
        $this->assertMessagePresent('success', 'success_registration');
        $this->productHelper()->frontOpenProduct($productData[0]['general_name']);
        $tags[0] = $this->generate('string', 4, ':lower:');
        $this->tagsHelper()->frontendAddTag($tags[0]);
        $this->logoutCustomer();

        //Submit two tags (second customer, second product)
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($customerData[1]);
        $this->assertMessagePresent('success', 'success_registration');
        $this->productHelper()->frontOpenProduct($productData[1]['general_name']);
        $tags[1] = $this->generate('string', 4, ':lower:');
        $this->tagsHelper()->frontendAddTag($tags[1]);
        $tags[2] = $this->generate('string', 4, ':lower:');
        $this->tagsHelper()->frontendAddTag($tags[2]);
        $this->logoutCustomer();

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
                'tags' => array($tags[0])
            ),
            array(
                'customer' => $customerData[1],
                'product' => $productData[1]['general_name'],
                'tags' => array($tags[1], $tags[2])
            )
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
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $simple = array();
        $category = array();
        //Steps
        for ($i = 0; $i < 3; $i++) {
            $product = $this->productHelper()->createSimpleProduct(true);
            $simple[] = $product['simple']['product_name'];
            $category[] = $product['category']['path'];
        }
        $this->reindexInvalidedData();
        $this->flushCache();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'user' => array(1 => array('email' => $userData['email'], 'password' => $userData['password'])),
            'simple' => $simple,
            'category' => $category
        );
    }
}