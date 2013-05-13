<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Delete product.</p>
     *
     * @param string $type
     *
     * @test
     * @dataProvider deleteSingleProductDataProvider
     * @TestlinkId TL-MAGE-3425
     */
    public function deleteSingleProduct($type)
    {
        //Data
        $productData = $this->loadDataSet('Product', $type . '_product_required');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, $type);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');
    }

    public function deleteSingleProductDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('grouped'),
            array('bundle')
        );
    }

    /**
     * <p>Delete configurable product</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-3424
     */
    public function deleteSingleConfigurableProduct()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associated = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['attribute_code']));
        $configurable = $this->loadDataSet('Product', 'configurable_product_required', array('associated_weight' => 15),
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['attribute_label']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associated);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');

        return $attrData;
    }

    /**
     * Delete product that used in configurable
     *
     * @param string $type
     * @param array $attrData
     *
     * @test
     * @dataProvider deleteAssociatedToConfigurableDataProvider
     * @depends deleteSingleConfigurableProduct
     */
    public function deleteAssociatedToConfigurable($type, $attrData)
    {
        //Data
        $associated = $this->loadDataSet('Product', $type . '_product_required');
        $associated['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array('associated_name' => $associated['general_name'],
                  'associated_sku' => $associated['general_sku']),
            array('var1_attr_value1' => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['attribute_label']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $associated['general_sku']));
        //Steps
        $this->productHelper()->createProduct($associated, $type);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');
    }

    public function deleteAssociatedToConfigurableDataProvider()
    {
        return array(
            array('simple'),
            array('virtual')
        );
    }

    /**
     * Delete product that used in Grouped or bundle
     *
     * @param string $associatedType
     * @param string $type
     *
     * @test
     * @dataProvider deleteAssociatedProductDataProvider
     */
    public function deleteAssociatedProduct($associatedType, $type)
    {
        //Data
        $associatedData = $this->loadDataSet('Product', $associatedType . '_product_required');
        if ($associatedType == 'downloadable') {
            $associatedData['downloadable_information_data']['downloadable_links_purchased_separately'] = 'No';
        }
        if ($type == 'grouped') {
            $productData = $this->loadDataSet('Product', $type . '_product_required',
                array('associated_search_sku' => $associatedData['general_sku']));
        } else {
            $productData = $this->loadDataSet('Product', $type . '_product_required');
            $productData['general_bundle_items']['item_1'] = $this->loadDataSet('Product', 'bundle_item_2',
                array('associated_search_sku' => $associatedData['general_sku']));
        }
        $search =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $associatedData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($associatedData, $associatedType);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($productData, $type);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');
    }

    public function deleteAssociatedProductDataProvider()
    {
        return array(
            array('simple', 'grouped'),
            array('virtual', 'grouped'),
            array('downloadable', 'grouped'),
            array('simple', 'bundle'),
            array('virtual', 'bundle')
        );
    }

    /**
     * <p>Delete several products.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3426
     */
    public function throughMassAction()
    {
        $productQty = 2;
        for ($i = 1; $i <= $productQty; $i++) {
            //Data
            $productData = $this->loadDataSet('Product', 'simple_product_required');
            ${'searchData' . $i} =
                $this->loadDataSet('Product', 'product_search', array('product_name' => $productData['general_sku']));
            //Steps
            $this->productHelper()->createProduct($productData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        for ($i = 1; $i <= $productQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i}, 'product_grid');
        }
        $this->addParameter('qtyDeletedProducts', $productQty);
        $this->fillDropdown('mass_action_select_action', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }
}
