<?php
/**
 * Core functionality for Selenium TestSuit
 *
 * @author Magento Inc.
 */
final class Core
{
    /**
     * Magento environment that is being tested
     * Default value can be overridden by passing version GET parameter to the Bootstrap
     */
    private static $_magentoEnv = 'test';

    /**
     * Manage debug function outputs.
     * 0 - silent function
     * 9 - all debug information printed
     * @var int
     */
    protected static $_debugLevel  = 7;

    /**
     * System config container
     *
     * @var array
     */
    private static $_config = array();

    /**
     * Must be run in the Bootstrap before any other code running
     */
    static public function init()
    {
        self::$_config = self::_loadConfig();

        self::$_magentoEnv = self::getConfig('env', self::$_magentoEnv);
        $configPath = self::getConfig('paths/envPath');

        if (isset($_GET['env'])) {
            self::$_magentoEnv = $_GET['env'];
        }

        $envConfigPath = rtrim($configPath, DS) . DS . self::$_magentoEnv;

        self::$_config['environment']['config'] = self::_loadConfig($envConfigPath, 'config');
        self::$_config['environment']['map']    = self::_loadConfig($envConfigPath, 'map');
        $refs = self::_loadConfig($configPath, 'references');
        if (isset($refs['testCase']) && $refs['testCase']) {
            foreach ($refs['testCase'] as $testCase) {
                self::$_config['references'][$testCase['id']] = array(
                    'id'    => $testCase['id'],
                    'name'  => $testCase['name'],
                    'class' => $testCase['testClass'],
                );
            }
        }

        // print_r(self::$_config['environment']['map']);
    }

    /**
     * Fetches an environment identifier of the Magento being tested
     *
     * @return string
     */
    public static function getEnvironment()
    {
        return self::$_magentoEnv;
    }

    /**
     * Fetch a value from the loaded config
     *
     * @param string $configPath
     * @param array | string $default
     * @return array | string
     */
    public static function getConfig($configPath, $default = null)
    {
        $value = self::$_config;

        $keys = explode('/', $configPath);
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                self::debug("WARNING: No config data for key == ".$configPath,4);
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Fetch a config parameter from the loaded environment
     *
     * @param string $configPath
     * @param array | string $default
     * @return array | string
     */
    public static function getEnvConfig($configPath, $default = null)
    {
        if (!$configPath) {
            self::debug("WARNING: No config data for key == ".$configPath,4);
            return null;
        }
        self::debug("getEnvConfig(".'environment/config/'.$configPath."): ".self::getConfig('environment/config/' . $configPath),7);
        return self::getConfig('environment/config/' . $configPath);
    }

    /**
     * Fetch a UI map from the loaded environment
     *
     * @param string $configPath
     * @param array | string $default
     * @return array | string
     */
    public static function getEnvMap($configPath, $default = null)
    {
        if (!$configPath) {
            return null;
        }

        return self::getConfig('environment/map/' . $configPath);
    }

    /**
     * Fetch the test suite class name by the test ID in config
     *
     * @param string $testId
     * @return string
     */
    public static function getTestSuiteClassName($testId)
    {
        return self::getConfig('references/' . $testId . '/source');
    }

    /**
     * Fetch the test suite title by the test ID in config
     *
     * @param string $testId
     * @return string
     */
    public static function getTestSuiteTitle($testId)
    {
        return self::getConfig('references/' . $testId . '/name');
    }

    /**
     * Parse a SimpleXMLElement object recursively into an Array.
     * Attention: attributes skipped
     *
     *
     * @param $xml The SimpleXMLElement object
     * @param $arr Target array where the values will be stored
     * @return NULL
     */
    private static function _convertXmlObjToArr($obj, &$arr)
    {
        $children   = $obj->children();
        $executed = false;

        foreach ($children as $elementName => $node) {
            if (is_array($arr) && array_key_exists($elementName, $arr)) {
                if (is_array($arr) && array_key_exists(0, $arr[$elementName])) {
                    $i = count($arr[$elementName]);
                    self::_convertXmlObjToArr($node, $arr[$elementName][$i]);
                } else {
                    $tmp = $arr[$elementName];
                    $arr[$elementName] = array();
                    $arr[$elementName][0] = $tmp;
                    $i = count($arr[$elementName]);
                    self::_convertXmlObjToArr($node, $arr[$elementName][$i]);
                }
            } else {
                $arr[$elementName] = array();
                self::_convertXmlObjToArr($node, $arr[$elementName]);
            }

            $executed = true;
        }

        if (!$executed && $children->getName() == "") {
            $arr = (string) $obj;
        }

        return;
    }

    /**
     * Load an XML config file and convert it into array
     *
     * @param string $path
     * @param string $fileName
     * @return array
     */
    protected static function _loadConfig($path = 'config', $fileName = 'config')
    {
        $config = array();
        $fileName = realpath(BASE_DIR . DS . rtrim($path, DS)) . DS . $fileName . '.xml';

        if (is_readable($fileName)) {
            $xml = simplexml_load_file($fileName);
            if ($xml) {
                self::_convertXmlObjToArr($xml, $config);
            }
        }

        return $config;
    }

    /**
     * Debug function
     * Puts debug $line to output
     */
    public static function debug($line, $level=5)
    {
        if ($level<=self::$_debugLevel) {
            echo $line."\n";
        }
    }

}
