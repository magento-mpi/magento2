<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab;

use Magento\Backend\Block\Widget\Form;

/**
 * Reminder rules edit form email templates and labels fields
 */
class Templates
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Email Template Factory
     *
     * @var \Magento\Backend\Model\Config\Source\Email\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Backend\Model\Config\Source\Email\TemplateFactory $templateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Config\Source\Email\TemplateFactory $templateFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Prepare general properties form
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_reminder_rule');
        
        $fieldset = $form->addFieldset('email_fieldset', array(
            'legend' => __('Email Templates'),
            'class'  => 'tree-store-scope',
            'comment' => __('Only customers who registered on a store view will receive emails related to that store view.'),
        ));

        foreach ($this->_storeManager->getWebsites() as $website) {
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

        foreach ($this->_storeManager->getWebsites() as $website) {
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
        $template = $this->_templateFactory->create();
        $template->setPath(\Magento\Reminder\Model\Rule::XML_PATH_EMAIL_TEMPLATE);

        $options = $template->toOptionArray();
        array_unshift($options, array('value'=>'',
            'label' => __('-- Not Selected --'))
        );
        return $options;
    }
}
