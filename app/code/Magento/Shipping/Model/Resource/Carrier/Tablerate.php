<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping table rates
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Shipping_Model_Resource_Carrier_Tablerate extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Import table rates website ID
     *
     * @var int
     */
    protected $_importWebsiteId     = 0;

    /**
     * Errors in import process
     *
     * @var array
     */
    protected $_importErrors        = array();

    /**
     * Count of imported table rates
     *
     * @var int
     */
    protected $_importedRows        = 0;

    /**
     * Array of unique table rate keys to protect from duplicates
     *
     * @var array
     */
    protected $_importUniqueHash    = array();

    /**
     * Array of countries keyed by iso2 code
     *
     * @var array
     */
    protected $_importIso2Countries;

    /**
     * Array of countries keyed by iso3 code
     *
     * @var array
     */
    protected $_importIso3Countries;

    /**
     * Associative array of countries and regions
     * [country_id][region_code] = region_id
     *
     * @var array
     */
    protected $_importRegions;

    /**
     * Import Table Rate condition name
     *
     * @var string
     */
    protected $_importConditionName;

    /**
     * Array of condition full names
     *
     * @var array
     */
    protected $_conditionFullNames  = array();

    /**
     * Define main table and id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('shipping_tablerate', 'pk');
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return array|boolean
     */
    public function getRate(Magento_Shipping_Model_Rate_Request $request)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array(
            ':website_id' => (int) $request->getWebsiteId(),
            ':country_id' => $request->getDestCountryId(),
            ':region_id' => (int) $request->getDestRegionId(),
            ':postcode' => $request->getDestPostcode()
        );
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('website_id = :website_id')
            ->order(array('dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC'))
            ->limit(1);

        // Render destination condition
        $orWhere = '(' . implode(') OR (', array(
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode",
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",

            // Handle asterix in dest_zip field
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
            "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
            "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",

            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
        )) . ')';
        $select->where($orWhere);

        // Render condition by condition name
        if (is_array($request->getConditionName())) {
            $orWhere = array();
            $i = 0;
            foreach ($request->getConditionName() as $conditionName) {
                $bindNameKey  = sprintf(':condition_name_%d', $i);
                $bindValueKey = sprintf(':condition_value_%d', $i);
                $orWhere[] = "(condition_name = {$bindNameKey} AND condition_value <= {$bindValueKey})";
                $bind[$bindNameKey] = $conditionName;
                $bind[$bindValueKey] = $request->getData($conditionName);
                $i++;
            }

            if ($orWhere) {
                $select->where(implode(' OR ', $orWhere));
            }
        } else {
            $bind[':condition_name']  = $request->getConditionName();
            $bind[':condition_value'] = $request->getData($request->getConditionName());

            $select->where('condition_name = :condition_name');
            $select->where('condition_value <= :condition_value');
        }

        $result = $adapter->fetchRow($select, $bind);
        // Normalize destination zip code
        if ($result && $result['dest_zip'] == '*') {
            $result['dest_zip'] = '';
        }
        return $result;
    }

    /**
     * Upload table rate file and import data from it
     *
     * @param Magento_Object $object
     * @throws Magento_Core_Exception
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate
     */
    public function uploadAndImport(Magento_Object $object)
    {
        if (empty($_FILES['groups']['tmp_name']['tablerate']['fields']['import']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['tablerate']['fields']['import']['value'];
        $website = Mage::app()->getWebsite($object->getScopeId());

        $this->_importWebsiteId     = (int)$website->getId();
        $this->_importUniqueHash    = array();
        $this->_importErrors        = array();
        $this->_importedRows        = 0;

        $io     = new Magento_Io_File();
        $info   = pathinfo($csvFile);
        $io->open(array('path' => $info['dirname']));
        $io->streamOpen($info['basename'], 'r');

        // check and skip headers
        $headers = $io->streamReadCsv();
        if ($headers === false || count($headers) < 5) {
            $io->streamClose();
            Mage::throwException(__('Please correct Table Rates File Format.'));
        }

        if ($object->getData('groups/tablerate/fields/condition_name/inherit') == '1') {
            $conditionName = (string)Mage::getConfig()->getValue('carriers/tablerate/condition_name', 'default');
        } else {
            $conditionName = $object->getData('groups/tablerate/fields/condition_name/value');
        }
        $this->_importConditionName = $conditionName;

        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $rowNumber  = 1;
            $importData = array();

            $this->_loadDirectoryCountries();
            $this->_loadDirectoryRegions();

            // delete old data by website and condition name
            $condition = array(
                'website_id = ?'     => $this->_importWebsiteId,
                'condition_name = ?' => $this->_importConditionName
            );
            $adapter->delete($this->getMainTable(), $condition);

            while (false !== ($csvLine = $io->streamReadCsv())) {
                $rowNumber ++;

                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if (count($importData) == 5000) {
                    $this->_saveImportData($importData);
                    $importData = array();
                }
            }
            $this->_saveImportData($importData);
            $io->streamClose();
        } catch (Magento_Core_Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            Mage::logException($e);
            Mage::throwException(__('Something went wrong while importing table rates.'));
        }

        $adapter->commit();

        if ($this->_importErrors) {
            $error = __('We couldn\'t import this file because of these errors: %1', implode(" \n", $this->_importErrors));
            Mage::throwException($error);
        }

        return $this;
    }

    /**
     * Load directory countries
     *
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _loadDirectoryCountries()
    {
        if (!is_null($this->_importIso2Countries) && !is_null($this->_importIso3Countries)) {
            return $this;
        }

        $this->_importIso2Countries = array();
        $this->_importIso3Countries = array();

        /** @var $collection Magento_Directory_Model_Resource_Country_Collection */
        $collection = Mage::getResourceModel('Magento_Directory_Model_Resource_Country_Collection');
        foreach ($collection->getData() as $row) {
            $this->_importIso2Countries[$row['iso2_code']] = $row['country_id'];
            $this->_importIso3Countries[$row['iso3_code']] = $row['country_id'];
        }

        return $this;
    }

    /**
     * Load directory regions
     *
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _loadDirectoryRegions()
    {
        if (!is_null($this->_importRegions)) {
            return $this;
        }

        $this->_importRegions = array();

        /** @var $collection Magento_Directory_Model_Resource_Region_Collection */
        $collection = Mage::getResourceModel('Magento_Directory_Model_Resource_Region_Collection');
        foreach ($collection->getData() as $row) {
            $this->_importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }

        return $this;
    }

    /**
     * Return import condition full name by condition name code
     *
     * @param string $conditionName
     * @return string
     */
    protected function _getConditionFullName($conditionName)
    {
        if (!isset($this->_conditionFullNames[$conditionName])) {
            $name = Mage::getSingleton('Magento_Shipping_Model_Carrier_Tablerate')->getCode('condition_name_short', $conditionName);
            $this->_conditionFullNames[$conditionName] = $name;
        }

        return $this->_conditionFullNames[$conditionName];
    }

    /**
     * Validate row for import and return table rate array or false
     * Error will be add to _importErrors array
     *
     * @param array $row
     * @param int $rowNumber
     * @return array|false
     */
    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row
        if (count($row) < 5) {
            $this->_importErrors[] = __('Please correct Table Rates format in the Row #%1.', $rowNumber);
            return false;
        }

        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        // validate country
        if (isset($this->_importIso2Countries[$row[0]])) {
            $countryId = $this->_importIso2Countries[$row[0]];
        } elseif (isset($this->_importIso3Countries[$row[0]])) {
            $countryId = $this->_importIso3Countries[$row[0]];
        } elseif ($row[0] == '*' || $row[0] == '') {
            $countryId = '0';
        } else {
            $this->_importErrors[] = __('Please correct Country "%1" in the Row #%2.', $row[0], $rowNumber);
            return false;
        }

        // validate region
        if ($countryId != '0' && isset($this->_importRegions[$countryId][$row[1]])) {
            $regionId = $this->_importRegions[$countryId][$row[1]];
        } elseif ($row[1] == '*' || $row[1] == '') {
            $regionId = 0;
        } else {
            $this->_importErrors[] = __('Please correct Region/State "%1" in the Row #%2.', $row[1], $rowNumber);
            return false;
        }

        // detect zip code
        if ($row[2] == '*' || $row[2] == '') {
            $zipCode = '*';
        } else {
            $zipCode = $row[2];
        }

        // validate condition value
        $value = $this->_parseDecimalValue($row[3]);
        if ($value === false) {
            $this->_importErrors[] = __('Please correct %1 "%2" in the Row #%3.', $this->_getConditionFullName($this->_importConditionName), $row[3], $rowNumber);
            return false;
        }

        // validate price
        $price = $this->_parseDecimalValue($row[4]);
        if ($price === false) {
            $this->_importErrors[] = __('Please correct Shipping Price "%1" in the Row #%2.', $row[4], $rowNumber);
            return false;
        }

        // protect from duplicate
        $hash = sprintf("%s-%d-%s-%F", $countryId, $regionId, $zipCode, $value);
        if (isset($this->_importUniqueHash[$hash])) {
            $this->_importErrors[] = __('Duplicate Row #%1 (Country "%2", Region/State "%3", Zip "%4" and Value "%5")', $rowNumber, $row[0], $row[1], $zipCode, $value);
            return false;
        }
        $this->_importUniqueHash[$hash] = true;

        return array(
            $this->_importWebsiteId,    // website_id
            $countryId,                 // dest_country_id
            $regionId,                  // dest_region_id,
            $zipCode,                   // dest_zip
            $this->_importConditionName,// condition_name,
            $value,                     // condition_value
            $price                      // price
        );
    }

    /**
     * Save import data batch
     *
     * @param array $data
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip',
                'condition_name', 'condition_value', 'price');
            $this->_getWriteAdapter()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }

    /**
     * Parse and validate positive decimal value
     * Return false if value is not decimal or is not positive
     *
     * @param string $value
     * @return bool|float
     */
    protected function _parseDecimalValue($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        $value = (float)sprintf('%.4F', $value);
        if ($value < 0.0000) {
            return false;
        }
        return $value;
    }
}
