<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner content per store view edit page
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Enterprise_Banner_Helper_Data')->__('Content');
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
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $banner = Mage::registry('current_banner');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('banner_content_');
        $wysiwygConfig = Mage::getSingleton('Mage_Cms_Model_Wysiwyg_Config')->getConfig(array(
            'tab_id' => $this->getTabId(),
            'skip_widgets' => array('Enterprise_Banner_Block_Widget_Banner'),
        ));
        $fieldsetHtmlClass = 'fieldset-wide';

        $storeContents = $banner->getStoreContents();
        $model = Mage::registry('current_banner');

        Mage::dispatchEvent('adminhtml_banner_edit_tab_content_before_prepare_form', 
            array('model' => $model, 'form' => $form)
        );

        // add default content fieldset
        $fieldset = $form->addFieldset('default_fieldset', array(
            'legend'       => Mage::helper('Enterprise_Banner_Helper_Data')->__('Default Content'),
            'class'        => $fieldsetHtmlClass,
        ));

        $fieldset->addField('store_0_content_use', 'checkbox', array(
            'name'      => 'store_contents_not_use[0]',
            'required'  => false,
            'label'    => Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Default Content for All Store Views'),
            'onclick'   => "$('store_default_content').toggle();
                $('" . $form->getHtmlIdPrefix() . "store_default_content').disabled = !$('" . $form->getHtmlIdPrefix() . "store_default_content').disabled;",
            'checked'   => isset($storeContents[0]) ? false : (!$model->getId() ? false : true),
            'after_element_html' => '<label for="' . $form->getHtmlIdPrefix()
                . 'store_0_content_use">'
                . Mage::helper('Enterprise_Banner_Helper_Data')->__('No Default Content') . '</label>',
            'value'     => 0,
            'fieldset_html_class' => 'store',
            'disabled'  => (bool)$model->getIsReadonly() || ($model->getCanSaveAllStoreViewsContent() === false)
        ));

        $field = $fieldset->addField('store_default_content', 'editor', array(
            'name'     => 'store_contents[0]',
            'value'    => (isset($storeContents[0]) ? $storeContents[0] : ''),
            'disabled' => (bool)$model->getIsReadonly() ||
                          ($model->getCanSaveAllStoreViewsContent() === false) ||
                          (isset($storeContents[0]) ? false : (!$model->getId() ? false : true)),
            'config'   => $wysiwygConfig,
            'wysiwyg'  => false,
            'container_id' => 'store_default_content',
            'after_element_html' =>
                '<script type="text/javascript">' .
                ((bool)$model->getIsReadonly() || ($model->getCanSaveAllStoreViewsContent() === false) ? '$(\'buttons' . $form->getHtmlIdPrefix() . 'store_default_content\').hide(); ' : '') .
                (isset($storeContents[0]) ? '' : (!$model->getId() ? '' : '$(\'store_default_content\').hide();')) .
                '</script>',
        ));

        // fieldset and content areas per store views
        $fieldset = $form->addFieldset('scopes_fieldset', array(
            'legend' => Mage::helper('Enterprise_Banner_Helper_Data')->__('Store View Specific Content'),
            'class'  => $fieldsetHtmlClass,
            'table_class' => 'form-list stores-tree',
        ));
        $wysiwygConfig->setUseContainer(true);
        foreach (Mage::app()->getWebsites() as $website) {
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
                    $contentFieldId = 's_'.$store->getId().'_content';
                    $wysiwygConfig = clone $wysiwygConfig;
                    $fieldset->addField('store_'.$store->getId().'_content_use', 'checkbox', array(
                        'name'      => 'store_contents_not_use['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'onclick'   => "$('{$contentFieldId}').toggle(); $('" . $form->getHtmlIdPrefix() . $contentFieldId . "').disabled = !$('" . $form->getHtmlIdPrefix() . $contentFieldId . "').disabled;",
                        'checked'   => $storeContent ? false : true,
                        'after_element_html' => '<label for="' . $form->getHtmlIdPrefix()
                            . 'store_' . $store->getId() .'_content_use">'
                            . Mage::helper('Enterprise_Banner_Helper_Data')->__('Use Default') . '</label>',
                        'value'     => $store->getId(),
                        'fieldset_html_class' => 'store',
                        'disabled'  => (bool)$model->getIsReadonly()
                    ));

                    $fieldset->addField($contentFieldId, 'editor', array(
                        'name'         => 'store_contents['.$store->getId().']',
                        'required'     => false,
                        'disabled'     => (bool)$model->getIsReadonly() || ($storeContent ? false : true),
                        'value'        => $storeContent,
                        'container_id' => $contentFieldId,
                        'config'       => $wysiwygConfig,
                        'wysiwyg'      => false,
                        'after_element_html' =>
                            '<script type="text/javascript">' .
                            ((bool)$model->getIsReadonly() ? '$(\'buttons' . $form->getHtmlIdPrefix() . $contentFieldId . '\').hide(); ' : '') .
                            ($storeContent ? '' : '$(\'' . $contentFieldId . '\').hide();') .
                            '</script>',
                    ));
                }
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
