<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A custom "Import" adapter for Mage_ImportExport module that allows mocking data rows
 */
class Mage_ImportExport_Model_Import_Adapter_Mock extends Mage_ImportExport_Model_Import_Adapter_Abstract
{
    protected $_values = array();

    /**
     * Instantiate with array of data
     *
     * @param array $colNames
     * @param array $values
     */
    public function __construct(array $colNames, array $values)
    {
        $this->_colNames = $colNames;
        $this->_colQuantity = count($colNames);
        $this->_values = $values;
    }

    /**
     * Generate new element ("Iterator")
     */
    public function next()
    {
        if (isset($this->_values[$this->_currentKey + 1])) {
            $this->_currentKey++;
            $this->_currentRow = $this->_values[$this->_currentKey];
        } else {
            $this->_currentKey = null;
            $this->_currentRow = false;
        }
    }

    /**
     * Generate first element ("Iterator")
     */
    public function rewind()
    {
        $this->_currentKey = -1;
        $this->next();
    }
}
