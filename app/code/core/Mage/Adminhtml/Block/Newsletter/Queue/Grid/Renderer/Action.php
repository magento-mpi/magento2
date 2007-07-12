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
        $str = '<a href="' . Mage::getUrl('*/newsletter_template/preview',array('id'=>$row->getTemplateId())) . '" target="_blank">' 
             . __('Preview') . '</a>';
        
        if($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
        	$str .= ' | <a href="' . Mage::getUrl('*/*/edit', array('id'=>$row->getId())) . '">' . __('edit') . '</a>';
        	if(!$row->getQueueStartAt()) {
        		$str .= ' | <a href="' . Mage::getUrl('*/*/start', array('id'=>$row->getId())) . '">' . __('Start') . '</a>';
        	}
        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_SENDIND) {
        	// TOdo: do)
        }
        
        return $str;
    }
}