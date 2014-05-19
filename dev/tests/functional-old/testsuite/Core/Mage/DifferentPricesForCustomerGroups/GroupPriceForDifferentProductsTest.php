<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_DifferentPricesForCustomerGroups_GroupPriceForDifferentProductsTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Creating three customer groups</p>
     *
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array(
            'general_group' => 'General_%randomize%',
            'wholesale_group' => 'Wholesale_%randomize%',
            'retailer_group' => 'Retailer_%randomize%'
        );
        $processedGroupNames = array();
        //Creating three Customer Groups
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $customerGroup = $this->loadDataSet('CustomerGroup', 'new_customer_group',
                array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($customerGroup);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $customerGroup['group_name'];
        }
        //Data. Creating attribute for Configurable product
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['advanced_attribute_properties']['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $processedGroupNames['general_configurable_attribute_title'] =
            $attrData['attribute_properties']['attribute_label'];
        $processedGroupNames['attribute_option_name'] = $attrData['option_1']['admin_option_name'];

        return $processedGroupNames;
    }

    /**
     * <p>Creating product with empty grouped price</p>
     *
     * @test
     */
    public function emptyGroupPriceField()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productData['prices_group_price_data'] = $this->loadDataSet('Product', 'prices_group_price_data',
            array('prices_group_price' => '%noValue%'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        for ($i = 0; $i < 3; $i++) {
            $this->addParameter('groupPriceId', $i);
            $this->addFieldIdToMessage('field', 'prices_group_price');
            $this->assertMessagePresent('validation', 'empty_required_field');
            $this->addFieldIdToMessage('dropdown', 'prices_group_price_customer_group');
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(6), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid value for  grouped price</p>
     *
     * @param $priceValue
     * @param array $processedGroupNames
     *
     * @depends preconditionsForTests
     * @dataProvider withInvalidValueDataProvider
     *
     * @test
     */
    public function withInvalidValue($priceValue, $processedGroupNames)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['prices_group_price_data'] = $this->loadDataSet('Product', 'prices_group_price_data',
            array('prices_group_price' => $priceValue),
            array(
                'group_1' => $processedGroupNames['general_group'],
                'group_2' => $processedGroupNames['wholesale_group'],
                'group_3' => $processedGroupNames['retailer_group']
            )
        );
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_group_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(3), $this->getParsedMessages());
    }

    public function withInvalidValueDataProvider()
    {
        return array(
            array('g3648GJTest'),
            array('-128123'),
            array('!@#$%^&**()_+')
        );
    }

    /**
     * <p>Creating different products with grouped price</p>
     *
     * @param string $productType
     * @param array $processedGroupNames
     * @depends preconditionsForTests
     *
     * @test
     * @dataProvider verifyingPriceOnFrontEndDataProvider
     */
    public function verifyingPriceOnFrontEnd($productType, $processedGroupNames)
    {
        //Data. Creating product with Grouped Price
        $this->navigate('manage_products');
        $override = null;
        if ($productType == 'configurable') {
            $override = array(
                'var1_attr_value1' => $processedGroupNames['attribute_option_name'],
                'general_attribute_1' => $processedGroupNames['general_configurable_attribute_title']
            );
        }
        $product = $this->loadDataSet('Product', $productType . '_product_visible', null, $override);
        $product['prices_group_price_data'] = $this->loadDataSet('Product', 'prices_group_price_data', null, array(
            'group_1' => $processedGroupNames['general_group'],
            'group_2' => $processedGroupNames['wholesale_group'],
            'group_3' => $processedGroupNames['retailer_group']
        ));
        //Steps. Creating product with Grouped Price
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        unset($processedGroupNames['general_configurable_attribute_title']);
        unset($processedGroupNames['attribute_option_name']);
        //Creating Customers
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $searchUser = $this->loadDataSet('Customers', 'search_customer', array(
            'email' => $user['email'],
            'group' => '%noValue%'
        ));
        $this->frontend();
        $this->customerHelper()->registerCustomer($user);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        $i = 1;
        foreach ($processedGroupNames as $groupName) {
            $this->loginAdminUser();
            $this->navigate('manage_customers');
            $this->customerHelper()->openCustomer($searchUser);
            $this->openTab('account_information');
            $this->fillDropdown('group', $groupName);
            $this->saveForm('save_customer');
            $this->assertMessagePresent('success', 'success_saved_customer');
            $expectedPrice = array(
                'general_price' => $product['general_price'],
                'prices_special_price' =>
                    $product['prices_group_price_data']['prices_group_price_' . $i++]['prices_group_price']
            );
            $this->customerHelper()->frontLoginCustomer(
                array('email' => $user['email'], 'password' => $user['password'])
            );
            $this->productHelper()->frontOpenProduct($product['general_name']);
            $this->assertEquals($expectedPrice, $this->productHelper()->getFrontendProductPrices());
        }
    }

    public function verifyingPriceOnFrontEndDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('configurable')
        );
    }
}