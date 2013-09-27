<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system template edit form
 */

class Magento_Adminhtml_Block_System_Email_Template_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Magento_Core_Model_Source_Email_Variables
     */
    protected $_variables;

    /**
     * @var Magento_Core_Model_VariableFactory
     */
    protected $_variableFactory;

    /**
     * @param Magento_Core_Model_VariableFactory $variableFactory
     * @param Magento_Core_Model_Source_Email_Variables $variables
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_VariableFactory $variableFactory,
        Magento_Core_Model_Source_Email_Variables $variables,
        Magento_Backend_Model_Session $backendSession,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_variableFactory = $variableFactory;
        $this->_variables = $variables;
        $this->_backendSession = $backendSession;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare layout.
     * Add files to use dialog windows
     *
     * @return Magento_Adminhtml_Block_System_Email_Template_Edit_Form
     */
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addChild(
                'prototype-window-js',
                'Magento_Page_Block_Html_Head_Script',
                array(
                    'file' => 'prototype/window.js'
                )
            );
            $head->addChild(
                'prototype-windows-themes-default-css',
                'Magento_Page_Block_Html_Head_Css',
                array(
                    'file' => 'prototype/windows/themes/default.css'
                )
            );
            $head->addChild(
                'magento-core-prototype-magento-css',
                'Magento_Page_Block_Html_Head_Css',
                array(
                    'file' => 'Magento_Core::prototype/magento.css'
                )
            );
            $head->addChild(
                'magento-adminhtml-variables-js',
                'Magento_Page_Block_Html_Head_Script',
                array(
                    'file' => 'Magento_Adminhtml::variables.js'
                )
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Add fields to form and create template info form
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Template Information'),
            'class' => 'fieldset-wide'
        ));

        $templateId = $this->getEmailTemplate()->getId();
        if ($templateId) {
            $fieldset->addField('used_currently_for', 'label', array(
                'label' => __('Used Currently For'),
                'container_id' => 'used_currently_for',
                'after_element_html' =>
                    '<script type="text/javascript">' .
                    (!$this->getEmailTemplate()->getSystemConfigPathsWhereUsedCurrently()
                        ? '$(\'' . 'used_currently_for' . '\').hide(); ' : '') .
                    '</script>',
            ));
        }

        if (!$templateId) {
            $fieldset->addField('used_default_for', 'label', array(
                'label' => __('Used as Default For'),
                'container_id' => 'used_default_for',
                'after_element_html' =>
                    '<script type="text/javascript">' .
                    (!(bool)$this->getEmailTemplate()->getOrigTemplateCode()
                        ? '$(\'' . 'used_default_for' . '\').hide(); ' : '') .
                    '</script>',
            ));
        }

        $fieldset->addField('template_code', 'text', array(
            'name'=>'template_code',
            'label' => __('Template Name'),
            'required' => true

        ));

        $fieldset->addField('template_subject', 'text', array(
            'name'=>'template_subject',
            'label' => __('Template Subject'),
            'required' => true
        ));

        $fieldset->addField('orig_template_variables', 'hidden', array(
            'name' => 'orig_template_variables',
        ));

        $fieldset->addField('variables', 'hidden', array(
            'name' => 'variables',
            'value' => Zend_Json::encode($this->getVariables())
        ));

        $fieldset->addField('template_variables', 'hidden', array(
            'name' => 'template_variables',
        ));

        $insertVariableButton = $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Widget_Button', '', array('data' => array(
                'type' => 'button',
                'label' => __('Insert Variable...'),
                'onclick' => 'templateControl.openVariableChooser();return false;'
            )));

        $fieldset->addField('insert_variable', 'note', array(
            'text' => $insertVariableButton->toHtml()
        ));

        $fieldset->addField('template_text', 'textarea', array(
            'name'=>'template_text',
            'label' => __('Template Content'),
            'title' => __('Template Content'),
            'required' => true,
            'style' => 'height:24em;',
        ));

        if (!$this->getEmailTemplate()->isPlain()) {
            $fieldset->addField('template_styles', 'textarea', array(
                'name'=>'template_styles',
                'label' => __('Template Styles'),
                'container_id' => 'field_template_styles'
            ));
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
     * @return Magento_Core_Model_Email_Template
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
        /* @var $template Magento_Core_Model_Email_Template */
        $template = $this->_coreRegistry->registry('current_email_template');
        if ($template->getId() && $templateVariables = $template->getVariablesOptionArray(true)) {
            $variables[] = $templateVariables;
        }
        return $variables;
    }
}
