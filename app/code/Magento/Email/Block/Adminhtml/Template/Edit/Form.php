<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template edit form
 */
namespace Magento\Email\Block\Adminhtml\Template\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Email\Model\Source\Variables
     */
    protected $_variables;

    /**
     * @var \Magento\Core\Model\VariableFactory
     */
    protected $_variableFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\VariableFactory $variableFactory
     * @param \Magento\Email\Model\Source\Variables $variables
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Core\Model\VariableFactory $variableFactory,
        \Magento\Email\Model\Source\Variables $variables,
        array $data = array()
    ) {
        $this->_variableFactory = $variableFactory;
        $this->_variables = $variables;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare layout.
     * Add files to use dialog windows
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addChild(
                'prototype-window-js',
                'Magento\Theme\Block\Html\Head\Script',
                array('file' => 'prototype/window.js')
            );
            $head->addChild(
                'prototype-windows-themes-default-css',
                'Magento\Theme\Block\Html\Head\Css',
                array('file' => 'prototype/windows/themes/default.css')
            );
            $head->addChild(
                'magento-core-prototype-magento-css',
                'Magento\Theme\Block\Html\Head\Css',
                array('file' => 'Magento_Core::prototype/magento.css')
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Add fields to form and create template info form
     *
     * @return \Magento\Backend\Block\Widget\Form
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('Template Information'), 'class' => 'fieldset-wide')
        );

        $templateId = $this->getEmailTemplate()->getId();
        if ($templateId) {
            $fieldset->addField(
                'used_currently_for',
                'label',
                array(
                    'label' => __('Used Currently For'),
                    'container_id' => 'used_currently_for',
                    'after_element_html' => '<script type="text/javascript">' .
                    (!$this->getEmailTemplate()->getSystemConfigPathsWhereUsedCurrently() ? '$(\'' .
                    'used_currently_for' .
                    '\').hide(); ' : '') .
                    '</script>'
                )
            );
        }

        if (!$templateId) {
            $fieldset->addField(
                'used_default_for',
                'label',
                array(
                    'label' => __('Used as Default For'),
                    'container_id' => 'used_default_for',
                    'after_element_html' => '<script type="text/javascript">' .
                    (!(bool)$this->getEmailTemplate()->getOrigTemplateCode() ? '$(\'' .
                    'used_default_for' .
                    '\').hide(); ' : '') .
                    '</script>'
                )
            );
        }

        $fieldset->addField(
            'template_code',
            'text',
            array('name' => 'template_code', 'label' => __('Template Name'), 'required' => true)
        );
        $fieldset->addField(
            'template_subject',
            'text',
            array('name' => 'template_subject', 'label' => __('Template Subject'), 'required' => true)
        );
        $fieldset->addField('orig_template_variables', 'hidden', array('name' => 'orig_template_variables'));
        $fieldset->addField(
            'variables',
            'hidden',
            array('name' => 'variables', 'value' => \Zend_Json::encode($this->getVariables()))
        );
        $fieldset->addField('template_variables', 'hidden', array('name' => 'template_variables'));

        $insertVariableButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button',
            '',
            array(
                'data' => array(
                    'type' => 'button',
                    'label' => __('Insert Variable...'),
                    'onclick' => 'templateControl.openVariableChooser();return false;'
                )
            )
        );

        $fieldset->addField('insert_variable', 'note', array('text' => $insertVariableButton->toHtml()));

        $fieldset->addField(
            'template_text',
            'textarea',
            array(
                'name' => 'template_text',
                'label' => __('Template Content'),
                'title' => __('Template Content'),
                'required' => true,
                'style' => 'height:24em;'
            )
        );

        if (!$this->getEmailTemplate()->isPlain()) {
            $fieldset->addField(
                'template_styles',
                'textarea',
                array(
                    'name' => 'template_styles',
                    'label' => __('Template Styles'),
                    'container_id' => 'field_template_styles'
                )
            );
        }

        if ($templateId) {
            $form->addValues($this->getEmailTemplate()->getData());
        }

        $values = $this->_backendSession->getData('email_template_form_data', true);
        if ($values) {
            $form->setValues($values);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return current email template model
     *
     * @return \Magento\Email\Model\Template
     */
    public function getEmailTemplate()
    {
        return $this->_coreRegistry->registry('current_email_template');
    }

    /**
     * Retrieve variables to insert into email
     *
     * @return array
     */
    public function getVariables()
    {
        $variables = array();
        $variables[] = $this->_variables->toOptionArray(true);
        $customVariables = $this->_variableFactory->create()->getVariablesOptionArray(true);
        if ($customVariables) {
            $variables[] = $customVariables;
        }
        /* @var $template \Magento\Email\Model\Template */
        $template = $this->_coreRegistry->registry('current_email_template');
        if ($template->getId() && ($templateVariables = $template->getVariablesOptionArray(true))) {
            $variables[] = $templateVariables;
        }
        return $variables;
    }
}
