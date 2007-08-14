<?php
/**
 * Shipping table rates collection
 *
 * @package     Mage
 * @subpackage  Shipping
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Shipping_Model_Mysql4_Carrier_Tablerate_Collection extends Varien_Data_Collection_Db
{
    protected $_shipTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('shipping_read'));
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');
        $this->_sqlSelect->from($this->_shipTable);

//        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('shipping/tablerate'));
        
        return $this;
    }

    public function setCountryFilter($countryId)
    {
        $this->_sqlSelect->where("dest_country_id = ?", $countryId);

        return $this;
    }

    public function setConditionFilter($conditionName)
    {
    	$this->_sqlSelect->where("condition_name = ?", $conditionName);

    	return $this;
    }
}