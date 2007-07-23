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
		
		$fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Queue Information')));
		
		if($queue->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
			$fieldset->addField('date','date',array(
				'name'	  =>	'start_at',
				'time'	  =>	true,
				'label'	  =>	__('Start This Queue At'),
				'image'	  =>	$this->getSkinUrl('images/grid-cal.gif'),
				'value'	  => 	$queue->getQueueStartAt(),
				'title'	  =>	__('Queue Start Date')
			));
			
			$fieldset->addField('stores','multiselect',array(
				'name'	  =>	'stores[]',
				'time'	  =>	true,
				'label'	  =>	__('Subscribers From'),
				'image'	  =>	$this->getSkinUrl('images/grid-cal.gif'),
				'value'	  => 	$queue->getQueueStores(),
				'title'	  =>	__('Subscribers From'),
				'values'  =>    Mage::getResourceSingleton('core/store_collection')->load()->toOptionArray(),
				'value'	  =>	$queue->getStores(),
				'select_all' => __('Select All Stores'),
				'deselect_all' => __('Deselect All Stores'),
			));			
		} else {
			$fieldset->addField('date','date',array(
				'name'	  =>	'start_at',
				'time'	  =>	true,
				'disabled'=> 	'true',
				'label'	  =>	__('Start This Queue At'),
				'image'	  =>	$this->getSkinUrl('images/grid-cal.gif'),
				'value'	  => 	$queue->getQueueStartAt(),
				'title'	  =>	__('Queue Start Date')
			));
			
			$fieldset->addField('stores','multiselect',array(
				'name'	  =>	'stores[]',
				'time'	  =>	true,
				'disabled'=> 	'true',
				'label'	  =>	__('Subscribers From'),
				'image'	  =>	$this->getSkinUrl('images/grid-cal.gif'),
				'value'	  => 	$queue->getQueueStores(),
				'title'	  =>	__('Subscribers From'),
				'values'  =>    Mage::getResourceSingleton('core/store_collection')->load()->toOptionArray(),
				'value'	  =>	$queue->getStores()
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
		
        if(in_array($queue->getQueueStatus(), array(Mage_Newsletter_Model_Queue::STATUS_NEVER, Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
			$fieldset->addField('text','editor', array(
				'name'	  =>	'text',
				'wysiwyg' =>    !$queue->getTemplate()->isPlain(),
				'label'	  =>	__('Message'),
				'title'	  =>	__('Message'),
				'theme'	  => 	'advanced',
				'value'	  =>    $queue->getTemplate()->getTemplateTextPreprocessed()
			));
        } else {
        	$fieldset->addField('text','text', array(
				'name'	  =>	'text',
				'label'	  =>	__('Message'),
				'title'	  =>	__('Message'),
				'value'	  =>    $this->getUrl('*/newsletter_template/preview', array('_current'=>true))
			));
			
			$form->getElement('text')->setRenderer(Mage::getModel('adminhtml/newsletter_renderer_text'));
			$form->getElement('subject')->setDisabled('true');
			$form->getElement('sender_name')->setDisabled('true');
			$form->getElement('sender_email')->setDisabled('true');
        }
		
	/*	
		$form->getElement('template')->setRenderer(
			$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form_renderer_template')
		);
		*/
		
		
		$this->setForm($form);
		return parent::_prepareForm();
	}
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form END
