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
        
//            $csv = trim($object->getValue());
            $csv = trim(file_get_contents($csvFile));

    	    $websiteId = $object->getScopeId();
            $country = 223; // TOFIX, FIXME;
            $table = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');
            
            $connection = $this->getConnection('write');

            if (!empty($csv)) {
                $csvLines = explode("\n", $csv);
                $csvLine = array_shift($csvLines);
                $csvLine = $this->_getCsvValues($csvLine);
                array_shift($csvLine);
                array_shift($csvLine);

                $conditionValues = array();
                foreach ($csvLine as $ceil) {
                    if ($ceil != '') {
                        $conditionValues[] = $ceil;
                    }
                }

                $data = array();
                foreach ($csvLines as $csvLine) {
                    $csvLine = $this->_getCsvValues($csvLine);
                    $region = array_shift($csvLine);
                    $zip = array_shift($csvLine);
                    foreach ($csvLine as $k=>$ceil) {
                        $data[] = array('website_id'=>$websiteId, 'dest_country_id'=>$country, 'dest_region_id'=>$region, 'dest_zip'=>$zip, 'condition_name'=>Mage::getStoreConfig('carriers/tablerate/condition_name'), 'condition_value'=>$conditionValues[$k], 'price'=>$ceil, 'cost'=>0);
                    }
                }
            }
            
    	    $condition = array(
                $connection->quoteInto('website_id = ?', $websiteId),
                $connection->quoteInto('dest_country_id = ?', $country),
    		    $connection->quoteInto('condition_name = ?', Mage::getStoreConfig('carriers/tablerate/condition_name')),
    	    );
    	    $connection->delete($table, $condition);

            foreach ($data as $dataLine) {
    	        $connection->insert($table, $dataLine);
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
        }
        return $elements;
    }

}
