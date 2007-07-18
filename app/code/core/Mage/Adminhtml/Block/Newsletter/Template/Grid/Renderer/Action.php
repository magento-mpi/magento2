<?php
/**
 * Adminhtml newsletter templates grid block action item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        
    	$actions = array();
    	
    	$actions[] = array(
    		'@'	=>	array('href' => Mage::getUrl('*/*/edit', array('id'=>$row->getId()))),
    		'#'	=>	__('Edit')
    	);
    	
    	if($row->isValidForSend()) {
    		$actions[] = array(
	    		'@'	=>	array('href' => Mage::getUrl('*/*/toqueue', array('id'=>$row->getId()))),
	    		'#'	=>	__('Create Queue')
	    	);
    	}
    	
    	$actions[] = array(
    		
    		'@'	=>	array(
	    				'href'		=>	Mage::getUrl('*/*/preview', array('id'=>$row->getId())),
	    				'target'	=>	'_blank'
	    	),
    		'#'	=>	__('Preview')
    	);
    	
    	$actions[] = array(
    		'@'	=>	array(
    					  'href' => Mage::getUrl('*/*/delete', array('id'=>$row->getId())),
    					  'onclick' => 'return confirm(\'' . $this->_getEscapedValue(__('Do you really want to delete this template?')) . '\')'
    		),
    		'#'	=>	__('Delete')
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
    	foreach ($actions as $action) {
    		$attributesObject->setData($action['@']);
    		$html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
    	}    	
    	return implode(' | ', $html);
    }
}
