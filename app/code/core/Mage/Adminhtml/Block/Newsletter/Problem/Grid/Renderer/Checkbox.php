<?php
/**
 * Adminhtml newsletter subscribers grid checkbox item renderer
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Problem_Grid_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return '<input type="checkbox" name="problem[]" value="' . $row->getId() . '" class="problemCheckbox"/>';
    }
}// Class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Checkbox END