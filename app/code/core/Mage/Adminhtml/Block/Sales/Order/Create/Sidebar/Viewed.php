<?php
/**
 * Adminhtml sales order create sidebar viewed block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Viewed extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_sidebar_viewed');
        $this->setScId('viewed');
    }

    public function hasItems()
    {
        return false;
    }

    public function getHeaderText()
    {
        return __('Recently Viewed');
    }

}
