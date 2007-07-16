<?php
/**
 * Adminhtml grid tags renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Grid_Column_Renderer_Tags extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
            $out = array();
            foreach ($data as $t) {
                $out[] = '<span class="nowrap">' . $t['name'] . '<sup>(' . $t['total_used'] . ')</sup></span>';
        	}
            return implode(', ', $out);
        }
        return $this->getColumn()->getDefault();
    }
}