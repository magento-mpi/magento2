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
 * Implementation of the @magentoAppIsolation DocBlock annotation
 */
class Magento_Test_Annotation_AppIsolation
{
    /**
     * Flag to prevent an excessive test case isolation if the last test has been just isolated
     *
     * @var bool
     */
    private $_hasNonIsolatedTests = true;

    /**
     * @var Zend_Cache_Core
     */
    private $_cache;

    /**
     * Isolate global application objects
     */
    protected function _isolateApp()
    {
        if ($this->_hasNonIsolatedTests) {
            $this->_cleanupCache();
            $this->_resetWorkingDirectory();
            Magento_Test_Bootstrap::getInstance()->reinitialize();
            $this->_hasNonIsolatedTests = false;
        }
    }

    /**
     * Remove cache polluted by other tests excluding performance critical cache (configuration, ddl)
     */
    protected function _cleanupCache()
    {
        if (!$this->_cache) {
            $this->_cache = Mage::app()->getCache();
        }
        $this->_cache->clean(
            Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG,
            array(Mage_Core_Model_Config::CACHE_TAG,
                Varien_Db_Adapter_Pdo_Mysql::DDL_CACHE_TAG,
                'DB_PDO_MSSQL_DDL', // Varien_Db_Adapter_Pdo_Mssql::DDL_CACHE_TAG
                'DB_ORACLE_DDL', // Varien_Db_Adapter_Oracle::DDL_CACHE_TAG
            )
        );
    }

    /**
     * Reset current working directory (CWD)
     */
    protected function _resetWorkingDirectory()
    {
        chdir(Magento_Test_Bootstrap::getInstance()->getTestsDir());
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
     * @param PHPUnit_Framework_TestCase $test
     * @throws Magento_Exception
     */
    public function endTest(PHPUnit_Framework_TestCase $test)
    {
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
            $isIsolationEnabled = $isolation === array('enabled');
        } else {
            /* Controller tests should be isolated by default */
            $isIsolationEnabled = $test instanceof Magento_Test_TestCase_ControllerAbstract;
        }

        if ($isIsolationEnabled) {
            $this->_isolateApp();
        }
    }
}
