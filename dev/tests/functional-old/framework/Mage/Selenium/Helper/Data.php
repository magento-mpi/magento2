<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test data helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Data extends Mage_Selenium_Helper_Abstract
{
    /**
     * Array of files paths to data fixtures
     * @var array
     */
    protected $_configDataFiles = array();

    /**
     * Test data array used in tests
     * @var array
     */
    protected $_testData = array();

    /**
     * Test data array loaded from files and not used in tests
     * @var array
     */
    protected $_loadedTestData = array();

    /**
     * Initialize process
     */
    protected function _init()
    {
        $this->_configDataFiles = $this->getConfig()->getConfigData();
        $config = $this->getConfig()->getHelper('config')->getConfigFramework();
        if ($config['load_all_data']) {
            $this->_loadTestData();
        }
    }

    /**
     * Loads and merges DataSet files
     * @return Mage_Selenium_Helper_Data
     */
    private function _loadTestData()
    {
        if ($this->_testData) {
            return $this;
        }
        foreach ($this->_configDataFiles as $file) {
            $dataSets = $this->getConfig()->getHelper('file')->loadYamlFile($file);
            if (!$dataSets) {
                continue;
            }
            foreach ($dataSets as $dataSetKey => $content) {
                if ($content) {
                    $this->_testData[$dataSetKey] = $content;
                }
            }
        }

        return $this;
    }

    /**
     * Get test data array
     * @return array
     */
    protected function getTestData()
    {
        if (!$this->_testData) {
            $this->_loadTestData();
        }
        return $this->_testData;
    }

    /**
     * Get value from DataSet by path
     *
     * @param string $path XPath-like path to DataSet value (by default = '')
     *
     * @return mixed
     */
    public function getDataValue($path = '')
    {
        return $this->getConfig()->_descend($this->_testData, $path);
    }

    /**
     * Loads DataSet from specified file.
     *
     * @param string $dataFile - File name or full path to file in fixture folder
     * (for example: 'default\core\Mage\AdminUser\data\AdminUsers') in which DataSet is specified
     * @param string $dataSetName
     *
     * @return array
     * @throws RuntimeException
     */
    public function loadTestDataSet($dataFile, $dataSetName)
    {
        $fileName = $dataFile;
        if (preg_match('/(\/)|(\\\)/', $dataFile)) {
            $condition = preg_quote(preg_replace('/(\/)|(\\\)/', '/', $dataFile));
            $fileName = end(explode('/', $condition));
        } else {
            $condition = 'data' . preg_quote('/') . $dataFile;
        }
        if (!preg_match('|\.yml$|', $condition)) {
            $condition .= '\.yml$';
        }
        $fileName = preg_replace('|\.yml$|', '', $fileName);

        if (!array_key_exists($fileName, $this->_loadedTestData)) {
            $this->_loadTestDataSetFromFiles($condition, $fileName);
        } elseif (array_key_exists($dataSetName, $this->_testData)) {
            return $this->_testData[$dataSetName];
        }
        if (isset($this->_loadedTestData[$fileName])
            && array_key_exists($dataSetName, $this->_loadedTestData[$fileName])
        ) {
            $this->_testData[$dataSetName] = $this->_loadedTestData[$fileName][$dataSetName];
            unset($this->_loadedTestData[$fileName][$dataSetName]);
            return $this->_testData[$dataSetName];
        }
        throw new RuntimeException(
            'DataSet with name "' . $dataSetName . '" is not present in "' . $dataFile . '" file.');
    }

    /**
     * Loads data from files
     * @param $condition
     * @param $fileName
     */
    protected function _loadTestDataSetFromFiles($condition, $fileName)
    {
        foreach ($this->_configDataFiles as $file) {
            if (!preg_match('|' . $condition . '|', $file)) {
                continue;
            }
            $dataSets = $this->getConfig()->getHelper('file')->loadYamlFile($file);
            foreach ($dataSets as $dataSetKey => $content) {
                if ($content) {
                    $this->_loadedTestData[$fileName][$dataSetKey] = $content;
                }
            }
        }
    }
}