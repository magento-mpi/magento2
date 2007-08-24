<?php
/**
 * Adminhtml sales order edit items grid qty_backordered renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Edit_Items_Grid_Renderer_Backordered extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $html = '<input name="items[qty_backordered][' . $row->getData('entity_id') . ']" value="' . $row->getData($this->getColumn()->getIndex()) . '" class="input-text" type="text">';
        return $html;
    }

}
