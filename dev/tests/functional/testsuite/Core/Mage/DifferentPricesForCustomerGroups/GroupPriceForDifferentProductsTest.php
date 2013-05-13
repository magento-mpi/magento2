<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DifferentPricesForCustomerGroups
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_DifferentPricesForCustomerGroups_GroupPriceForDifferentProductsTest extends Mage_Selenium_TestCase
{
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
            'general_group'   => 'General_%randomize%',
            'wholesale_group' => 'Wholesale_%randomize%',
            'retailer_group'  => 'Retailer_%randomize%');
        $processedGroupNames = array();
        //Creating three Customer Groups
        $this->loginAdminUser();
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
            array('Product Details' => $attrData['attribute_code']));
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
        $processedGroupNames['general_configurable_attribute_title'] = $attrData['attribute_label'];

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
        $productData['prices_group_price_data'] =
            $this->loadDataSet('Product', 'prices_group_price_data', array('group_price_price' => '%noValue%'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'group_price_price');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(3), $this->getParsedMessages());
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
        $productData['prices_group_price_data'] =
            $this->loadDataSet('Product', 'prices_group_price_data', array('group_price_price' => $priceValue),
                array(
                'group_1' => $processedGroupNames['general_group'],
                'group_2' => $processedGroupNames['wholesale_group'],
                'group_3' => $processedGroupNames['retailer_group']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'group_price_price');
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
    public function verifyingPriceOnFrontEnd ($productType, $processedGroupNames)
    {
        //Data. Creating product with Grouped Price
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', $productType . '_product_visible');
        $productData['prices_group_price_data'] =
            $this->loadDataSet('Product', 'prices_group_price_data', null,
                array(
                'group_1' => $processedGroupNames['general_group'],
                'group_2' => $processedGroupNames['wholesale_group'],
                'group_3' => $processedGroupNames['retailer_group']));
        //Steps. Creating product with Grouped Price
        if ($productType == 'configurable') {
            $productData['general_configurable_attribute_title'] =
                $processedGroupNames['general_configurable_attribute_title'];
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        unset($processedGroupNames['general_configurable_attribute_title']);
        //Creating Customers
        $this->navigate('manage_customers');
        $userEmails = array();
        foreach ($processedGroupNames as $groupKey => $groupName) {
            $userRegisterData = $this->loadDataSet('Customers', 'generic_customer_account',
                array('group' => $groupName));
            $this->customerHelper()->createCustomer($userRegisterData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $userEmails[$groupKey]['email'] = $userRegisterData['email'];
            $userEmails[$groupKey]['password'] = $userRegisterData['password'];
        }
        //Steps. Verifying price on front-end for different customers
        $this->frontend();
        $priceData = $productData['prices_group_price_data'];
        $priceForGroup = reset($priceData);
        foreach ($userEmails as $userInfo) {
            $this->customerHelper()->frontLoginCustomer($userInfo);
            $this->productHelper()->frontOpenProduct($productData['general_name']);
            $this->addParameter('symbol', '$');
            $this->addParameter('price', $priceForGroup['group_price_price']);
            $this->verifyForm(array('group_price' => '$' . $priceForGroup['group_price_price']));
            $priceForGroup = next($priceData);
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