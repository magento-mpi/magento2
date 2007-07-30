<?php
/**
 * Adminhtml customer wishlist grid filter by store
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Grid_Filter_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select 
{
    protected function _getOptions()
    {
        $options = Mage::getResourceModel('core/store_collection')
            ->setWithoutDefaultFilter()
            ->load()
            ->toOptionArray();
        array_unshift($options, array('label'=>__('All'),'value'=>'0'));
        return $options;
    }
}// Class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Filter_Store END