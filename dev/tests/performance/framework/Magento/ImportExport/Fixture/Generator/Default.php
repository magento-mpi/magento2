<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A custom "Import" adapter for Mage_ImportExport module that allows generating arbitrary data rows
 */
class Magento_ImportExport_Fixture_Generator_Default extends Mage_ImportExport_Model_Import_Adapter_Abstract
{
    /**
     * Data row pattern
     *
     * @var array
     */
    protected $_pattern = array();

    /**
     * Which columns are determined as dynamic
     *
     * @var array
     */
    protected $_dynamicColumns = array();

    /**
     * @var int
     */
    protected $_limit = 0;

    /**
     * Read the row pattern to determine which columns are dynamic, set the collection size
     *
     * @param array $rowPattern
     * @param int $limit how many records to generate
     */
    public function __construct(array $rowPattern, $limit)
    {
        $this->_pattern     = $rowPattern;
        $this->_colNames    = array_keys($rowPattern);
        $this->_colQuantity = count($rowPattern);
        foreach ($rowPattern as $key => $value) {
            if ($this->_isDynamicColumn($key, $value)) {
                $this->_dynamicColumns[$key] = $value;
            }
        }
        $this->_limit = (int)$limit;
    }

    /**
     * Return whether column's value must be generated dynamically
     *
     * @param string $column
     * @param mixed $pattern
     * @return bool
     */
    protected function _isDynamicColumn($column, $pattern)
    {
        // Basic functionality - ignore column name, assume dynamic columns only where
        return strpos($pattern, '%s') !== false;
    }

    /**
     * Whether limit of generated elements is reached (according to "Iterator" interface)
     *
     * @return bool
     */
    public function valid()
    {
        return $this->_currentKey <= $this->_limit;
    }

    /**
     * Generate new element ("Iterator")
     */
    public function next()
    {
        $this->_currentKey++;
        $this->_currentRow = $this->_pattern;
        foreach ($this->_dynamicColumns as $key => $pattern) {
            $this->_currentRow[$key] = $this->_generateValue($key, $pattern);
        }
    }

    /**
     * Generate value for a column
     *
     * @param string $column
     * @param string $pattern
     * @return mixed
     */
    protected function _generateValue($column, $pattern)
    {
        // Basic functionality - ignore column name, generate new value based on current key
        return str_replace('%s', $this->_currentKey, $pattern);
    }

    /**
     * Generate first element ("Iterator")
     */
    public function rewind()
    {
        $this->_currentKey = 0;
        $this->next();
    }
}
