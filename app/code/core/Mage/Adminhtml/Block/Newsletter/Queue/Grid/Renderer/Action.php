<?php
/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$actions = array();
    	
    	
    	
        
        if($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
        	
        	        	
        	if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
        		$actions[] = array(
		    		'@'	=>	array('href' => Mage::getUrl('*/*/start', array('id'=>$row->getId()))),
		    		'#'	=>	__('Start')
		    	);
        	}
        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_SENDING) {
        	$actions[] = array(
		    		'@'	=>	array('href' => Mage::getUrl('*/*/pause', array('id'=>$row->getId()))),
		    		'#'	=>	__('Pause')
		    );
		    $actions[] = array(
		    		'@'	=>	array(
		    			'href'		=>	Mage::getUrl('*/*/cancel', array('id'=>$row->getId())),
		    			'onclick'	=>	'return confirm(\'' . $this->_getEscapedValue(__('Do you really want to cancel the queue?')) . '\')'
		    		),
		    		'#'	=>	__('Cancel')
		    );
		    
        	
        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_PAUSE) {
			
		    $actions[] = array(
		    		'@'	=>	array('href' => Mage::getUrl('*/*/resume', array('id'=>$row->getId()))),
		    		'#'	=>	__('Resume')
		    );
		    
        }
        
        $actions[] = array(
    		'@'	=>	array(
    				'href'		=>	Mage::getUrl('*/newsletter_template/preview',
    											 array('id'=>$row->getTemplateId())	
    								),
    				'target'	=>	'_blank'
    		),
    		'#'	=>	__('Preview'),
    		'brake' => true
    	);
                         
        return $this->_actionsToHtml($actions);
    }
    
    protected function _getEscapedValue($value) 
    {
    	return addcslashes(htmlspecialchars($value),'\\\'');
    }
    
    protected function _actionsToHtml(array $actions) 
    {
    	$html = array();
    	$attributesObject = new Varien_Object();
    	$result = "";
    	foreach ($actions as $action) {
    		$attributesObject->setData($action['@']);
    		if((isset($action['brake']) &&  count($html) > 0)) {
    			$result.= implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    			$result.= '<br />';
    			$html = array();
    		}
    		
    		$html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
    		
    		
    	}    	
    	return $result . implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }
}
