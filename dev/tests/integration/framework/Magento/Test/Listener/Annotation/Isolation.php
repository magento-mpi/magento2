<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Implementation of the @magentoAppIsolation doc comment directive
 */
class Magento_Test_Listener_Annotation_Isolation
{
    /**
     * @var Magento_Test_Listener
     */
    protected $_listener;

    /**
     * Flag to prevent an excessive test case isolation if the last test has been just isolated
     *
     * @var bool
     */
    private $_hasNonIsolatedTests = true;

    /**
     * Constructor
     *
     * @param Magento_Test_Listener $listener
     */
    public function __construct(Magento_Test_Listener $listener)
    {
        $this->_listener = $listener;
    }

    /**
     * Isolate global application objects
     */
    protected function _isolateApp()
    {
        if ($this->_hasNonIsolatedTests) {
            $this->_cleanupCache();
            Magento_Test_Bootstrap::getInstance()->initialize();
            $this->_hasNonIsolatedTests = false;
        }
    }

    /**
     * Remove cache polluted by other tests excluding performance critical cache (configuration, ddl)
     */
    protected function _cleanupCache()
    {
        /*
         * Cache cleanup relies on the initialized config object, which could be polluted from within a test.
         * For instance, any test could explicitly call Mage::reset() to destroy the config object.
         */
        $expectedOptions = Magento_Test_Bootstrap::getInstance()->getAppOptions();
        $actualOptions = Mage::getConfig() ? Mage::getConfig()->getOptions()->getData() : array();
        $isConfigPolluted = array_intersect_assoc($expectedOptions, $actualOptions) !== $expectedOptions;
        if ($isConfigPolluted) {
            Magento_Test_Bootstrap::getInstance()->initialize();
        }
        Mage::app()->getCache()->clean(
            Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG,
            array(Mage_Core_Model_Config::CACHE_TAG,
                Varien_Db_Adapter_Pdo_Mysql::DDL_CACHE_TAG,
                'DB_PDO_MSSQL_DDL', // Varien_Db_Adapter_Pdo_Mssql::DDL_CACHE_TAG
                'DB_ORACLE_DDL', // Varien_Db_Adapter_Oracle::DDL_CACHE_TAG
            )
        );
    }

    /**
     * Isolate application before running test case
     */
    public function startTestSuite()
    {
        $this->_isolateApp();
    }

    /**
     * Handler for 'endTest' event
     *
     * @throws Magento_Exception
     */
    public function endTest()
    {
        $test = $this->_listener->getCurrentTest();

        $this->_hasNonIsolatedTests = true;

        /* Determine an isolation from doc comment */
        $annotations = $test->getAnnotations();
        if (isset($annotations['method']['magentoAppIsolation'])) {
            $isolation = $annotations['method']['magentoAppIsolation'];
            if ($isolation !== array('enabled') && $isolation !== array('disabled')) {
                throw new Magento_Exception(
                    'Invalid "@magentoAppIsolation" annotation, can be "enabled" or "disabled" only.'
                );
            }
            $isIsolationEnabled = ($isolation === array('enabled'));
        } else {
            /* Controller tests should be isolated by default */
            $isIsolationEnabled = ($test instanceof Magento_Test_TestCase_ControllerAbstract);
        }

        if ($isIsolationEnabled) {
            $this->_isolateApp();
        }
    }
}
