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

}
