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
 * Reward Points for Tags
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Tags_RewardPointsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/unset_rewarded_tag_submission_quantity_limit');
        $this->systemConfigurationHelper()->configure('General/disable_reward_points_for_tag_submission');
        $this->logoutCustomer();
    }

    /**
     *
     * @return array
     * @test
     */
    public function preconditionsForRewardPointsTest()
    {
        //Create product
        $product = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_name' => $this->generate('string', 8, ':lower:'),
        ));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create customer
        $customerData = $this->loadDataSet('Customers', 'customer_account_register', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:'),
        ));
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($customerData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        $this->loginAdminUser();
        //Enable reward points functionality for tags
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/enable_reward_points');
        $this->systemConfigurationHelper()->configure('General/enable_reward_points_for_tag_submission');

        return array('customer' => $customerData, 'product' => $product['general_name']);
    }

    /**
     * Verify that customer receives reward points only after tag has been approved by the store administrator.
     * Verify that the proper reward point amount is added to the customer reward points balance.
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForRewardPointsTest
     * @TestlinkId TL-MAGE-2457
     */
    public function receivingRewardPoints($testData)
    {
        $this->markTestIncomplete('BUG: There is no tag_accepted_success message after add tag');
        $tag = $this->generate('string', 4, ':lower:');
        $rewardTagConfig = $this->loadDataSet('General', 'enable_reward_points_for_tag_submission');
        $rewardBalance =
            $rewardTagConfig['tab_1']['configuration']['actions_for_acquiring_reward_points']['new_tag_submission'];

        //Step 1
        $this->customerHelper()->frontLoginCustomer(array(
                'email' => $testData['customer']['email'],
                'password' => $testData['customer']['password'])
        );
        //Step 2
        $this->productHelper()->frontOpenProduct($testData['product']);
        //Steps 3-5
        $this->tagsHelper()->frontendAddTag($tag);
        //Verifying
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tag, $testData['product']);
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => 'Pending'));
        $this->navigate('manage_products');
        $this->assertTrue($this->tagsHelper()->verifyTagProduct(
            array('tag_search_name' => $tag, 'tag_search_email' => $testData['customer']['email']),
            array('product_name' => $testData['product'])
        ), 'Customer tagged product verification failed');
        //Step 6
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $testData['customer']['email']));
        //Step 7
        $this->assertEquals('No records found.', $this->customerHelper()->getRewardPointsBalance(),
            'Customer reward points balance is not 0');
        //Step 8
        $this->navigate('all_tags');
        //Steps 9-11
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');
        //Verifying
        $this->navigate('all_tags');
        $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => 'Approved'));
        //Steps 12-13
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $testData['customer']['email']));
        //Verifying
        $this->assertEquals($rewardBalance, $this->customerHelper()->getRewardPointsBalance(),
            'Customer reward points balance was not updated');
        $this->assertNotNull($this->customerHelper()->searchRewardPointsHistoryRecord(array(
            'Balance' => $rewardBalance,
            'Points' => '+' . $rewardBalance,
            'Reason' => "For submitting tag ($tag)",
        )), 'Reward points history record is absent');
    }

    /**
     *
     * @return array
     * @test
     */
    public function preconditionsForRewardPointsLimitationTest()
    {
        //Create two products
        $products = array(
            $this->loadDataSet('Product', 'simple_product_visible',
                array('general_name' => $this->generate('string', 8, ':lower:'),)),
            $this->loadDataSet('Product', 'simple_product_visible',
                array('general_name' => $this->generate('string', 8, ':lower:'),))
        );
        foreach ($products as $product) {
            $this->navigate('manage_products');
            $this->productHelper()->createProduct($product);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        //Create customer
        $customerData = $this->loadDataSet('Customers', 'customer_account_register', array(
            'first_name' => $this->generate('string', 5, ':lower:'),
            'last_name' => $this->generate('string', 5, ':lower:'),
        ));
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($customerData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        $this->loginAdminUser();

        //Set “Rewarded Tag Submission Quantity Limit” to 3.
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/set_rewarded_tag_submission_quantity_limit');

        return array(
            'customer' => $customerData,
            'product' => array($products[0]['general_name'], $products[1]['general_name'])
        );
    }

    /**
     * Verify that “Rewarded Tag Submission Quantity Limit” field limits the number of tags that can be used to
     * earn points for each customer.
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForRewardPointsLimitationTest
     * @TestlinkId TL-MAGE-2457
     */
    public function receivingRewardPointsLimitation($testData)
    {
        $this->markTestIncomplete('BUG: There is no tag_accepted_success message after add tag');
        $tags = array(
            $this->generate('string', 4, ':lower:'), $this->generate('string', 4, ':lower:'),
            $this->generate('string', 4, ':lower:'), $this->generate('string', 4, ':lower:')
        );
        $rewardTagConfig = $this->loadDataSet('General', 'enable_reward_points_for_tag_submission');
        $rewardPointsForTag =
            $rewardTagConfig['tab_1']['configuration']['actions_for_acquiring_reward_points']['new_tag_submission'];

        $rewardPointsLimit = $this->loadDataSet('General',
            'set_rewarded_tag_submission_quantity_limit/tab_1/configuration/'
                . 'actions_for_acquiring_reward_points/rewarded_tag_submission_limit'
        );
        $rewardBalance = $rewardPointsForTag * $rewardPointsLimit;
        //Step 1
        $this->customerHelper()->frontLoginCustomer(array(
            'email' => $testData['customer']['email'],
            'password' => $testData['customer']['password']
        ));
        for ($i = 0; $i < 2; $i++) {
            //Steps 2, 5
            $this->productHelper()->frontOpenProduct($testData['product'][$i]);
            //Steps 3-4, 6-7
            $addedTags[$i] = array($tags[2 * $i], $tags[2 * $i + 1]);
            $this->tagsHelper()->frontendAddTag($addedTags[$i][0] . ' ' . $addedTags[$i][1]);
            //Verifying
            $this->assertMessagePresent('success', 'tag_accepted_success');
            $this->tagsHelper()->frontendTagVerification($addedTags[$i], $testData['product'][$i]);
            $this->loginAdminUser();
            foreach ($addedTags[$i] as $tag) {
                $this->navigate('all_tags');
                $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => 'Pending'));
            }
            foreach ($addedTags[$i] as $tag) {
                $this->navigate('manage_products');
                $this->assertTrue($this->tagsHelper()->verifyTagProduct(
                    array(
                        'tag_search_name' => $tag,
                        'tag_search_email' => $testData['customer']['email']
                    ),
                    array('product_name' => $testData['product'][$i])
                ), 'Customer tagged product verification failed');
            }
        }
        //Steps 8-9
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $testData['customer']['email']));
        //Step 10
        $this->assertEquals('No records found.', $this->customerHelper()->getRewardPointsBalance(),
            'Customer reward points balance is not 0');
        //Step 11
        $this->navigate('all_tags');
        //Step 12
        foreach ($tags as &$tag) {
            $tag = array('tag_name' => $tag);
        }
        $this->tagsHelper()->changeTagsStatus($tags, 'Approved');
        //Step 13
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $testData['customer']['email']));
        //Verifying
        $this->assertEquals($rewardBalance, $this->customerHelper()->getRewardPointsBalance(),
            'Customer reward points balance is wrong');
        for ($i = 0; $i < $rewardPointsLimit; $i++) {
            $this->assertNotNull($this->customerHelper()->searchRewardPointsHistoryRecord(array(
                'Balance' => ($i + 1) * $rewardPointsForTag,
                'Points' => '+' . $rewardPointsForTag,
                'Reason' => 'For submitting tag (' . $tags[$i]['tag_name'] . ').'
            )), 'Reward points history record is absent');
        }
    }
}