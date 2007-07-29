<?php
/**
 * Backup type column renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Backup_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected function _getOption($id)
    {
        $options = array(
                'db'=>__('DB')                
        );
        
        if(!isset($options[$id])) {
        	return '';
        }
        return $options[$id];
    }
    
    public function render(Varien_Object $row)
    {
    	return $this->_getOption($row->getData($this->getColumn()->getIndex()));
    }
    
    
}