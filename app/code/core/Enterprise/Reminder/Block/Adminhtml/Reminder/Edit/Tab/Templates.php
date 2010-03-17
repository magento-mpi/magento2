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
 * @package     Enterprise_Reminder
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Templates
    extends Enterprise_Enterprise_Block_Adminhtml_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Templates
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $model = Mage::registry('current_reminder_rule');
        $model->getTemplates();

        $fieldset = $form->addFieldset('email_fieldset', array(
            'legend' => Mage::helper('enterprise_reminder')->__('E-mail Templates'),
            'table_class'  => 'form-list stores-tree'
        ));

        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("website_{$website->getId()}_template", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("store_{$group->getId()}_template", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField('store_template_'.$store->getId(), 'select', array(
                        'name'      => 'store_templates['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'values'    => $this->getTemplatesOptionsArray(),
                        'fieldset_html_class' => 'store'
                    ));
                }
            }
        }

        $fieldset = $form->addFieldset('default_label_fieldset', array(
            'legend' => Mage::helper('enterprise_reminder')->__('Default Labels and Description')
        ));

        $fieldset->addField('store_default_label', 'text', array(
            'name'      => 'store_labels[0]',
            'required'  => false,
            'label'     => Mage::helper('enterprise_reminder')->__('Default Rule Label for All Store Views')
        ));

        $fieldset->addField('store_default_desctiption', 'textarea', array(
            'name'      => 'store_descriptions[0]',
            'required'  => false,
            'label'     => Mage::helper('enterprise_reminder')->__('Default Rule Description for All Store Views'),
            'style' => 'width: 98%; height: 50px;'
        ));

        $fieldset = $form->addFieldset('labels_fieldset', array(
            'legend' => Mage::helper('enterprise_reminder')->__('Labels per Store View'),
            'table_class'  => 'form-list stores-tree'
        ));

        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("website_{$website->getId()}_label", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("store_{$group->getId()}_label", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField('store_label_'.$store->getId(), 'text', array(
                        'name'      => 'store_labels['.$store->getId().']',
                        'label'     => $store->getName(),
                        'required'  => false,
                        'fieldset_html_class' => 'store'
                    ));
                }
            }
        }

        $fieldset = $form->addFieldset('descriptions_fieldset', array(
            'legend' => Mage::helper('enterprise_reminder')->__('Descriptions per Store View'),
            'table_class'  => 'form-list stores-tree'
        ));

        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("website_{$website->getId()}_description", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("store_{$group->getId()}_description", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField('store_description_'.$store->getId(), 'textarea', array(
                        'name'      => 'store_descriptions['.$store->getId().']',
                        'label'     => $store->getName(),
                        'required'  => false,
                        'fieldset_html_class' => 'store',
                        'style' => 'width: 98%; height: 50px;'
                    ));
                }
            }
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve array of available email templates
     *
     * @return array
     */
    public function getTemplatesOptionsArray()
    {
        $template = Mage::getModel('adminhtml/system_config_source_email_template');
        $template->setPath(Enterprise_Reminder_Model_Rule::XML_PATH_EMAIL_TEMPLATE);

        $options = $template->toOptionArray();
        array_unshift($options, array('value'=>'',
            'label'=>Mage::helper('enterprise_reminder')->__('-- Please select --'))
        );
        return $options;
    }
}
