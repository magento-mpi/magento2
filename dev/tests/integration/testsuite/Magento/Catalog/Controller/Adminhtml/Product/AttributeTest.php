<?php
/**
 * {license_notice}
 *
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
     * @return void
     */
    public function testWrongFrontendInput()
    {
        $postData = $this->_getAttributeData() + [
                'attribute_id' => 100500,
                'frontend_input' => 'some_input',
            ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product_attribute/edit/attribute_id/100500',
            $this->getResponse()->getHeader('Location')['value']
        );
        /** @var \Magento\Framework\Message\Collection $messages */
        $messages = $this->_objectManager->create('Magento\Framework\Message\ManagerInterface')->getMessages();
        $this->assertEquals(1, $messages->getCountByType('error'));
        $message = $messages->getItemsByType('error')[0];
        $this->assertEquals('Input type "some_input" not found in the input types list.', $message->getText());
    }

    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/attribute_system_popup.php
     * @return void
     */
    public function testWithPopup()
    {
        $postData = $this->_getAttributeData() + [
            'attribute_id' => 5,
            'popup' => 'true',
            'new_attribute_set_name' => 'new_attribute_set',
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product/addAttribute/attribute/5',
            $this->getResponse()->getHeader('Location')['value']
        );
        /** @var \Magento\Framework\Message\Collection $messages */
        $messages = $this->_objectManager->create('Magento\Framework\Message\ManagerInterface')->getMessages();
        $this->assertEquals(1, $messages->getCountByType('success'));
        $message = $messages->getItemsByType('success')[0];
        $this->assertEquals('You saved the product attribute.', $message->getText());
    }

    /**
     * @return void
     */
    public function testWithExceptionWhenSaveAttribute()
    {
        $postData = $this->_getAttributeData() + ['attribute_id' => 0, 'frontend_input' => 'boolean'];
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product_attribute/edit/attribute_id/0',
            $this->getResponse()->getHeader('Location')['value']
        );
        /** @var \Magento\Framework\Message\Collection $messages */
        $messages = $this->_objectManager->create('Magento\Framework\Message\ManagerInterface')->getMessages();
        $this->assertEquals(1, $messages->getCountByType('error'));
    }

    /**
     * @return void
     */
    public function testWrongAttributeId()
    {
        $postData = $this->_getAttributeData() + ['attribute_id' => 100500];
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product_attribute/index',
            $this->getResponse()->getHeader('Location')['value']
        );
        /** @var \Magento\Framework\Message\Collection $messages */
        $messages = $this->_objectManager->create('Magento\Framework\Message\ManagerInterface')->getMessages();
        $this->assertEquals(1, $messages->getCountByType('error'));
        /** @var \Magento\Framework\Message\Error $message */
        $message = $messages->getItemsByType('error')[0];
        $this->assertEquals('This attribute no longer exists.', $message->getText());
    }

    /**
     * @return void
     */
    public function testAttributeWithoutId()
    {
        $postData = $this->_getAttributeData() + [
                'attribute_code' => uniqid('attribute_'),
                'set' => 4,
                'frontend_input' => 'boolean',
            ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product_attribute/index',
            $this->getResponse()->getHeader('Location')['value']
        );
        /** @var \Magento\Framework\Message\Collection $messages */
        $messages = $this->_objectManager->create('Magento\Framework\Message\ManagerInterface')->getMessages();
        $this->assertEquals(1, $messages->getCountByType('success'));
        /** @var \Magento\Framework\Message\Success $message */
        $message = $messages->getItemsByType('success')[0];
        $this->assertEquals('You saved the product attribute.', $message->getText());
    }

    /**
     * @return void
     */
    public function testWrongAttributeCode()
    {
        $postData = $this->_getAttributeData() + array('attribute_id' => '2', 'attribute_code' => '_()&&&?');
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product_attribute/edit/attribute_id/2',
            $this->getResponse()->getHeader('Location')['value']
        );
        /** @var \Magento\Framework\Message\Collection $messages */
        $messages = $this->_objectManager->create('Magento\Framework\Message\ManagerInterface')->getMessages();
        $this->assertEquals(1, $messages->getCountByType('error'));
        /** @var \Magento\Framework\Message\Error $message */
        $message = $messages->getItemsByType('error')[0];
        $this->assertEquals(
            'Attribute code "_()&&&?" is invalid. Please use only letters (a-z),'
            . ' numbers (0-9) or underscore(_) in this field, first character should be a letter.',
            $message->getText()
        );
    }

    /**
     * @return void
     */
    public function testAttributeWithoutEntityTypeId()
    {
        $postData = $this->_getAttributeData() + array('attribute_id' => '2', 'new_attribute_set_name' => ' ');
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(
            'catalog/product_attribute/index',
            $this->getResponse()->getHeader('Location')['value']
        );
    }

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
     * @magentoDataFixture Magento/Translation/_files/db_translate_admin_store.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_enabled.php
     * @magentoDataFixture Magento/Catalog/controllers/_files/attribute_user_defined.php
     * @magentoAppIsolation enabled
     */
    public function testSaveActionCleanAttributeLabelCache()
    {
        /** @var \Magento\Translation\Model\Resource\String $string */
        $string = $this->_objectManager->create('Magento\Translation\Model\Resource\String');
        $this->assertEquals('predefined string translation', $this->_translate('string to translate'));
        $string->saveTranslate('string to translate', 'new string translation');
        $postData = $this->_getAttributeData() + array('attribute_id' => 1);
        $this->getRequest()->setPost($postData);
        $this->dispatch('backend/catalog/product_attribute/save');
        $this->assertEquals('new string translation', $this->_translate('string to translate'));
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        )->setDesignTheme(
            1
        );
        /** @var \Magento\Framework\TranslateInterface $translate */
        $translate = $this->_objectManager->get('Magento\Framework\TranslateInterface');
        $translate->loadData(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, true);
        return __($string);
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
            'frontend_label' => array(\Magento\Store\Model\Store::DEFAULT_STORE_ID => 'string to translate')
        );
    }
}
