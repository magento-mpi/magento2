<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner content per store view edit page
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Content extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * WYSIWYG config object
     *
     * @var Magento_Cms_Model_Wysiwyg_Config
     */
    protected $_wysiwygConfigModel;


    /**
     * WYSIWYG config data
     *
     * @var Magento_Object
     */
    protected $_wysiwygConfig;

    /**
     * Registry model
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * Application model
     *
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_App $app
     * @param Magento_Data_Form_Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_App $app,
        Magento_Data_Form_Factory $elementFactory,
        array $data = array()
    ) {
        $this->_wysiwygConfigModel = $wysiwygConfig;
        $this->_registryManager = $registry;
        $this->_app = $app;

        parent::__construct($context, $elementFactory, $data);
    }


    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare Banners Content Tab form, define Editor settings
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = $this->_createForm();
        $form->setHtmlIdPrefix('banner_content_');

        $model = $this->_registryManager->registry('current_banner');

        $this->_eventManager->dispatch(
            'adminhtml_banner_edit_tab_content_before_prepare_form',
            array('model' => $model, 'form' => $form)
        );

        $fieldsetHtmlClass = 'fieldset-wide';
        $fieldset = $this->_createDefaultContentFieldset($form, $fieldsetHtmlClass);

        if ($this->_app->isSingleStoreMode() == false) {
            $this->_createDefaultContentForStoresField($fieldset, $form, $model);
        }

        $this->_createStoreDefaultContentField($fieldset, $model, $form);

        if ($this->_app->isSingleStoreMode() == false) {
            $this->_createStoresContentFieldset($form, $model);

        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Create default content fieldset
     *
     * @param Magento_Data_Form $form
     * @param string $fieldsetHtmlClass
     * @return Magento_Data_Form_Element_Fieldset
     */
    protected function _createDefaultContentFieldset($form, $fieldsetHtmlClass)
    {
        $fieldset = $form->addFieldset('default_fieldset', array(
            'legend' => __('Default Content'),
            'class' => $fieldsetHtmlClass,
        ));
        return $fieldset;
    }

    /**
     * Get Wysiwyg Config
     *
     * @return Magento_Object
     */
    protected function _getWysiwygConfig()
    {
        if (is_null($this->_wysiwygConfig)) {
            $this->_wysiwygConfig = $this->_wysiwygConfigModel->getConfig(
                array(
                    'tab_id' => $this->getTabId(),
                    'skip_widgets' => array('Magento_Banner_Block_Widget_Banner'),
                )
            );
        }
        return $this->_wysiwygConfig;
    }

    /**
     * Create Store default content field
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param Magento_Banner_Model_Banner $model
     * @param Magento_Data_Form $form
     * @return Magento_Data_Form_Element_Abstract
     */
    protected function _createStoreDefaultContentField($fieldset, $model, $form)
    {
        $storeContents = $this->_registryManager->registry('current_banner')->getStoreContents();
        $isDisabled = (bool)$model->getIsReadonly() || ($model->getCanSaveAllStoreViewsContent() === false)
            || (isset($storeContents[0]) ? false : (!$model->getId() ? false : true));
        $isVisible = (bool)$model->getIsReadonly() || ($model->getCanSaveAllStoreViewsContent() === false);
        $afterHtml = '<script type="text/javascript">'
            . ($isVisible ? '$(\'buttons' . $form->getHtmlIdPrefix() . 'store_default_content\').hide(); ' : '')
            . (isset($storeContents[0]) ? '' : (!$model->getId() ? '' : '$(\'store_default_content\').hide();'))
            . '</script>';
        return $fieldset->addField('store_default_content', 'editor', array(
            'name' => 'store_contents[0]',
            'value' => (isset($storeContents[0]) ? $storeContents[0] : ''),
            'disabled' => $isDisabled,
            'config' => $this->_getWysiwygConfig(),
            'wysiwyg' => false,
            'container_id' => 'store_default_content',
            'after_element_html' =>  $afterHtml,
        ));
    }

    /**
     * Create default content for stores field
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param Magento_Data_Form $form
     * @param Magento_Banner_Model_Banner $model
     * @return Magento_Data_Form_Element_Abstract
     */
    protected function _createDefaultContentForStoresField($fieldset, $form, $model)
    {
        $storeContents = $this->_registryManager->registry('current_banner')->getStoreContents();
        $onclickScript = "$('store_default_content').toggle(); \n $('"
            . $form->getHtmlIdPrefix() . "store_default_content').disabled = !$('"
            . $form->getHtmlIdPrefix() . "store_default_content').disabled;";

        $afterHtml = '<label for="' . $form->getHtmlIdPrefix() . 'store_0_content_use">'
            . __('No Default Content') . '</label>';

        $isDisabled = (bool)$model->getIsReadonly() || ($model->getCanSaveAllStoreViewsContent() === false);

        return $fieldset->addField('store_0_content_use', 'checkbox', array(
            'name' => 'store_contents_not_use[0]',
            'required' => false,
            'label' => __('Banner Default Content for All Store Views'),
            'onclick' => $onclickScript,
            'checked' => isset($storeContents[0]) ? false : (!$model->getId() ? false : true),
            'after_element_html' => $afterHtml,
            'value' => 0,
            'fieldset_html_class' => 'store',
            'disabled' => $isDisabled
        ));
    }

    /**
     * Create fieldset that provides ability to change content per store view
     *
     * @param Magento_Data_Form $form
     * @param Magento_Banner_Model_Banner $model
     * @return Magento_Data_Form_Element_Fieldset
     */
    protected function _createStoresContentFieldset($form, $model)
    {
        $storeContents = $this->_registryManager->registry('current_banner')->getStoreContents();
        $fieldset = $form->addFieldset('scopes_fieldset', array(
            'legend' => __('Store View Specific Content'),
            'class' => 'store-scope',
        ));
        $renderer = $this->getLayout()->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset');
        $fieldset->setRenderer($renderer);
        $this->_getWysiwygConfig()->setUseContainer(true);
        foreach ($this->_app->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_label", 'note', array(
                'label' => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
                    'label' => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $storeContent = isset($storeContents[$store->getId()]) ? $storeContents[$store->getId()] : '';
                    $contentFieldId = 's_' . $store->getId() . '_content';
                    $wysiwygConfig = clone $this->_getWysiwygConfig();
                    $fieldset->addField('store_' . $store->getId() . '_content_use', 'checkbox', array(
                        'name' => 'store_contents_not_use[' . $store->getId() . ']',
                        'required' => false,
                        'label' => $store->getName(),
                        'onclick' => "$('{$contentFieldId}').toggle(); $('" . $form->getHtmlIdPrefix()
                            . $contentFieldId . "').disabled = !$('" . $form->getHtmlIdPrefix() . $contentFieldId
                            . "').disabled;",
                        'checked' => $storeContent ? false : true,
                        'after_element_html' => '<label for="' . $form->getHtmlIdPrefix()
                            . 'store_' . $store->getId() . '_content_use">'
                            . __('Use Default') . '</label>',
                        'value' => $store->getId(),
                        'fieldset_html_class' => 'store',
                        'disabled' => (bool)$model->getIsReadonly()
                    ));

                    $fieldset->addField($contentFieldId, 'editor', array(
                        'name' => 'store_contents[' . $store->getId() . ']',
                        'required' => false,
                        'disabled' => (bool)$model->getIsReadonly() || ($storeContent ? false : true),
                        'value' => $storeContent,
                        'container_id' => $contentFieldId,
                        'config' => $wysiwygConfig,
                        'wysiwyg' => false,
                        'after_element_html' =>
                            '<script type="text/javascript">' . ((bool)$model->getIsReadonly() ? '$(\'buttons'
                                . $form->getHtmlIdPrefix() . $contentFieldId . '\').hide(); ' : '')
                                . ($storeContent ? '' : '$(\'' . $contentFieldId . '\').hide();')
                                . '</script>',
                    ));
                }
            }
        }
        return $fieldset;
    }
}
