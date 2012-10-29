<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Catalog_Product_AttributeControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @magentoDataFixture Mage/Catalog/controllers/_files/attribute_system.php
     * @dataProvider saveActionDataProviderSystem
     * @param array $postData
     * @return void
     */
    public function testSaveActionApplyToDataSystemAttribute($postData)
    {
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/admin/catalog_product_attribute/save');
        $model = new Mage_Catalog_Model_Resource_Eav_Attribute();
        $model->load($postData['attribute_id']);
        $this->assertNull($model->getData('apply_to'));
    }

    /**
     * @magentoDataFixture Mage/Catalog/controllers/_files/attribute_user_defined.php
     * @dataProvider saveActionDataProviderUserDefined
     * @param array $postData
     * @return void
     */
    public function testSaveActionApplyToDataUserDefinedAttribute($postData)
    {
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/admin/catalog_product_attribute/save');
        $model = new Mage_Catalog_Model_Resource_Eav_Attribute();
        $model->load($postData['attribute_id']);
        $this->assertEquals('simple,configurable', $model->getData('apply_to'));
    }

    public function saveActionDataProviderSystem()
    {
        return array(array(array(
            'attribute_id' => '2',
            'is_global' => '2',
            'default_value_text' => '0',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '0',
            'is_required' => '1',
            'frontend_class' => '',
            'is_configurable' => '0',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => '0',
            'is_comparable' => '0',
            'is_filterable' => '0',
            'is_filterable_in_search' => '0',
            'is_used_for_promo_rules' => '0',
            'is_html_allowed_on_front' => '0',
            'is_visible_on_front' => '0',
            'used_in_product_listing' => '1',
            'used_for_sort_by' => '0',
            'apply_to' => array(
        'simple', 'configurable'),
            'frontend_label' => array(
        0 => 'Allow Open Amount',
        1 => ''),
            'default' => array(
        0 => '0'),
            'option' => array(
                'delete' => array(
                    0 => '',
                    1 => ''))))
        );
    }

    public function saveActionDataProviderUserDefined()
    {
        return array(array(array(
            'attribute_id' => '1',
            'is_global' => '2',
            'default_value_text' => '0',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '0',
            'is_required' => '1',
            'frontend_class' => '',
            'is_configurable' => '0',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => '0',
            'is_comparable' => '0',
            'is_filterable' => '0',
            'is_filterable_in_search' => '0',
            'is_used_for_promo_rules' => '0',
            'is_html_allowed_on_front' => '0',
            'is_visible_on_front' => '0',
            'used_in_product_listing' => '1',
            'used_for_sort_by' => '0',
            'apply_to' => array(
                'simple', 'configurable'),
            'frontend_label' => array(
                0 => 'Allow Open Amount',
                1 => ''),
            'default' => array(
                0 => '0'),
            'option' => array(
                'delete' => array(
                    0 => '',
                    1 => ''))))
        );
    }
}
