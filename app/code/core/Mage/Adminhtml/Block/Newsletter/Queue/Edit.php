<?php
/**
 * Adminhtml newsletter queue edit block
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Queue_Edit extends Mage_Core_Block_Template 
{
	protected  function _beforeToHtml() {
		
		$this->setTemplate('newsletter/queue/edit.phtml');
		
		$this->setChild('form',
			$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form','form')
		);
		
		return parent::_beforeToHtml();
	}
	
	public function getSaveUrl() 
	{
		return $this->getUrl('*/*/save',array('id'=>$this->_request->getParam('id')));
	}
	
	protected function _initChildren()
    {
        $this->setChild('saveButton', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Queue'),
                    'onclick'   => 'queueForm.submit()'
                ))
        );
    }
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

	
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit END
