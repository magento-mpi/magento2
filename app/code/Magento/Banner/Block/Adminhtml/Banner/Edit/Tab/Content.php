<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Banner content per store view edit page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab;

class Content extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * WYSIWYG config object
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfigModel;

    /**
     * WYSIWYG config data
     *
     * @var \Magento\Framework\Object
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfigModel = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
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
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare Banners Content Tab form, define Editor settings
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('banner_content_');

        $model = $this->_coreRegistry->registry('current_banner');

        $this->_eventManager->dispatch(
            'adminhtml_banner_edit_tab_content_before_prepare_form',
            ['model' => $model, 'form' => $form]
        );

        $fieldsetHtmlClass = 'fieldset-wide';
        $fieldset = $this->_createDefaultContentFieldset($form, $fieldsetHtmlClass);

        if ($this->_storeManager->isSingleStoreMode() == false) {
            $this->_createDefaultContentForStoresField($fieldset, $form, $model);
        }

        $this->_createStoreDefaultContentField($fieldset, $model, $form);

        if ($this->_storeManager->isSingleStoreMode() == false) {
            $this->_createStoresContentFieldset($form, $model);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Create default content fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param string $fieldsetHtmlClass
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _createDefaultContentFieldset($form, $fieldsetHtmlClass)
    {
        $fieldset = $form->addFieldset(
            'default_fieldset',
            ['legend' => __('Default Content'), 'class' => $fieldsetHtmlClass]
        );
        return $fieldset;
    }

    /**
     * Get Wysiwyg Config
     *
     * @return \Magento\Framework\Object
     */
    protected function _getWysiwygConfig()
    {
        if (is_null($this->_wysiwygConfig)) {
            $this->_wysiwygConfig = $this->_wysiwygConfigModel->getConfig(
                ['tab_id' => $this->getTabId(), 'skip_widgets' => ['Magento\Banner\Block\Widget\Banner']]
            );
        }
        return $this->_wysiwygConfig;
    }

    /**
     * Create Store default content field
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param \Magento\Banner\Model\Banner $model
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected function _createStoreDefaultContentField($fieldset, $model, $form)
    {
        $storeContents = $this->_coreRegistry->registry('current_banner')->getStoreContents();
        $isDisabled = (bool)$model->getIsReadonly() || $model->getCanSaveAllStoreViewsContent() === false || (isset(
            $storeContents[0]
        ) ? false : (!$model->getId() ? false : true));
        $isVisible = (bool)$model->getIsReadonly() || $model->getCanSaveAllStoreViewsContent() === false;
        $afterHtml = '<script type="text/javascript">' .
            ($isVisible ? '$(\'buttons' .
            $form->getHtmlIdPrefix() .
            'store_default_content\').hide(); ' : '') .
            (isset(
            $storeContents[0]
        ) ? '' : (!$model->getId() ? '' : '$(\'store_default_content\').hide();')) . '</script>';
        return $fieldset->addField(
            'store_default_content',
            'editor',
            [
                'name' => 'store_contents[0]',
                'value' => isset($storeContents[0]) ? $storeContents[0] : '',
                'disabled' => $isDisabled,
                'config' => $this->_getWysiwygConfig(),
                'wysiwyg' => false,
                'container_id' => 'store_default_content',
                'after_element_html' => $afterHtml
            ]
        );
    }

    /**
     * Create default content for stores field
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param \Magento\Framework\Data\Form $form
     * @param \Magento\Banner\Model\Banner $model
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected function _createDefaultContentForStoresField($fieldset, $form, $model)
    {
        $storeContents = $this->_coreRegistry->registry('current_banner')->getStoreContents();
        $onclickScript = "$('store_default_content').toggle(); \n $('" .
            $form->getHtmlIdPrefix() .
            "store_default_content').disabled = !$('" .
            $form->getHtmlIdPrefix() .
            "store_default_content').disabled;";
        foreach ($this->_storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $onclickScript .= ("\n $('" .
                    $form->getHtmlIdPrefix() . "store_0_content_use').checked == true" ?
                    "$('" . $form->getHtmlIdPrefix() .
                    "store_" . $store->getId() . "_content_use').checked = false;
                    $('" . $form->getHtmlIdPrefix() . "s_" . $store->getId() . "_content').disabled = false;
                    $('s_" . $store->getId() . "_content').show();" : "'';") .
                    "$('" . $form->getHtmlIdPrefix() .
                    "store_" . $store->getId() . "_content_use').disabled = $('" .
                    $form->getHtmlIdPrefix() . "store_0_content_use').checked;";
                }
            }
        }

        $afterHtml = '<label for="' . $form->getHtmlIdPrefix() . 'store_0_content_use">' . __(
            'No Default Content'
        ) . '</label>';

        $isDisabled = (bool)$model->getIsReadonly() || $model->getCanSaveAllStoreViewsContent() === false;

        return $fieldset->addField(
            'store_0_content_use',
            'checkbox',
            [
                'name' => 'store_contents_not_use[0]',
                'required' => false,
                'label' => __('Banner Default Content for All Store Views'),
                'onclick' => $onclickScript,
                'checked' => isset($storeContents[0]) ? false : (!$model->getId() ? false : true),
                'after_element_html' => $afterHtml,
                'value' => 0,
                'fieldset_html_class' => 'store',
                'disabled' => $isDisabled
            ]
        );
    }

    /**
     * Create fieldset that provides ability to change content per store view
     *
     * @param \Magento\Framework\Data\Form $form
     * @param \Magento\Banner\Model\Banner $model
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _createStoresContentFieldset($form, $model)
    {
        $storeContents = $this->_coreRegistry->registry('current_banner')->getStoreContents();
        $fieldset = $form->addFieldset(
            'scopes_fieldset',
            ['legend' => __('Store View Specific Content'), 'class' => 'store-scope']
        );
        $renderer = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');
        $fieldset->setRenderer($renderer);
        $this->_getWysiwygConfig()->setUseContainer(true);
        foreach ($this->_storeManager->getWebsites() as $website) {
            $fieldset->addField(
                "w_{$website->getId()}_label",
                'note',
                ['label' => $website->getName(), 'fieldset_html_class' => 'website']
            );
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField(
                    "sg_{$group->getId()}_label",
                    'note',
                    ['label' => $group->getName(), 'fieldset_html_class' => 'store-group']
                );
                foreach ($stores as $store) {
                    $storeContent = isset($storeContents[$store->getId()]) ? $storeContents[$store->getId()] : '';
                    $contentFieldId = 's_' . $store->getId() . '_content';
                    $wysiwygConfig = clone $this->_getWysiwygConfig();
                    $afterHtml = '<script type="text/javascript">' .
                        ("if ($('" . $form->getHtmlIdPrefix() . "store_0_content_use').checked) {" .
                            "$('" . $form->getHtmlIdPrefix() . "store_" . $store->getId() . "_content_use" .
                            "').disabled = true;" .
                            "$('" . $form->getHtmlIdPrefix() . "store_" . $store->getId() . "_content_use" .
                            "').checked = false;" .
                        "} else {" .
                            "$('" . $form->getHtmlIdPrefix() . "store_" . $store->getId() . "_content_use" .
                            "').disabled = false;}") .
                        '</script>';
                    $afterEditorHtml = '<script type="text/javascript">' .
                        ("if ('" . !$storeContent  . "') {" .
                            "if ($('" . $form->getHtmlIdPrefix() . "store_0_content_use').checked) {" .
                                "$('" . $contentFieldId . "').show();" .
                            "} else {" .
                                "$('" . $contentFieldId . "').hide();" .
                                "$('" . $form->getHtmlIdPrefix() . $contentFieldId . "').disabled = true;" .
                            "}" .
                        "} else if ('" . (bool)$model->getIsReadonly() . "') {" .
                            "$('buttons" . $form->getHtmlIdPrefix() . $contentFieldId . "').hide();" .
                        "}").
                        '</script>';
                    $fieldset->addField(
                        'store_' . $store->getId() . '_content_use',
                        'checkbox',
                        [
                            'name' => 'store_contents_not_use[' . $store->getId() . ']',
                            'required' => false,
                            'label' => $store->getName(),
                            'value' => $store->getId(),
                            'fieldset_html_class' => 'store',
                            'disabled' => (bool)$model->getIsReadonly(),
                            'onclick' => "\$('{$contentFieldId}').toggle(); \$('" .
                            $form->getHtmlIdPrefix() .
                            $contentFieldId .
                            "').disabled = !$('" .
                            $form->getHtmlIdPrefix() .
                            $contentFieldId .
                            "').disabled;",
                            'checked' => $storeContent ? false : true,
                            'after_element_html' => $afterHtml .
                            '<label for="' . $form->getHtmlIdPrefix() . 'store_' . $store->getId() .
                            '_content_use">' . __('Use Default') . '</label>'
                        ]
                    );

                    $fieldset->addField(
                        $contentFieldId,
                        'editor',
                        [
                            'name' => 'store_contents[' . $store->getId() . ']',
                            'required' => false,
                            'disabled' => (bool)$model->getIsReadonly(),
                            'value' => $storeContent,
                            'container_id' => $contentFieldId,
                            'config' => $wysiwygConfig,
                            'wysiwyg' => false,
                            'after_element_html' => $afterEditorHtml
                        ]
                    );
                }
            }
        }
        return $fieldset;
    }
}
