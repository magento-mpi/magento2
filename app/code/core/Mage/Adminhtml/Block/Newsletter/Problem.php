<?php
/**
 * Adminhtml newsletter problem block template.
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Problem extends Mage_Core_Block_Template 
{
	public function __construct() 
	{
		parent::__construct();
		$this->setTemplate('newsletter/problem/list.phtml');
	}
	
	protected function _initChildren()
	{
		$this->setChild('grid', 
			$this->getLayout()->createBlock('adminhtml/newsletter_problem_grid','newsletter.problem.grid')
		);
		
		$this->setChild('deleteButton', 
			$this->getLayout()->createBlock('adminhtml/widget_button','del.button')
				->setData(
					array(
						'label' => __('Delete Selected Problems'),
						'onclick' => 'problemController.deleteSelected();'
					)
				)
		);
		
		$this->setChild('unsubscribeButton', 
			$this->getLayout()->createBlock('adminhtml/widget_button','unsubscribe.button')
				->setData(
					array(
						'label' => __('Unsubscibe Selected'),
						'onclick' => 'problemController.unsubscribe();'
					)
				)
		);
	}
	
	public function getUnsubscribeButtonHtml() 
	{
		return $this->getChildHtml('unsubscribeButton');
	}
	
	public function getDeleteButtonHtml() 
	{
		return $this->getChildHtml('deleteButton');
	}
	
}// Class Mage_Adminhtml_Block_Newsletter_Problem END
