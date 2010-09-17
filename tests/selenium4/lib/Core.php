<?php
/**
 * Core functionality for Selenium TestSuit
 *
 * @author Magento Inc.
 */
final class Core
{
    /**
     * Debug level constants
     */
    const DEBUG_LEVEL_OFF   = 1000;
    const DEBUG_LEVEL_INFO  = 1;
    const DEBUG_LEVEL_DEBUG = 2;
    const DEBUG_LEVEL_ERROR = 3;

    private static $_debugLevelLabels = array(
        self::DEBUG_LEVEL_INFO  => 'INFO',
        self::DEBUG_LEVEL_DEBUG => 'DEBUG',
        self::DEBUG_LEVEL_ERROR => 'ERROR',
        self::DEBUG_LEVEL_OFF   => 'SILENT',
    );

    /**
     * Magento environment that is being tested
     * Default value can be overridden by passing version GET parameter to the Bootstrap
     */
    private static $_magentoEnv = 'test';

    /**
     * Environment unique stamp used to for creating similar instances
     * of the same test case executed several times
     *
     * @var string
     */
    private static $_magentoStamp = null;

    /**
     * Debug level
     *
     * @var int
     */
    private static $_debugLevel = self::DEBUG_LEVEL_ERROR;

    /**
     * System config container
     *
     * @var array
     */
    private static $_config = array();

    /**
     * Registry storage
     *
     * @var array
     */
    private static $_registry = array();

    /**
     * Must be run in the Bootstrap before any other code running
     */
    static public function init()
    {
        self::$_config = self::_loadConfig();

        self::$_magentoEnv = self::getConfig('env', self::$_magentoEnv);
        $configPath = self::getConfig('paths/envPath');

        if (getenv('SELENIUM_ENV')) {
            self::$_magentoEnv = getenv('SELENIUM_ENV');
        }

        if (getenv('SELENIUM_STAMP')) {
            self::$_magentoStamp = getenv('SELENIUM_STAMP');
        } else {
            self::$_magentoStamp = date('Ymd/His');
        }

        // Fetching debug level
        if (getenv('SELENIUM_DEBUG_LEVEL')) {
            $debug = getenv('SELENIUM_DEBUG_LEVEL');
        } else {
            $debug = self::getConfig('debugLevel');
        }
        
        switch (strtoupper($debug)) {
            case '0':
            case 'OFF':
            case self::DEBUG_LEVEL_OFF:
            case self::$_debugLevelLabels[self::DEBUG_LEVEL_OFF]:
                self::$_debugLevel = self::DEBUG_LEVEL_OFF;
                break;
            case self::DEBUG_LEVEL_INFO:
            case self::$_debugLevelLabels[self::DEBUG_LEVEL_INFO]:
                self::$_debugLevel = self::DEBUG_LEVEL_INFO;
                break;
            case self::DEBUG_LEVEL_DEBUG:
            case self::$_debugLevelLabels[self::DEBUG_LEVEL_DEBUG]:
                self::$_debugLevel = self::DEBUG_LEVEL_DEBUG;
                break;
            default:
                self::$_debugLevel = self::DEBUG_LEVEL_ERROR;
        }

        echo "\n*** Debug level:       " . self::$_debugLevelLabels[self::$_debugLevel];
        echo "\n*** Environment stamp: " . self::getStamp() . "\n\n";

        $envConfigPath = rtrim($configPath, DS) . DS . self::$_magentoEnv;

        self::$_config['environment']['config'] = self::_loadConfig($envConfigPath, 'config');
        self::$_config['environment']['map']    = self::_loadConfig($envConfigPath, 'map');
        $refs = self::_loadConfig($configPath, 'references');
        if (isset($refs['testCase']) && $refs['testCase']) {
            foreach ($refs['testCase'] as $testCase) {
                self::$_config['references'][$testCase['id']] = array(
                    'id'    => $testCase['id'],
                    'name'  => $testCase['name'],
                    'source' => $testCase['source'],
                );
            }
        }
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
     * Retrieve an environment stamp
     *
     * @return string
     */
    public static function getStamp()
    {
        return self::getEnvironment() . '_' > self::$_magentoStamp;
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
        $ref =& self::$_config;

        $keys = explode('/', $configPath);
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                Core::debug('No config data for key == <' . $key . '>', Core::DEBUG_LEVEL_ERROR);
                return $default;
            }

            if (is_string($value[$key]) && preg_match('/^#include\s+(.+)$/', $value[$key], $m)) {
                $basePath = $keys[0] == 'environment' ? 'config' . DS . self::$_magentoEnv : 'config';
                $pathName = $basePath . DS . pathinfo($m[1], PATHINFO_DIRNAME);
                $fileName = pathinfo($m[1], PATHINFO_FILENAME);
                $value[$key] = $ref[$key] = self::_loadConfig($pathName, $fileName);
            }

            $value = $value[$key];
            $ref =& $ref[$key];
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

        return self::getConfig('environment/config/' . trim($configPath, '/'), $default);
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

        return self::getConfig('environment/map/' . trim($configPath, '/'), $default);
    }

    /**
     * Fetch the test suite class name by the test ID in config
     *
     * @param string $testId
     * @return string
     */
    public static function getTestSuiteSource($testId)
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
     * Debug method
     * Puts debug $line to output acording to the debug mode and debug level
     *
     * @param string $line
     * @param int $level
     */
    public static function debug($line, $level = self::DEBUG_LEVEL_INFO)
    {
//        echo ('zzzz=' . self::$_debugLevel);
        if ($level <= self::$_debugLevel) {
            echo "\n" . self::$_debugLevelLabels[$level] . ': ' . $line;
        }
    }

    /**
     * Check if the key exists in the registry
     *
     * @param string $key
     * @return boolean
     */
    public static function rCheck($key)
    {
        return isset(self::$_registry[$key]);
    }

    /**
     * Retrieve a value from the registry
     *
     * @param string $key
     * @return mixed
     */
    public static function rGet($key)
    {
        return self::rCheck($key) ? self::$_registry[$key] : null;
    }

    /**
     * Set a named value to the registry
     *
     * @param string $key
     * @param mixed $value
     */
    public static function rSet($key, $value)
    {
        self::$_registry[$key] = $value;
    }

    /**
     * Dispose the key value in the registry
     *
     * @param string $key
     */
    public static function rUnset($key)
    {
        if (self::rCheck($key)) {
            unset(self::$_registry[$key]);
        }
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

}
