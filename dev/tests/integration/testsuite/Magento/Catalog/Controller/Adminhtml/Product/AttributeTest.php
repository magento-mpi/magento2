<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Controller\Adminhtml\Product;

/**
 * @magentoAppArea adminhtml
 */
class AttributeTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/attribute_system.php
     */
    public function testSaveActionApplyToDataSystemAttribute()
    {
        $postData = $this->_getAttributeData() + array('attribute_id' => '2');
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $model = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');
        $model->load($postData['attribute_id']);
        $this->assertNull($model->getData('apply_to'));
    }

    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/attribute_user_defined.php
     */
    public function testSaveActionApplyToDataUserDefinedAttribute()
    {
        $postData = $this->_getAttributeData() + array('attribute_id' => '1');
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $model */
        $model = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');
        $model->load($postData['attribute_id']);
        $this->assertEquals('simple', $model->getData('apply_to'));
    }

    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/attribute_system_with_applyto_data.php
     */
    public function testSaveActionApplyToData()
    {
        $postData = $this->_getAttributeData() + array('attribute_id' => '3');
        unset($postData['apply_to']);
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $model = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');
        $model->load($postData['attribute_id']);
        $this->assertEquals(array('simple'), $model->getApplyTo());
    }

    /**
     * @magentoDataFixture Magento/Core/_files/db_translate_admin_store.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_enabled.php
     * @magentoDataFixture Magento/Catalog/controllers/_files/attribute_user_defined.php
     * @magentoAppIsolation enabled
     */
    public function testSaveActionCleanAttributeLabelCache()
    {
        /** @var \Magento\Translate\Model\Resource\String $string */
        $string = $this->_objectManager->create('Magento\Translate\Model\Resource\Translate\String');
        $this->assertEquals($this->_translate('string to translate'), 'predefined string translation');
        $string->saveTranslate('string to translate', 'new string translation');
        $postData = $this->_getAttributeData() + array('attribute_id' => 1);
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals($this->_translate('string to translate'), 'new string translation');
    }

    /**
     * Return translation for a string literal belonging to backend area
     *
     * @param string $string
     * @return string
     */
    protected function _translate($string)
    {
        // emulate admin store and design
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\DesignInterface')
            ->setDesignTheme(1);
        /** @var \Magento\TranslateInterface $translate */
        $translate = $this->_objectManager->create('Magento\TranslateInterface');
        $translate->init(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, null, true);
        return $translate->translate(array($string));
    }

    /**
     * Get attribute data for post
     *
     * @return array
     */
    protected function _getAttributeData()
    {
        return array(
            'is_global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
            'default_value_text' => '0',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '0',
            'is_required' => '1',
            'frontend_class' => '',
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
            'apply_to' => array('simple'),
            'frontend_label' => array(
                \Magento\Core\Model\Store::DEFAULT_STORE_ID => 'string to translate',
            ),
        );
    }
}
