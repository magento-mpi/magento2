<?php
/**
 * Adminhtml newsletter templates grid block type item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Renderer
{
    public function render(Varien_Object $row, $index, $format=null)
    {
        $types = array(
            1=>'text',
            2=>'html'
        );
        
        $str = 'unknown';
        
        if($row->getTemplateType()) {
            $str = $types[$row->getTemplateType()];
        } 
           
        return __($str);
    }
}