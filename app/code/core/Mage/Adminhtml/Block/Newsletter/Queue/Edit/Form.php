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
		$queue = Mage::getModel('newsletter/queue')
			->load($this->_request->getParam('id'));
		$queue->addTemplateData($queue);
		$form = new Varien_Data_Form();
		
		$fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Queue general')));
		
		$fieldset->addField('template','text',array(
			'label'	=>	__('Template'),
			'title'	=>	__('Template'),
			'value'	=>	$queue->getTemplate()
		));
		
		$fieldset->addField('date','date',array(
			'name'	=>	'start_at',
			'time'	=>	true,
			'label'	=>	__('Start this queue at '),
			'title'	=>	__('Queue start date')
		));
		
		$form->getElement('template')->setRenderer(
			$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form_renderer_template')
		);
		
		Mage::getModel('core/website')
			->load(1)
			->getStoreIds();
		
		$this->setForm($form);
		return parent::_prepareForm();
	}
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form END