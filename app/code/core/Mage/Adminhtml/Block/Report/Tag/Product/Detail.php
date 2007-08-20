<?php
/**
 * Adminhtml tags detail for product report blocks content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */

class Mage_Adminhtml_Block_Report_Tag_Product_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'report_tag_product_detail';
        $this->_headerText = __('Product Tags');
        parent::__construct();
        $this->_removeButton('add');
    }
    
}