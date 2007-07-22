<?php
/**
 * Adminhtml newsletter templates grid block sender item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Template_System_Grid_Renderer_Sender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $str = '';
        
        if($row->getTemplateSenderName()) {
            $str .= htmlspecialchars($row->getTemplateSenderName()) . ' ';
        }        
        
        if($row->getTemplateSenderEmail()) {
            $str .= '[' . $row->getTemplateSenderEmail() . ']';
        }        
        
        if($str == '') {
            $str .= '---';
        }
            
        return $str;
    }
}