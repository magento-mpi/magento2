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
class Mage_Sales_Model_Entity_Quote_Address extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
	    $this->setType('quote_address')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write')
        );
    }

    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $attributes = $this->loadAllAttributes()->getAttributesByCode();
        foreach ($attributes as $attrCode=>$attr) {
            $backend = $attr->getBackend();
            if (is_callable(array($backend, 'collectTotals'))) {
                $backend->collectTotals($address);
            }
        }
        return $this;
    }
    
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $attributes = $this->loadAllAttributes()->getAttributesByCode();
        foreach ($attributes as $attrCode=>$attr) {
            $frontend = $attr->getFrontend();
            if (is_callable(array($frontend, 'fetchTotals'))) {
                $frontend->fetchTotals($address);
            }
        }

        return $this;
    }
    
    protected function _afterSave(Varien_Object $object)
    {
        parent::_afterSave($object);
        $object->getShippingRatesCollection()->save();
    }
}