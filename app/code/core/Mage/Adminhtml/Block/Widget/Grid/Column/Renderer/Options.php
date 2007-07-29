<?php
/**
 * Grid column widget for rendering grid cells that contains mapped values
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        if (!empty($options) && is_array($options)) {
            return $options[$row->getData($this->getColumn()->getIndex())];
        }
    }

}