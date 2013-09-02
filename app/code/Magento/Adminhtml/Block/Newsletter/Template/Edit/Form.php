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
class Magento_Adminhtml_Block_Newsletter_Template_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Retrieve template object
     *
     * @return Magento_Newsletter_Model_Template
     */
    public function getModel()
    {
        return Mage::registry('_current_template');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Adminhtml_Block_Newsletter_Template_Edit_Form
     */
    protected function _prepareForm()
    {
        $model  = $this->getModel();
        $identity = Mage::getStoreConfig(Magento_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY);
        $identityName = Mage::getStoreConfig('trans_email/ident_'.$identity.'/name');
        $identityEmail = Mage::getStoreConfig('trans_email/ident_'.$identity.'/email');

        $form   = $this->_createForm(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

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
        $wysiwygConfig = Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Config')->getConfig(array('widget_filters' => $widgetFilters));
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
