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
 * Adminhtml Newsletter Template Edit Form Block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Newsletter_Template_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Cms_Model_Wysiwyg_Config
     */
    protected $_wysiwygConfig;

    /**
     * @param Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Retrieve template object
     *
     * @return Magento_Newsletter_Model_Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_current_template');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Adminhtml_Block_Newsletter_Template_Edit_Form
     */
    protected function _prepareForm()
    {
        $model  = $this->getModel();
        $identity = $this->_storeConfig->getConfig(
            Magento_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY
        );
        $identityName = $this->_storeConfig->getConfig('trans_email/ident_'.$identity.'/name');
        $identityEmail = $this->_storeConfig->getConfig('trans_email/ident_'.$identity.'/email');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => __('Template Information'),
            'class'     => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $model->getId(),
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => __('Template Name'),
            'title'     => __('Template Name'),
            'required'  => true,
            'value'     => $model->getTemplateCode(),
        ));

        $fieldset->addField('subject', 'text', array(
            'name'      => 'subject',
            'label'     => __('Template Subject'),
            'title'     => __('Template Subject'),
            'required'  => true,
            'value'     => $model->getTemplateSubject(),
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name'      =>'sender_name',
            'label'     => __('Sender Name'),
            'title'     => __('Sender Name'),
            'required'  => true,
            'value'     => $model->getId() !== null
                ? $model->getTemplateSenderName()
                : $identityName,
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name'      =>'sender_email',
            'label'     => __('Sender Email'),
            'title'     => __('Sender Email'),
            'class'     => 'validate-email',
            'required'  => true,
            'value'     => $model->getId() !== null
                ? $model->getTemplateSenderEmail()
                : $identityEmail
        ));


        $widgetFilters = array('is_email_compatible' => 1);
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(array('widget_filters' => $widgetFilters));
        if ($model->isPlain()) {
            $wysiwygConfig->setEnabled(false);
        }
        $fieldset->addField('text', 'editor', array(
            'name'      => 'text',
            'label'     => __('Template Content'),
            'title'     => __('Template Content'),
            'required'  => true,
            'state'     => 'html',
            'style'     => 'height:36em;',
            'value'     => $model->getTemplateText(),
            'config'    => $wysiwygConfig
        ));

        if (!$model->isPlain()) {
            $fieldset->addField('template_styles', 'textarea', array(
                'name'          =>'styles',
                'label'         => __('Template Styles'),
                'container_id'  => 'field_template_styles',
                'value'         => $model->getTemplateStyles()
            ));
        }

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
