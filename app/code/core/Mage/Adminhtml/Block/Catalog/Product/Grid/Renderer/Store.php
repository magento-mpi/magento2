<?php
/**
 * Adminhtml product grid renderer by store
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Grid_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row) 
    {
        $stores = $row->getData($this->getColumn()->getIndex());
        return is_array($stores) ? implode(', ', $stores) : $stores;
    }
}