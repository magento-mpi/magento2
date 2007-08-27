<?php
/**
 * Adminhtml Shopping cart products report page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */

class Mage_Adminhtml_Block_Report_Shopcart_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'report_shopcart_product';
        $this->_headerText = __('Shopcart Products');
        parent::__construct();
        $this->_removeButton('add');
    }

}