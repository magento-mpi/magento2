<?php
/**
 * Order status resource model
 *
 * @package     Mage
 * @subpackage  Sales
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_Model_Entity_Order_Status extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('sales/order_status', 'order_status_id');
    }

}
