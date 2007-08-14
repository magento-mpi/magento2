<?php
/**
 * Shipping table rates
 *
 * @package     Mage
 * @subpackage  Shipping
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */

class Mage_Shipping_Model_Mysql4_Carrier_Tablerate
{
    protected $_read;
    protected $_write;
    protected $_shipTable;
    
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('shipping_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('shipping_write');
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');
    }
    
    public function getRate(Mage_Shipping_Model_Rate_Request $request)
    {
        $select = $this->_read->select()->from($this->_shipTable);
        if (is_null($request->getDestCountryId()) && is_null($request->getDestRegionId())) {

            // assuming that request is coming from shopping cart
            // for shipping prices pre-estimation...

            // also probably it will be required to move this part to
            // Sales/Model/Quote/Address.php !

            // TOFIX, FIXME                                TOFIX, FIXME
            $selectCountry = $this->_read->select()->from('usa_postcode', array('country_id', 'region_id'));
            $selectCountry->where('postcode=?', $request->getDestPostcode());
            $selectCountry->limit(1);
            $countryRegion = $this->_read->fetchRow($selectCountry);
            $region = $this->_read->quote($countryRegion['region_id']);
            $country = $this->_read->quote($countryRegion['country_id']);
        } else {
            $region = $this->_read->quote($request->getDestRegionId());
            $country = $this->_read->quote($request->getDestCountryId());
        }
        $zip = $this->_read->quote($request->getDestPostcode());
        $select->where("(dest_zip=$zip)
                     OR (dest_region_id=$region AND dest_zip='')
                     OR (dest_country_id=$country AND dest_region_id='0' AND dest_zip='')");
        if (is_array($request->getConditionName())) {
            $i = 0;
            foreach ($request->getConditionName() as $conditionName) {
                if ($i == 0) {
                    $select->where('condition_name=?', $conditionName);
                } else {
                    $select->orWhere('condition_name=?', $conditionName);
                }
                $select->where('condition_value<=?', $request->getData($conditionName));
                $i++;
            }
        } else {
            $select->where('condition_name=?', $request->getConditionName());
            $select->where('condition_value<=?', $request->getData($request->getConditionName()));
        }
        $select->order('condition_value DESC')->limit(1);
        $row = $this->_read->fetchRow($select);
        return $row;
    }
}
