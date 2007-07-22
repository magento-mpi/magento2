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
 
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$actions = array();
    	
    	$actions[] = array(
    		'@'	=>	array(
    				'href'		=>	Mage::getUrl('*/newsletter_template/preview',
    											 array(
    											 	'id'			=>	$row->getTemplateId(),
    											 	'subscriber'	=>	Mage::registry('subscriber')->getId()
    											 )	
    								),
    				'target'	=>	'_blank'
    		),
    		'#'	=>	__('View')
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
    	return implode('<span class="spacer">&bull;</span>', $html);
    }
}
