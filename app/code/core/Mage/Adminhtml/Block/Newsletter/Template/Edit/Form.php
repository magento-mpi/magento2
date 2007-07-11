<?php
/** 
 * Adminhtml newsletter template edit form
 * 
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
     /** 
     * Constructor
     * 
     * Initialize form
     */
    public function __construct() 
    {
        parent::__construct();
        
    }
    
   
    /**
     * Prepare form for render
     */
    public function renderPrepare($template)
    {
        $form = new Varien_Data_Form();
        
        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if (isset($post['template_id'])) {
                unset($post['template_id']);
            } 
            
            if (isset($post['template_type'])) { 
                unset($post['template_type']);
            }
            
            $template->addData($post);
        }
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Template general')));
        
        $fieldset->addField('code', 'text', array(
            'name'=>'code',
            'label' => __('template code'),
            'title' => __('template code title'),
            'class' => 'required-entry validate-alphanum',
             'value' => $template->getTemplateCode()   
        ));
        
        $fieldset->addField('subject', 'text', array(
            'name'=>'subject',
            'label' => __('template subject'),
            'title' => __('template subject title'),
            'value' => $template->getTemplateSubject()            
        ));
        
        $fieldset->addField('sender_name', 'text', array(
            'name'=>'sender_name',
            'label' => __('sender name'),
            'title' => __('sender name title'),
            'value' => $template->getTemplateSenderName()
        ));
        
        $fieldset->addField('sender_email', 'text', array(
            'name'=>'sender_email',
            'label' => __('sender email'),
            'title' => __('sender email title'),
            'class' => 'validate-email',
            'value' => $template->getTemplateSenderEmail()   
        ));       
        
        $txtType = constant(Mage::getConfig()->getModelClassName('newsletter/template') . '::TYPE_TEXT');
        
        $fieldset->addField('text', 'editor', array(
            'name'=>'text',
            'wysiwyg' => ($template->getTemplateType() != $txtType),
            'label' => __('template content'),
            'title' => __('template content title'),
            'cols' => 20,
            'rows' => 15,
            'theme' => 'advanced',
            'value' => $template->getTemplateText()
        ));
        
        if ($template->getId()) { 
            // If edit add id
            $form->addField('id', 'hidden', 
                array(
                    'name'  => 'id',
                    'value' => $template->getId()
                )
            );
        }
        
        $this->setForm($form);
        
        return $this;
    }
}