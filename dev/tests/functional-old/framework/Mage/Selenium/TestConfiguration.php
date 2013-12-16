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
 * Test run configuration
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestConfiguration
{
    /**
     * Configuration object instance
     * @var Mage_Selenium_TestConfiguration|null
     */
    private static $_instance = null;

    /**
     * Initial options
     *
     * @var array
     */
    protected $_initialOptions = array();

    /**
     * Initial path
     *
     * @var string
     */
    protected $_initialPath;

    /**
     * File helper instance
     * @var Mage_Selenium_Helper_File|null
     */
    protected $_fileHelper = null;

    /**
     * Config helper instance
     * @var Mage_Selenium_Helper_Config|null
     */
    protected $_configHelper = null;

    /**
     * UIMap helper instance
     * @var Mage_Selenium_Helper_Uimap|null
     */
    protected $_uimapHelper = null;

    /**
     * Data helper instance
     * @var Mage_Selenium_Helper_Data|null
     */
    protected $_dataHelper = null;

    /**
     * Params helper instance
     * @var Mage_Selenium_Helper_Params|null
     */
    protected $_paramsHelper = null;

    /**
     * Data generator helper instance
     * @var Mage_Selenium_Helper_DataGenerator|null
     */
    protected $_dataGeneratorHelper = null;

    /**
     * Array of files paths to data fixtures
     * @var array
     */
    protected $_configData = array();

    /**
     * Array of files paths to Uimap fixtures
     * @var array
     */
    protected $_configUimap = array();

    /**
     * Array of files paths to Uimap Include fixtures
     * @var array
     */
    protected $_configUimapInclude = array();

    /**
     * Array of class names for test Helper files
     * @var array
     */
    protected $_testHelperNames = array();

    /**
     * Uimap include folder name
     * @var string
     */
    const UIMAP_INCLUDE_FOLDER = '_uimapIncludes';

    /**
     * Get test configuration instance
     *
     * @static
     *
     * @param null|array $options
     *
     * @return Mage_Selenium_TestConfiguration|null
     * @throws RuntimeException
     */
    public static function getInstance($options = null)
    {
        if (is_null(static::$_instance)) {
            static::$_instance = new static();
            if (is_array($options)) {
                static::$_instance->setInitialOptions($options);
            }
            static::$_instance->init();
        } else {
            if (!is_null($options)) {
                throw new RuntimeException('Cannot redeclare initial options on existed instance.');
            }
        }
        return static::$_instance;
    }

    /**
     * Set instance
     *
     * @static
     *
     * @param Mage_Selenium_TestConfiguration|null $_instance
     */
    public static function setInstance(Mage_Selenium_TestConfiguration $_instance = null)
    {
        static::$_instance = $_instance;
    }

    /**
     * Set initial options
     *
     * @param array $options
     *
     * @return Mage_Selenium_TestConfiguration
     */
    public function setInitialOptions(array $options)
    {
        $this->_initialOptions = $options;
        return $this;
    }

    /**
     * Retrieve initial options
     *
     * @return array
     */
    public function getInitialOptions()
    {
        return $this->_initialOptions;
    }

    /**
     * Initializes test configuration instance which includes:
     * <ul>
     * <li>Initialize configuration
     * <li>Initialize all paths to Fixture files
     * <li>Initialize Fixtures
     * </ul>
     */
    public function init()
    {
        $this->setInitialPath(SELENIUM_TESTS_BASEDIR . '/');
        $this->_initConfig();
        $this->_initFixturesPaths();
        $this->_initTestHelperClassNames();
        $this->_initFixtures();
    }

    /**
     * Initializes and loads configuration data
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initConfig()
    {
        $this->getHelper('config');
        return $this;
    }

    /**
     * Initialize all paths to fixture files
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initFixturesPaths()
    {
        //Get initial path to fixtures
        $frameworkConfig = $this->_configHelper->getConfigFramework();
        $initialPath = $this->getInitialPath() . $frameworkConfig['fixture_base_path'];
        //Get fixtures sequence
        $fallbackOrderFixture = $this->_configHelper->getFixturesFallbackOrder();

        $initialOptions = $this->getInitialOptions();
        if (isset($initialOptions['fallbackOrderFixture'])) {
            $fallbackOrderFixture = $initialOptions['fallbackOrderFixture'];
        }

        $facade = new File_Iterator_Facade();
        foreach ($fallbackOrderFixture as $codePoolName) {
            $projectPath = $initialPath . '/' . $codePoolName;
            if (!is_dir($projectPath)) {
                continue;
            }
            $files = $facade->getFilesAsArray($projectPath, '.yml');
            $this->setConfigData($files);
            $this->setConfigUimap($files, $codePoolName);
            $this->setConfigUimapInclude($files);
        }
        return $this;
    }

    /**
     * Initialize all class names for test Helper files
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initTestHelperClassNames()
    {
        $this->getTestHelperNames();
        return $this;
    }

    /**
     * Initializes and loads fixtures data
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initFixtures()
    {
        $this->getHelper('uimap');
        $this->getHelper('data');
        return $this;
    }

    //@codingStandardsIgnoreStart
    /**
     * Get $helperName helper instance
     *
     * @param string $helperName config|data|dataGenerator|file|params|uimap
     *
     * @return Mage_Selenium_Helper_Uimap|Mage_Selenium_Helper_Params|Mage_Selenium_Helper_File|Mage_Selenium_Helper_DataGenerator|Mage_Selenium_Helper_Data|Mage_Selenium_Helper_Config
     * @throws OutOfRangeException
     */
    //@codingStandardsIgnoreEnd
    public function getHelper($helperName)
    {
        $class = 'Mage_Selenium_Helper_' . ucfirst($helperName);
        if (!class_exists($class)) {
            throw new OutOfRangeException($class . ' does not exist');
        }
        $variableName = '_' . preg_replace('/^[A-Za-z]/', strtolower($helperName[0]), $helperName) . 'Helper';
        if (is_null($this->$variableName)) {
            if (strtolower($helperName) !== 'params') {
                $this->$variableName = new $class($this);
            } else {
                $this->$variableName = new $class();
            }
        }
        return $this->$variableName;
    }

    /**
     * Set initial path
     *
     * @param string $path
     *
     * @return Mage_Selenium_TestConfiguration
     */
    public function setInitialPath($path)
    {
        $this->_initialPath = $path;
        return $this;
    }

    /**
     * Retrieve initial path
     *
     * @return string
     */
    public function getInitialPath()
    {
        return $this->_initialPath;
    }

    /**
     * @param array $files
     */
    public function setConfigData(array $files)
    {
        foreach ($files as $file) {
            if (preg_match('|' . '\/data\/' . '|', $file)) {
                $this->_configData[] = $file;
            }
        }
    }

    /**
     * @param array $files
     */
    public function setConfigUimapInclude(array $files)
    {
        $uimapFolders = $this->_configHelper->getConfigAreasUimapFolders();
        foreach ($files as $file) {
            if (!preg_match('|\/' . self::UIMAP_INCLUDE_FOLDER . '\/|', $file)) {
                continue;
            }
            foreach ($uimapFolders as $areaName => $uimapFolder) {
                $pattern = '\/' . self::UIMAP_INCLUDE_FOLDER . '\/' . $uimapFolder . '\.yml';
                if (preg_match('|' . $pattern . '|', $file)) {
                    $this->_configUimapInclude[$areaName][] = $file;
                }
            }
        }
    }

    /**
     * @param array $files
     * @param $codePoolName
     */
    public function setConfigUimap(array $files, $codePoolName)
    {
        $uimapFolders = $this->_configHelper->getConfigAreasUimapFolders();
        foreach ($files as $file) {
            if (!preg_match('|' . '\/uimap\/' . '|', $file)) {
                continue;
            }
            foreach ($uimapFolders as $areaName => $uimapFolder) {
                $pattern = '\/uimap\/' . $uimapFolder . '\/';
                if (preg_match('|' . $pattern . '|', $file)) {
                    $this->_configUimap[$codePoolName][$areaName][] = $file;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getConfigUimap()
    {
        return $this->_configUimap;
    }

    /**
     * @return array
     */
    public function getConfigUimapInclude()
    {
        return $this->_configUimapInclude;
    }

    /**
     * @return array
     */
    public function getConfigData()
    {
        return $this->_configData;
    }

    /**
     * Get all test helper class names
     * @return array
     */
    public function getTestHelperNames()
    {
        if (!empty($this->_testHelperNames)) {
            return $this->_testHelperNames;
        }
        //Get initial path to test helpers
        $frameworkConfig = $this->_configHelper->getConfigFramework();
        $initialPath = $this->getInitialPath() . $frameworkConfig['testsuite_base_path'];
        //Get test helpers sequence
        $fallbackOrderHelper = $this->_configHelper->getHelpersFallbackOrder();

        $facade = new File_Iterator_Facade();
        foreach ($fallbackOrderHelper as $codePoolName) {
            $projectPath = $initialPath . '/' . $codePoolName;
            if (!is_dir($projectPath)) {
                continue;
            }
            $files = $facade->getFilesAsArray($projectPath, 'Helper.php');
            foreach ($files as $file) {
                $className = str_replace($initialPath . '/', '', $file);
                $className = str_replace('/', '_', str_replace('.php', '', $className));
                $array = explode('_', str_replace('_Helper', '', $className));
                $helperName = end($array);
                $this->_testHelperNames[$helperName] = $className;
            }
        }
        return $this->_testHelperNames;
    }

    /**
     * Get node|value by path
     *
     * @param array  $data Array of Configuration|DataSet data
     * @param string $path XPath-like path to Configuration|DataSet value
     *
     * @return array|string|bool
     */
    public function _descend($data, $path)
    {
        $pathArr = (!empty($path)) ? explode('/', $path) : '';
        $currNode = $data;
        if (!empty($pathArr)) {
            foreach ($pathArr as $node) {
                if (isset($currNode[$node])) {
                    $currNode = $currNode[$node];
                } else {
                    return false;
                }
            }
        }
        return $currNode;
    }
}