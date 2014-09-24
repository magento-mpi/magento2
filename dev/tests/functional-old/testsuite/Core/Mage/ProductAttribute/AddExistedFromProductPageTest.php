<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Add existed attribute on product page
 */
class Core_Mage_ProductAttribute_AddExistedFromProductPageTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to Products - Catalog.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Preconditions for tests:
     *  1. Create test product attribute.
     *  2. Create new attribute set.
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        $return = array();
        //Create product attributes
        $this->navigate('manage_attributes');
        for ($i = 0; $i < 3; $i++) {
            $attribute = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
            $this->productAttributeHelper()->createAttribute($attribute);
            $this->assertMessagePresent('success', 'success_saved_attribute');
            $return[$i]['name'] = $attribute['attribute_properties']['attribute_label'];
            $return[$i]['code'] = $attribute['advanced_attribute_properties']['attribute_code'];
        }
        //Create attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $return['setName'] = $attributeSet['set_name'];

        return $return;
    }

    /**
     * Add existing attribute to ‘Default’ product template
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-1
     */
    public function addAttributeToDefaultTemplate($data)
    {
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productAttributeHelper()->addAttributeOnProductTab($data[0]['name']);
        //Verifying
        $this->addParameter('elementId', 'attribute-' . $data[0]['code'] . '-container');
        $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'),
            'Added attribute was not found on the "Product Details" tab');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet(array('set_name' => 'Default'));
        $this->attributeSetHelper()->verifyAttributeAssignment(array($data[0]['code']));
    }

    /**
     * Add existing attribute to custom product template
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-2
     */
    public function addAttributeToCustomTemplate($data)
    {
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->changeAttributeSet($data['setName']);
        $this->productAttributeHelper()->addAttributeOnProductTab($data[0]['name']);
        //Verifying
        $this->addParameter('elementId', 'attribute-' . $data[0]['code'] . '-container');
        $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'),
            'Added attribute was not found on the "Product Details" tab');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet(array('set_name' => $data['setName']));
        $this->attributeSetHelper()->verifyAttributeAssignment(array($data[0]['code']));
    }

    /**
     * Add existing attribute while product editing
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-3
     */
    public function addAttributeWhileEditing($data)
    {
        //Data
        $product = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $product['general_sku']));
        $this->openTab('meta_information');
        $this->productAttributeHelper()->addAttributeOnProductTab($data[1]['name']);
        $this->openTab('meta_information');
        //Verifying
        $this->addParameter('elementId', 'attribute-' . $data[1]['code'] . '-container');
        $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'),
            'Added attribute was not found on the "Search Engine Optimization" tab');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet(array('set_name' => $product['product_attribute_set']));
        $this->attributeSetHelper()->verifyAttributeAssignment(array($data[1]['code']));
    }

    /**
     * Add existing attribute and create new attribute in new product template
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-4
     */
    public function addExistedAndNewAttributes($data)
    {
        //Data
        $attribute = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
        $attributeCodes = array(
            $data[2]['code'],
            $attribute['advanced_attribute_properties']['attribute_code']
        );
        $setName = $this->generate('string', 10, ':alnum:');
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->changeAttributeSet($data['setName']);
        $this->productAttributeHelper()->addAttributeOnProductTab($data[2]['name']);
        $this->productAttributeHelper()->createAttributeOnProductTab($attribute, $setName);
        //Verifying
        $this->addParameter('elementId', 'attribute-' . $data[2]['code'] . '-container');
        $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'),
            'Added attribute was not found on the tab');
        $this->addParameter('elementId',
            'attribute-' . $attribute['advanced_attribute_properties']['attribute_code'] . '-container');
        $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'),
            'Created attribute was not found on the tab');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet(array('set_name' => $setName));
        $this->attributeSetHelper()->verifyAttributeAssignment($attributeCodes);
    }

    /**
     * Search non-existent attribute
     *
     * @test
     * @TestLinkid TL-MAGETWO-5
     */
    public function addNonexistentAttribute()
    {
        //Data
        $attributeName = $this->generate('string', 10, ':alnum:');
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->addParameter('tab', $this->getControlAttribute('tab', $this->_getActiveTabUimap()->getTabId(), 'name'));
        $this->clickButton('add_attribute', false);
        $this->waitForControlVisible('field', 'attribute_search');
        $this->productAttributeHelper()->fillSearchAttributeField($attributeName);
        //Verifying
        $this->assertTrue($this->controlIsVisible('pageelement', 'attribute_no_records'), 'Some attributes were found');
        $this->getControlElement('button', 'create_new_attribute')->click();
        $this->waitForControl('pageelement', 'add_new_attribute_iframe');
        $this->pleaseWait();
        $this->frame('create_new_attribute_container');
        $this->setCurrentPage('new_product_attribute_from_product_page');
        $this->assertEquals($attributeName, $this->getControlAttribute('field', 'attribute_label', 'value'));
    }

    /**
     * Search for non-unique attribute
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-6
     */
    public function addNonuniqueAttribute($data)
    {
        //Data
        $attributeName = $data[2]['name'];
        $attribute = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield',
            array('attribute_label' => $attributeName));
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attribute);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');
        for ($i = 2; $i > 0; $i--) {
            $this->clickButton('add_attribute', false);
            $this->productAttributeHelper()->fillSearchAttributeField($attributeName);
            $this->assertEquals($i, $this->getControlCount('link', 'attribute'));
            $this->productAttributeHelper()->selectAttribute();
        }
        //Verifying
        $this->clickButton('add_attribute', false);
        $this->productAttributeHelper()->fillSearchAttributeField($attributeName);
        $this->assertTrue($this->controlIsVisible('pageelement', 'attribute_no_records'), 'Some attributes were found');
    }

    /**
     * Deleted attribute is not displayed in search results
     *
     * @param $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-7
     */
    public function searchForDeletedAttribute($data)
    {
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->changeAttributeSet($data['setName']);
        $this->clickButton('add_attribute', false);
        $this->productAttributeHelper()->fillSearchAttributeField($data[1]['name']);
        $this->assertTrue($this->controlIsVisible('link', 'attribute'));
        //Delete attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute(array('attribute_code' => $data[1]['code']));
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Verify deleted attribute on product page
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->changeAttributeSet($data['setName']);
        $this->clickButton('add_attribute', false);
        $this->productAttributeHelper()->fillSearchAttributeField($data[1]['name']);
        $this->assertTrue($this->controlIsVisible('pageelement', 'attribute_no_records'), 'Some attributes were found');
    }
}
