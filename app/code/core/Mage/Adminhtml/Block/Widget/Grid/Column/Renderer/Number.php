<?php
/**
 * Adminhtml grid item renderer number
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
        	return $data * 1;
        }
        return null;
    }

    public function renderProperty()
    {
        $out = parent::renderProperty();
        $out.= ' width="140px" ';
        return $out;
    }

}
