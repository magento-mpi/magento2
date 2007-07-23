<?php
/**
 * Adminhtml newsletter queue edit form
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm() 
	{
		$queue = Mage::getSingleton('newsletter/queue');
			
		$queue->addTemplateData($queue);
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Queue General')));
		
		if($queue->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
			$fieldset->addField('date','date',array(
				'name'	  =>	'start_at',
				'time'	  =>	true,
				'label'	  =>	__('Start This Queue At'),
				'image'	  =>	$this->getSkinUrl('images/grid-cal.gif'),
				'value'	  => 	$queue->getQueueStartAt(),
				'title'	  =>	__('Queue Start Date')
			));			
		}
		
		$fieldset->addField('subject', 'text', array(
            'name'=>'subject',
            'label' => __('Subject'),
            'title' => __('Subject'),
            'class'	=> 'required-entry',
            'required' => true,
            'value' => $queue->getTemplate()->getTemplateSubject() 
        ));
		
		$fieldset->addField('sender_name', 'text', array(
            'name'=>'sender_name',
            'label' => __('Sender Name'),
            'title' => __('Sender Name'),
            'class'	=> 'required-entry',
            'required' => true,
            'value' => $queue->getTemplate()->getTemplateSenderName()
        ));
        
        $fieldset->addField('sender_email', 'text', array(
            'name'=>'sender_email',
            'label' => __('Sender Email'),
            'title' => __('Sender Email'),
            'class' => 'validate-email required-entry',
            'required' => true,
            'value' => $queue->getTemplate()->getTemplateSenderEmail()   
        )); 
		
		$fieldset->addField('text','editor', array(
			'name'	  =>	'text',
			'wysiwyg' =>    !$queue->getTemplate()->isPlain(),
			'label'	  =>	__('Message'),
			'title'	  =>	__('Message'),
			'theme'	  => 	'advanced',
			'value'	  =>    $queue->getTemplate()->getTemplateTextPreprocessed()
		));
		
		
	/*	
		$form->getElement('template')->setRenderer(
			$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form_renderer_template')
		);
		*/
		
		
		$this->setForm($form);
		return parent::_prepareForm();
	}
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form END
