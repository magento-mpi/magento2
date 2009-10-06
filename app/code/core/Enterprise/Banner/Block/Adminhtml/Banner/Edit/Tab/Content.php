<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
        return Mage::helper('enterprise_banner')->__('Content');
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
     * Load Wysiwyg on demand and Prepare layout
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
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
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'tab_id' => $this->getTabId(),
            'skip_widgets' => array('enterprise_banner/widget_banner'),
        ));
        $fieldsetHtmlClass = 'fieldset-wide';

        // add default content fieldset
        $fieldset = $form->addFieldset('default_fieldset', array(
            'legend'       => Mage::helper('enterprise_banner')->__('Default Content'),
            'class'        => $fieldsetHtmlClass,
        ));
        $storeContents = $banner->getStoreContents();
        $fieldset->addField('store_default_content', 'editor', array(
            'name'     => 'store_contents[0]',
            'value'    => isset($storeContents[0]) ? $storeContents[0] : '',
            'config'   => $wysiwygConfig,
            'wysiwyg'  => false,
            'label'    => Mage::helper('enterprise_banner')->__('Banner Default Content for All Store Views'),
            'required' => true,
        ));

        // fieldset and content areas per store views
        $fieldset = $form->addFieldset('scopes_fieldset', array(
            'legend' => Mage::helper('enterprise_banner')->__('Store View Specific Content'),
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
                        'onclick'   => "$('{$contentFieldId}').toggle();",
                        'checked'   => $storeContent ? false : true,
                        'after_element_html' => '<label for="' . $form->getHtmlIdPrefix()
                            . 'store_' . $store->getId() .'_content_use">'
                            . Mage::helper('enterprise_banner')->__('Use Default') . '</label>',
                        'value'     => $store->getId(),
                        'fieldset_html_class' => 'store',
                    ));

                    $fieldset->addField($contentFieldId, 'editor', array(
                        'name'         => 'store_contents['.$store->getId().']',
                        'required'     => false,
                        'value'        => $storeContent,
                        'container_id' => $contentFieldId,
                        'config'       => $wysiwygConfig,
                        'wysiwyg'      => false,
                        'after_element_html' => $storeContent ? ''
                            : '<script type="text/javascript">$(\'' . $contentFieldId . '\').hide();</script>',
                    ));
                }
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
