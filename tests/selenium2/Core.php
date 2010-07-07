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
        if (isset($_GET['version'])) {
            self::$_magentoVersion = $_GET['version'];
        }
        if (isset($_GET['env'])) {
            self::$_magentoEnv = $_GET['env'];
        }
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
     * R a helper Singleton instance
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

}
