<?php
/**
 * Core functionality for Selenium TestSuit
 *
 * @author Magento Inc.
 */
final class Core
{
    /**
     * Magento version that is being tested
     * Default value can be overridden by passing version GET parameter to the Bootstrap
     */
    private static $_magentoVersion = '1.8';

    /**
     * Magento environment that is being tested
     * Default value can be overridden by passing version GET parameter to the Bootstrap
     */
    private static $_magentoEnv = 'production';

    /**
     * TestCase context instantiated for helpers
     *
     * @var Test_Abstract
     */
    private static $_context = null;

    /**
     * System config container
     *
     * @var array
     */
    private static $_config = array();

    /**
     * Helper instances for their Singleton implementation
     *
     * @var array
     */
    private static $_helperInstances = array();

    /**
     * Must be run in the Bootstrap before any other code running
     */
    static public function init()
    {
        self::$_config = self::_loadConfig();

        self::$_magentoVersion = self::getConfig('environment/version', self::$_magentoVersion);
        self::$_magentoEnv = self::getConfig('environment/stage', self::$_magentoEnv);
        $configPath = self::getConfig('paths/env');

        if (isset($_GET['version'])) {
            self::$_magentoVersion = $_GET['version'];
        }
        if (isset($_GET['env'])) {
            self::$_magentoEnv = $_GET['env'];
        }

        $stageConfigPath = rtrim($configPath, DS) . DS . self::$_magentoVersion . DS . self::$_magentoEnv;

        self::$_config['environment']['config'] = self::_loadConfig($stageConfigPath, 'config');
        self::$_config['environment']['map']    = self::_loadConfig($stageConfigPath, 'map');

        // print_r(self::$_config);
    }

    /**
     * Fetches a version of the Magento being tested
     *
     * @return string
     */
    public static function getVersion()
    {
        return self::$_magentoVersion;
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
            return null;
        }

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
     * Set a context for helpers
     *
     * @param Test_Abstract $testCase
     */
    public static function setContext(Test_Abstract $testCase)
    {
        self::$_context = $testCase;
    }

    public static function getContext()
    {
        if ((null === self::$_context) || !(self::$_context instanceof Test_Abstract)) {
            throw new Exception('Testing context has not been set');
        }    
        return self::$_context;
    }

    /**
     * Return a helper Singleton instance
     *
     * @param string $helperName
     * @return Helper_Abstract
     */
    public static function getHelper($helperName)
    {
        $helperClassName = 'Helper_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $helperName)));

        if (!isset(self::$_helperInstances[$helperClassName])) {
            self::$_helperInstances[$helperClassName] = new $helperClassName();
        } 
        return self::$_helperInstances[$helperClassName];
    }

/**
     * Reset the helper Singleton instance
     *
     * @param string $helperName
     * @return Helper_Abstract
     */
    public static function resetHelper($helperName)
    {
        $helperClassName = 'Helper_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $helperName)));

        self::$_helperInstances = array ();
        return self;
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
        $children = $obj->children();
        $executed = false;

        foreach ($children as $elementName => $node) {
            if (array_key_exists($elementName, $arr)) {
                if (array_key_exists(0, $arr[$elementName])) {
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
        $fileName = rtrim($path, DS) . DS . $fileName . '.xml';

        if (is_readable($fileName)) {
            $xml = simplexml_load_file($fileName);
            if ($xml) {
                self::_convertXmlObjToArr($xml, $config);
            }
        }

        return $config;
    }

}
