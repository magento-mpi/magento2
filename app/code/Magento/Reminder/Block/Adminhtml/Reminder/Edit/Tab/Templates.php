<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder rules edit form email templates and labels fields
 */
class Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Templates
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Templates
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();
        $model = Mage::registry('current_reminder_rule');

        $fieldset = $form->addFieldset('email_fieldset', array(
            'legend' => __('Email Templates'),
            'class'  => 'tree-store-scope',
            'comment' => __('Only customers who registered on a store view will receive emails related to that store view.'),
        ));

        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("website_template_{$website->getId()}", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("group_template_{$group->getId()}", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField('store_template_' . $store->getId(), 'select', array(
                        'name'      => 'store_templates[' . $store->getId() . ']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'values'    => $this->getTemplatesOptionsArray(),
                        'fieldset_html_class' => 'store'
                    ));
                }
            }
        }

        $fieldset = $form->addFieldset('default_label_fieldset', array(
            'legend' => __('Default Titles and Description'),
            'comment' => __('You can find and edit rule label and descriptions, per store view, in email templates.'),
        ));

        $fieldset->addField('default_label', 'text', array(
            'name'      => 'default_label',
            'required'  => false,
            'label'     => __('Rule Title for All Store Views')
        ));

        $fieldset->addField('default_description', 'textarea', array(
            'name'     => 'default_description',
            'required' => false,
            'label'    => __('Rule Description for All Store Views'),
            'style'    => 'height: 50px;'
        ));

        $fieldset = $form->addFieldset('labels_fieldset', array(
            'legend' => __('Titles and Descriptions Per Store View'),
            'comment' => __('Overrides default titles and descriptions. Note that if email an template is not specified for this store view, the respective variable values will be deleted.'),
            'class'  => 'tree-store-scope'
        ));

        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("website_label_{$website->getId()}", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("group_label_{$group->getId()}", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField('store_label_' . $store->getId(), 'text', array(
                        'name'      => 'store_labels[' . $store->getId() . ']',
                        'label'     => $store->getName(),
                        'required'  => false,
                        'fieldset_html_class' => 'store'
                    ));
                     $fieldset->addField('store_description_' . $store->getId(), 'textarea', array(
                        'name'      => 'store_descriptions[' . $store->getId() . ']',
                        'required'  => false,
                        'fieldset_html_class' => 'store',
                        'style' => 'height: 50px;'
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
        $template = Mage::getModel('Magento_Backend_Model_Config_Source_Email_Template');
        $template->setPath(Magento_Reminder_Model_Rule::XML_PATH_EMAIL_TEMPLATE);

        $options = $template->toOptionArray();
        array_unshift($options, array('value'=>'',
            'label' => __('-- Not Selected --'))
        );
        return $options;
    }
}
