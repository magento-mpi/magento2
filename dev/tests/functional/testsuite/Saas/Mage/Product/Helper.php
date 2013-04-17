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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    #*********************************************************************************
    #*                         Test  Methods for creating product                    *
    #*********************************************************************************
    /**
     * Create Configurable product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createConfigurableProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['attribute_code'];
        $storeViewOptionsNames = array(
            $attrData['option_1']['store_view_titles']['Default Store View'],
            $attrData['option_2']['store_view_titles']['Default Store View'],
            $attrData['option_3']['store_view_titles']['Default Store View']
        );
        $adminOptionsNames = array(
            $attrData['option_1']['admin_option_name'],
            $attrData['option_2']['admin_option_name'],
            $attrData['option_3']['admin_option_name']
        );
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('general_categories' => $returnCategory['path']),
            array(
                'general_attribute_1' => $attrData['admin_title'],
                'var1_attr_value1'    => $adminOptionsNames[0],
                'var1_attr_value2'    => $adminOptionsNames[1],
                'var1_attr_value3'    => $adminOptionsNames[2]
            ));
        $associatedPr = $configurable['general_configurable_variations'];
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet(array('Product Details' => $attrCode));
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_products');
        $this->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'             => array(
                'product_name' => $associatedPr['configurable_1']['associated_name'],
                'product_sku'  => $associatedPr['configurable_1']['associated_sku']
            ),
            'virtual'            => array(
                'product_name' => $associatedPr['configurable_2']['associated_name'],
                'product_sku'  => $associatedPr['configurable_2']['associated_sku']
            ),
            'configurable'       => array(
                'product_name' => $configurable['general_name'],
                'product_sku'  => $configurable['general_sku']
            ),
            'simpleOption'       => array(
                'option'       => $adminOptionsNames[0],
                'option_front' => $storeViewOptionsNames[0]
            ),
            'virtualOption'      => array(
                'option'       => $adminOptionsNames[1],
                'option_front' => $storeViewOptionsNames[1]
            ),
            'configurableOption' => array(
                'title'                  => $attrData['admin_title'],
                'custom_option_dropdown' => $storeViewOptionsNames[0]
            ),
            'attribute'          => array(
                'title'       => $attrData['admin_title'],
                'title_front' => $attrData['store_view_titles']['Default Store View'],
                'code'        => $attrCode
            ),
            'category'           => $returnCategory
        );
    }

    /**
     * Create Grouped product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createGroupedProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $productCat = array('general_categories' => $returnCategory['path']);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', $productCat,
            array(
                'associated_1' => $simple['general_sku'],
                'associated_2' => $virtual['general_sku'],
            ));
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'        => array(
                'product_name' => $simple['general_name'],
                'product_sku'  => $simple['general_sku']
            ),
            'virtual'       => array(
                'product_name' => $virtual['general_name'],
                'product_sku'  => $virtual['general_sku']
            ),
            'grouped'       => array(
                'product_name' => $grouped['general_name'],
                'product_sku'  => $grouped['general_sku']
            ), 'category'   => $returnCategory,
            'groupedOption' => array(
                'subProduct_1' => $simple['general_name'],
                'subProduct_2' => $virtual['general_name'],
            )
        );
    }
}
