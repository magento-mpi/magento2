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
 
class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Renderer
{
    public function render(Varien_Object $row, $index, $format=null)
    {
        $str = '<a class="" href="' . Mage::getUrl('adminhtml/*/edit',array('id'=>$row->getId())) . '">' . __('edit') 
             . '</a> ' . ($row->isValidForSend() ?  '| <a href="' . Mage::getUrl('adminhtml/*/toqueue',array('id'=>$row->getId())) . '">' 
             . __('create queue') . '</a>' : '' )  
             . '| <a href="' . Mage::getUrl('adminhtml/*/delete',array('id'=>$row->getId())) . '">' 
             . __('delete') . '</a>';
        
        return $str;
    }
}