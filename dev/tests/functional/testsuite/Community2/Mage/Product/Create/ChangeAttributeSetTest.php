<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products creation tests with ability to change attribute set during creation and editing products
 */
class Community2_Mage_Product_Create_ChangeAttributeSetTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog - Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Preconditions for tests</p>
     * <p>1. Custom attribute set based on "Default" set is created</p>
     * <p>2. A new attribute is created and added to custom attribute set</p>
     *
     * @test
     *
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $testData = $this->loadDataSet('AttributeSet', 'attribute_set');
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attrData['attribute_code']));
        $setName = $testData['set_name'];
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($testData);
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array('product_attribute_set' => $setName, 'assigned_attribute' => $attrData['attribute_code']);
    }

    // @codingStandardsIgnoreStart
    /**
     * <p>Change attribute set during product creation from Default to Custom attribute set </p>
     * <p>Preconditions:</p>
     * <p>Custom Attribute set, based on Default is created. User-defined attribute is assigned to this Attribute Set</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Navigate to Catalog - Manage Products</p>
     * <p>3. Press "Add product button" </p>
     * <p>4. Select Attribute Set - Default</p>
     * <p>5. Select Product type(Simple, Virtual, Downloadable, Grouped, Bundle)</p>
     * <p>6. Press the Continue button</p>
     * <p>7. Fill all required fields for product creation</p>
     * <p>8. Press the Change Attribute Set button</p>
     * <p>9. Select Custom attribute set</p>
     * <p>10. Press OK button</p>
     * <p>11. Press Save button on product page</p>
     *
     * <p>Expected results:</p>
     * <p>After Step 6: New Product(Default) page opens with Change Attribute Set button in header (with Back, Reset, Save, Save and Continue Edit buttons)</p>
     * <p>After Step 8: Change Attribute Set pop-up page opens and has: dropdown list with all attribute sets, OK, Cancel buttons</p>
     * <p>After Step 10: page title is New Product (%Custom%), attributes from previous attribute set is deleted and for current attribute set is added</p>
     * <p>After Step 11: Product has been successfully saved and "Product has been saved" message appears</p>
     *
     * @param string $productType
     * @param array $customSetData
     *
     * @test
     * @dataProvider changeAttributeSetInCreatedProductDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5881, TL-MAGE-5896, TL-MAGE-5900, TL-MAGE-5901, TL-MAGE-5902
     */
    // @codingStandardsIgnoreEnd
    public function fromDefaultToCustomCreate($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required');
        $assignedAttribute = $customSetData['assigned_attribute'];
        $newAttributeSet = $customSetData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    // @codingStandardsIgnoreStart
    /**
     * <p>Change attribute set during product creating from Custom to Default attribute set </p>
     * <p>Preconditions:</p>
     * <p>Custom Attribute set, based on Default is created. User-defined attribute is assigned to this Attribute Set</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Navigate to Catalog>Manage Products</p>
     * <p>3. Press "Add product button" </p>
     * <p>4. Select Attribute Set - Custom</p>
     * <p>5. Select Product type (Simple, Virtual, Downloadable, Grouped, Bundle)</p>
     * <p>6. Press the Continue button</p>
     * <p>7. Fill all required fields for product creation</p>
     * <p>8. Press the Change Attribute Set button</p>
     * <p>9. Select Default attribute set</p>
     * <p>10. Press OK button</p>
     * <p>11. Press Save button on product page</p>
     *
     * <p>Expected results:</p>
     * <p>After Step 6: New Product(Custom) page opens with Change Attribute Set button in header (with Back, Reset, Save, Save and Continue Edit buttons)</p>
     * <p>After Step 8: Change Attribute Set pop-up page opens and has: dropdown list with all attribute sets, OK, Cancel buttons</p>
     * <p>After Step 10: page title is New Product (Custom), attributes from previous attribute set is deleted and for current attribute set is added</p>
     * <p>After Step 11: Product has been successfully saved and "Product has been saved" message appears</p>
     *
     * @param string $productType
     * @param $customSetData
     *
     * @test
     * @dataProvider changeAttributeSetInCreatedProductDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5903, TL-MAGE-5904, TL-MAGE-5905, TL-MAGE-5906, TL-MAGE-5907
     */
    // @codingStandardsIgnoreEnd
    public function fromCustomToDefaultCreate($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required',
            array('product_attribute_set' => $customSetData['product_attribute_set']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $customSetData['assigned_attribute'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial, array($customSetData['assigned_attribute']));
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    // @codingStandardsIgnoreStart
    /**
     * <p>Change attribute set during product editing from Default to Custom attribute set </p>
     * <p>Preconditions:</p>
     * <p>Custom Attribute set, based on Default is created. User-defined attribute is assigned to this Attribute Set</p>
     * <p>Product, based on Default Attribute Set is created</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Navigate to Catalog - Manage Products</p>
     * <p>3. Find created product in the Manage Products grid and click on it</p>
     * <p>4. Press the Change Attribute Set button</p>
     * <p>5. Select custom attribute set</p>
     * <p>6. Press OK button</p>
     * <p>7. Press Save button on product page</p>
     *
     * <p>Expected results:</p>
     * <p>After Step 3: %productName%(Default) page opens with Change Attribute Set button in header (with Back, Reset, Save, Save and Continue Edit buttons)</p>
     * <p>After Step 4: Change Attribute Set pop-up page opens and has: dropdown list with all attribute sets, OK, Cancel buttons</p>
     * <p>After Step 6: page title is %productName% (New Attr Set Test)</p>
     * <p>- all information inputted in common for both attribute sets tabs and fields has been saved</p>
     * <p>- attributes from previous attribute set is deleted and for current attribute set is added</p>
     * <p>After Step 7: Product has been successfully saved and "Product has been saved" message appears</p>
     *
     * @param string $productType
     * @param $customSetData
     *
     * @test
     * @dataProvider changeAttributeSetInCreatedProductDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5884, TL-MAGE-5908, TL-MAGE-5909, TL-MAGE-5910, TL-MAGE-5911
     */
    // @codingStandardsIgnoreEnd
    public function fromDefaultToCustomEdit($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required');
        $assignedAttribute = $customSetData['assigned_attribute'];
        $newAttributeSet = $customSetData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productDataInitial['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    // @codingStandardsIgnoreStart
    /**
     * <p>Change attribute set during product editing from Custom to Default attribute set</p>
     * <p>Preconditions:</p>
     * <p>Attribute set, based on Default is created. User-defined attribute is assigned to this Attribute Set</p>
     * <p>Product, based on Default Attribute Set is created</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Navigate to Catalog - Manage Products</p>
     * <p>3. Find created product in the Manage Products grid and click on it</p>
     * <p>4. Press the Change Attribute Set button</p>
     * <p>5. Select Default attribute set</p>
     * <p>6. Press OK button</p>
     * <p>7. Press Save button on product page</p>
     *
     * <p>Expected results:</p>
     * <p>After Step 3: %productName%(Custom) page opens with Change Attribute Set button in header (with Back, Reset, Save, Save and Continue Edit buttons)</p>
     * <p>After Step 4: Change Attribute Set pop-up page opens and has: dropdown list with all attribute sets, OK, Cancel buttons</p>
     * <p>After Step 6: page title is %productName% (Default)</p>
     * <p>- all information inputted in common for both attribute sets tabs and fields  has been saved</p>
     * <p>- attributes from previous attribute set is deleted and for current attribute set is added</p>
     * <p>After Step 7: Product has been successfully saved and "Product has been saved" message appears</p>
     *
     * @param string $productType
     * @param $customSetData
     *
     * @test
     * @dataProvider changeAttributeSetInCreatedProductDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5912, TL-MAGE-5913,TL-MAGE-5914,TL-MAGE-5915, TL-MAGE-5916
     */
    // @codingStandardsIgnoreEnd
    public function fromCustomToDefaultEdit($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required',
            array('product_attribute_set' => $customSetData['product_attribute_set']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $customSetData['assigned_attribute'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productDataInitial['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial, array($customSetData['assigned_attribute']));
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    public function changeAttributeSetInCreatedProductDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('grouped'),
            array('bundle'));
    }
}