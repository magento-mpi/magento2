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
 
class Mage_Adminhtml_Block_Newsletter_Template_System_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	protected static $_types = array(
		Mage_Newsletter_Model_Template::TYPE_HTML   => 'HTML',
		Mage_Newsletter_Model_Template::TYPE_TEXT 	=> 'Text'
	);
    public function render(Varien_Object $row)
    {
                
        $str = 'Unknown';
        
        if(isset(self::$_types[$row->getTemplateType()])) {
            $str = self::$_types[$row->getTemplateType()];
        }
        
        return __($str);
    }
}