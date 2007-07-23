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
        $this->setChild('save_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Queue'),
                    'onclick'   => 'queueForm.submit()'
                ))
        );
        
        $this->setChild('reset_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'window.location = window.location'
                ))
        );
        
        $this->setChild('back_button', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Back'),
    					'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'"
    				)
    			)
    	);
    	
    }
    
    
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
    
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }
    
	public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }
    
    public function getIsPreview()
    {
    	$queue = Mage::getSingleton('newsletter/queue');
    	return !in_array($queue->getQueueStatus(), array(Mage_Newsletter_Model_Queue::STATUS_NEVER, Mage_Newsletter_Model_Queue::STATUS_PAUSE));
    }
    
    public function getHeaderText() 
    {
    	return ( $this->getIsPreview() ? __('Edit Newsletter Queue') : __('Newsletter Queue'));
    }

	
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit END
