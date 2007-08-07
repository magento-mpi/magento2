<?php
/**
 * Order payment entity resource model
 *
 * @package     Mage
 * @subpackage  Sales
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_Model_Entity_Order_Payment extends Mage_Eav_Model_Entity_Abstract
{

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
	    $this->setType('order_payment')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write')
        );
    }

}
