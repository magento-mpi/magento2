<?php
/**
 * Backend model for shipping table rates CSV importing
 *
 * @package     Mage
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */

final class Mage_Adminhtml_Model_System_Config_Backend_Shipping_Tablerate extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    protected $_resourceModel;

	/**
	 * DB connections list
	 *
	 * @var array
	 */
	protected $_connections = array();

	public function __construct()
	{

    }

    /**
     * Set connections for entity operations
     *
     * @param Zend_Db_Adapter_Abstract $read
     * @param Zend_Db_Adapter_Abstract $write
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setConnection(Zend_Db_Adapter_Abstract $read, Zend_Db_Adapter_Abstract $write=null)
    {
        $this->_connections['read'] = $read;
        $this->_connections['write'] = $write ? $write : $read;
        return $this;
    }

    /**
     * Return DB connection
     *
     * @param	string		$type
     * @return	Zend_Db_Adapter_Abstract
     */
    public function getConnection($type)
    {
    	if (!isset($this->_connections[$type])) {
    		$this->_connections[$type] = Mage::getSingleton('core/resource')->getConnection('shipping_' . $type);
    	}
    	return $this->_connections[$type];
    }

    public function afterSave($object)
    {
        // TOFIX, FIXME:
        $csvFile = $_FILES["groups"]["tmp_name"]["tablerate"]["fields"]["import"]["value"];
        
        if (!empty($csvFile)) {

            $csv = trim(file_get_contents($csvFile));

            $table = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');

    	    $websiteId = $object->getScopeId();
            $websiteModel = Mage::getModel('core/website')->load($websiteId);
            $conditionName = $object->getValue();
            if ($conditionName{0} == '_') {
                $conditionName = substr($conditionName, 1, strpos($conditionName, '/')-1);
            } else {
                $conditionName = $websiteModel->getConfig('carriers/tablerate/condition_name');
            }
            $conditionFullName = Mage::getModel('shipping/carrier_tablerate')->getCode('condition_name_short', $conditionName);

            if (!empty($csv)) {
                $exceptions = array();
                $csvLines = explode("\n", $csv);
                $csvLine = array_shift($csvLines);
                $csvLine = $this->_getCsvValues($csvLine);
                if (count($csvLine) < 5) {
                    $exceptions[0] = 'Invalid Table Rates File Format';
                }

                $countryCodes = array();
                $regionCodes = array();
                foreach ($csvLines as $k=>$csvLine) {
                    $csvLine = $this->_getCsvValues($csvLine);
                    if (count($csvLine) > 0 && count($csvLine) < 5) {
                        $exceptions[0] = 'Invalid Table Rates File Format';
                    } else {
                        $countryCodes[] = $csvLine[0];
                        $regionCodes[] = $csvLine[1];
                    }
                }

                if (empty($exceptions)) {
                    $data = array();
                    $countryCodesToIds = array();
                    $regionCodesToIds = array();

                    $countryCollection = Mage::getResourceModel('directory/country_collection')->addCountryCodeFilter($countryCodes)->load();
                    foreach ($countryCollection->getItems() as $country) {
                        $countryCodesToIds[$country->getData('iso3_code')] = $country->getData('country_id');
                    }

                    $regionCollection = Mage::getResourceModel('directory/region_collection')->addRegionCodeFilter($regionCodes)->load();
                    foreach ($regionCollection->getItems() as $region) {
                        $regionCodesToIds[$region->getData('code')] = $region->getData('region_id');
                    }

                    foreach ($csvLines as $k=>$csvLine) {
                        $csvLine = $this->_getCsvValues($csvLine);

                        if (empty($countryCodesToIds) || !array_key_exists($csvLine[0], $countryCodesToIds)) {
                            $countryId = '0';
                            if ($csvLine[0] != '*' && $csvLine[0] != '') {
                                $exceptions[] = 'Invalid Country "' . $csvLine[0] . '" in the Row #' . ($k+1);
                            }
                        } else {
                            $countryId = $countryCodesToIds[$csvLine[0]];
                        }

                        if (empty($regionCodesToIds) || !array_key_exists($csvLine[1], $regionCodesToIds)) {
                            $regionId = '0';
                            if ($csvLine[1] != '*' && $csvLine[1] != '') {
                                $exceptions[] = 'Invalid Region/State "' . $csvLine[1] . '" in the Row #' . ($k+1);
                            }
                        } else {
                            $regionId = $regionCodesToIds[$csvLine[1]];
                        }

                        if ($csvLine[2] == '*' || $csvLine[2] == '') {
                            $zip = '';
                        } else {
                            $zip = $csvLine[2];
                        }
                        
                        if (!$this->_isPositiveDecimalNumber($csvLine[3]) || $csvLine[3] == '*' || $csvLine[3] == '') {
                            $exceptions[] = 'Invalid ' . $conditionFullName . ' "' . $csvLine[3] . '" in the Row #' . ($k+1);
                        } else {
                            $csvLine[3] = (float)$csvLine[3];
                        }

                        if (!$this->_isPositiveDecimalNumber($csvLine[4])) {
                            $exceptions[] = 'Invalid Shipping Price "' . $csvLine[4] . '" in the Row #' . ($k+1);
                        } else {
                            $csvLine[4] = (float)$csvLine[4];
                        }

                        $data[] = array('website_id'=>$websiteId, 'dest_country_id'=>$countryId, 'dest_region_id'=>$regionId, 'dest_zip'=>$zip, 'condition_name'=>$conditionName, 'condition_value'=>$csvLine[3], 'price'=>$csvLine[4]);
                        $dataDetails[] = array('country'=>$csvLine[0], 'region'=>$csvLine[1]);
                    }
                }
                if (empty($exceptions)) {
                    $connection = $this->getConnection('write');

    	            $condition = array(
                        $connection->quoteInto('website_id = ?', $websiteId),
    		            $connection->quoteInto('condition_name = ?', $conditionName),
    	            );
    	            $connection->delete($table, $condition);

                    foreach($data as $k=>$dataLine) {
                        try {
    	                    $connection->insert($table, $dataLine);
                        } catch (Exception $e) {
                            $exceptions[] = 'Duplicate Row #' . ($k+1) . ' (Country "' . $dataDetails[$k]['country'] . '", Region/State "' . $dataDetails[$k]['region'] . '", Zip "' . $dataLine['dest_zip'] . '" and Value "' . $dataLine['condition_value'] . '")';
                        }
                    }
                }
                if (!empty($exceptions)) {
                    throw new Exception( "\n" . implode("\n", $exceptions) );
                }
            }
        }
    }
    
    private function _getCsvValues($string, $separator=",")
    {
        $elements = explode($separator, trim($string));
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes %2 == 1) {
                for ($j = $i+1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') > 0) {
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j-$i+1, implode($separator, array_slice($elements, $i, $j-$i+1)));
                        break;
                    }
                }
            }
            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr =& $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
            $elements[$i] = trim($elements[$i]);
        }
        return $elements;
    }
    
    private function _isPositiveDecimalNumber($n)
    {
        return preg_match ("/^[0-9]+(\.[0-9]*)?$/", $n);
    }

}
