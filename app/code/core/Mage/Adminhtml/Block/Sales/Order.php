<?php
/**
 * Adminhtml sales orders block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = __('Orders');
        $this->_addButtonLabel = __('Create New Order');
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return Mage::getUrl('*/sales_order_create/start');
    }

}
