<?php
/**
 * Quote entity resource model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Sales_Model_Entity_Quote extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
	    $this->setType('quote')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write')
        );
    }
    
    public function getAllAddresses()
    {
        $addresses = Mage::getModel('sales_entity/quote_')
    }

    public function getBillingAddress()
    {
        
    }
    
    public function getAllShippingAddresses()
    {
        
    }
    
    public function getShippingAddress($addressId)
    {
        
    }
    
    public function getAllItems()
    {
        
    }
    
    public function getItem($itemId)
    {
        
    }
    
    public function getAllPayments()
    {
        
    }
    
    public function getPayment($paymentId)
    {
        
    }
    
    public function getAllShippingQuotes()
    {
        
    }
    
    public function getShippingQuote($shippingId)
    {
        
    }
}