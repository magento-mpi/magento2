<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests entry point. Implements application installation, initialization and uninstall
 */
class Magento_Test_Bootstrap
{
    /**
     * Name for DB backups, used by bootstrap
     */
    const DB_BACKUP_NAME = 'bootstrap_backup';

    /**
     * @var Magento_Test_Bootstrap
     */
    private static $_instance;

    /**
     * Filename of an existing local.xml configuration file
     *
     * @var string
     */
    protected $_localXmlFile;

    /**
     * @var SimpleXMLElement
     */
    protected $_localXml;

    /**
     * Root directory of the Magento source code
     *
     * @var string
     */
    protected $_magentoDir;

    /**
     * Directory with tests-specific *.xml configuration files
     *
     * @var string
     */
    protected $_testsEtcDir;

    /**
     * Additional directory with tests-specific *.xml configuration files
     *
     * @var string
     */
    protected $_additionalTestsEtcDirs;

    /**
     * Installation destination directory
     *
     * @var string
     */
    protected $_installDir;

    /**
     * Installation destination directory with configuration files
     *
     * @var string
     */
    protected $_installEtcDir;

    /**
     * Application initialization options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * DB vendor name
     *
     * @var string
     */
    protected $_dbVendorName = '';

    /**
     * DB vendor adapter instance
     *
     * @var Magento_Test_Db_DbAbstract
     */
    protected $_db = null;

    /**
     * Method to be ran on object destruction
     *
     * @var string
     */
    protected $_shutdownMethod = null;

    /**
     * Set self instance for static access
     *
     * @param Magento_Test_Bootstrap $instance
     */
    public static function setInstance(Magento_Test_Bootstrap $instance)
    {
        self::$_instance = $instance;
    }

    /**
     * Self instance getter
     *
     * @return Magento_Test_Bootstrap
     * @throws Exception
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new Exception('Bootstrap instance is not defined yet.');
        }
        return self::$_instance;
    }

    /**
     * Check the possibility to send headers or to use headers related function (like set_cookie)
     *
     * @return bool
     */
    public static function canTestHeaders()
    {
        if (!headers_sent() && extension_loaded('xdebug') && function_exists('xdebug_get_headers')) {
            return true;
        }
        return false;
    }

    /**
     * Initialize DB configuration, db vendor and install dir
     *
     * @param string $dbVendorName
     * @param string $magentoDir
     * @param string|array $testsEtcDir
     * @param string $tmpDir
     */
    public function __construct($dbVendorName, $magentoDir, $testsEtcDir, $tmpDir)
    {

        $this->_dbVendorName = $dbVendorName;
        $this->_magentoDir = $magentoDir;

        //For backward compatibility If test directory is an array
        //it's first element used as a main tests etc directory
        //and all other elements are used as additional etc directories
        if (is_array($testsEtcDir)) {
            $_testsEtcDir = array_shift($testsEtcDir);
            $this->_additionalTestsEtcDirs = $testsEtcDir;
            $testsEtcDir = $_testsEtcDir;
        }
        $this->_testsEtcDir = $testsEtcDir;

        $this->_readLocalXml();

        $this->_verifyDirectories($tmpDir);

        $this->_installDir = "{$tmpDir}/sandbox-{$this->_dbVendorName}-" . md5_file($this->_localXmlFile);
        $this->_installEtcDir = $this->_installDir . '/etc';

        $this->_db = $this->_instantiateDb();

        $this->_emulateEnvironment();

        if ($this->_isInstalled()) {
            $this->initialize();
        } else {
            $this->_db->verifyEmptyDatabase();
            $this->_install();
        }
    }

    /**
     * Get DB vendor name
     *
     * @return string
     */
    public function getDbVendorName()
    {
        return $this->_dbVendorName;
    }

    /**
     * Get Magento dir
     *
     * @return string
     */
    public function getMagentoDir()
    {
        return $this->_magentoDir;
    }

    /**
     * Initialize an already installed Magento application
     *
     * @param string $scopeCode
     * @param string $scopeType
     */
    public function initialize($scopeCode = '', $scopeType = 'store')
    {
        if (!class_exists('Mage', false)) {
            require $this->_magentoDir . '/app/Mage.php';
        } else {
            $resource = Mage::registry('_singleton/core/resource');
            Mage::reset();
            if ($resource) {
                Mage::register('_singleton/core/resource', $resource);
            }
        }
        $this->_options = array(
            'etc_dir'     => $this->_installEtcDir,
            'var_dir'     => $this->_installDir,
            'tmp_dir'     => $this->_installDir . DIRECTORY_SEPARATOR . 'tmp',
            'cache_dir'   => $this->_installDir . DIRECTORY_SEPARATOR . 'cache',
            'log_dir'     => $this->_installDir . DIRECTORY_SEPARATOR . 'log',
            'session_dir' => $this->_installDir . DIRECTORY_SEPARATOR . 'session',
            'media_dir'   => $this->_installDir . DIRECTORY_SEPARATOR . 'media',
            'public_dir'  => $this->_installDir . DIRECTORY_SEPARATOR . 'pub',
            'skin_dir'    => $this->_installDir . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . 'skin',
            'upload_dir'  => $this->_installDir . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'upload',
        );

        Mage::app($scopeCode, $scopeType, $this->_options);
    }

    /**
     * Remove cached configuration and reinitialize the application
     */
    public function refreshConfiguration()
    {
        Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG));
        $this->initialize();
    }

    /**
     * Re-create empty temporary dir by specified
     *
     * @param string $optionCode
     * @throws Exception if one of protected directories specified
     */
    public function cleanupDir($optionCode)
    {
        if (in_array($optionCode, array('etc_dir', 'var_dir', 'media_dir', 'public_dir'))) {
            throw new Exception("Directory '{$optionCode}' must not be cleaned up while running tests.");
        }
        $dir = $this->_options[$optionCode];
        Varien_Io_File::rmdirRecursive($dir);
        mkdir($dir);
    }

    /**
     * Get application initialization options
     *
     * @return array
     */
    public function getAppOptions()
    {
        return $this->_options;
    }

    /**
     * Register tests shutdown action
     *
     * @param string $shutdownAction
     * @throws Exception
     */
    public function setShutdownAction($shutdownAction)
    {
        $shutdownMethod = '_doShutdown' . ucfirst($shutdownAction);
        if (!method_exists($this, $shutdownMethod)) {
            throw new Exception("Shutdown action '{$shutdownAction}' is not supported.");
        }
        $this->_shutdownMethod = $shutdownMethod;
    }

    /**
     * Perform requested shutdown operations
     */
    public function __destruct()
    {
        if ($this->_shutdownMethod) {
            call_user_func(array($this, $this->_shutdownMethod));
        }
    }

    /**
     * Perform 'uninstall' shutdown action
     */
    protected function _doShutdownUninstall()
    {
        $this->_db->cleanup();
        $this->_cleanupFilesystem();
    }

    /**
     * Perform 'restoreDatabase' shutdown action
     */
    protected function _doShutdownRestoreDatabase()
    {
        $this->_db->restoreBackup(self::DB_BACKUP_NAME);
    }

    /**
     * Load application local.xml file
     *
     * @throws Exception when the wrong DB vendor name is specified
     */
    protected function _readLocalXml()
    {
        $defaultLocalXml = "{$this->_testsEtcDir}/local-{$this->_dbVendorName}.xml.dist";
        $customLocalXml  = "{$this->_testsEtcDir}/local-{$this->_dbVendorName}.xml";
        $this->_localXmlFile = (is_file($customLocalXml) ? $customLocalXml : $defaultLocalXml);
        if (!is_file($defaultLocalXml)) {
            throw new Exception("Database vendor '{$this->_dbVendorName}' is not supported.");
        }
        $this->_localXml = simplexml_load_file($this->_localXmlFile);
    }

    /**
     * Check all required directories contents and permissions
     *
     * @param string $tmpDir
     * @throws Exception when any of required directories is not eligible
     */
    protected function _verifyDirectories($tmpDir)
    {
        /* Magento application dir */
        if (!is_file($this->_magentoDir . '/app/Mage.php')) {
            throw new Exception('Unable to locate Magento root folder and Mage.php.');
        }
        /* Temporary directory */
        if (!is_dir($tmpDir) || !is_writable($tmpDir)) {
            throw new Exception("The '{$tmpDir}' is not a directory or not writable.");
        }
    }

    /**
     * Create object of configured DB vendor adapter
     *
     * @return Magento_Test_Db_DbAbstract
     */
    protected function _instantiateDb()
    {
        $suffix = ucfirst($this->_dbVendorName);
        require_once dirname(__FILE__) . '/Db/DbAbstract.php';
        require_once dirname(__FILE__) . "/Db/{$suffix}.php";
        $class = "Magento_Test_Db_{$suffix}";
        $dbConfig = $this->_localXml->global->resources->default_setup->connection;
        $this->_ensureDirExists($this->_installDir);
        return new $class(
            (string)$dbConfig->host,
            (string)$dbConfig->username,
            (string)$dbConfig->password,
            (string)$dbConfig->dbname,
            $this->_installDir
        );
    }

    /**
     * Weather the application is installed or not
     *
     * @return bool
     */
    protected function _isInstalled()
    {
        return is_file($this->_installEtcDir . '/local.xml');
    }

    /**
     * Set environment variables or apply workarounds, so that they would be closer to real application
     */
    protected function _emulateEnvironment()
    {
        // emulate entry point to ensure that tests generate invariant URLs
        $_SERVER['SCRIPT_FILENAME'] = 'index.php';
        // prevent session_start, because it may rely on cookies
        $_SESSION = array();
        // application relies on a non-empty session ID
        session_id(uniqid());
    }

    /**
     * Create a directory with write permissions or don't touch existing one
     *
     * @throws Exception
     * @param string $dir
     */
    protected function _ensureDirExists($dir)
    {
        if (!file_exists($dir)) {
            $old = umask(0);
            mkdir($dir, 0777);
            umask($old);
        } else if (!is_dir($dir)) {
            throw new Exception("'$dir' is not a directory.");
        }
    }

    /**
     * Install application using temporary directory and vendor-specific database settings
     */
    protected function _install()
    {
        $this->_ensureDirExists($this->_installDir);
        $this->_ensureDirExists($this->_installEtcDir);
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'media');
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'skin');
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'pub');

        $magentoEtcDir = $this->_magentoDir . '/app/etc';

        /**
         * Source etc directories contains all directories with XML files, excluding modules etc directory
         *
         * @var array $sourceEtcDirs
         */
        $sourceEtcDirs = array($magentoEtcDir, $this->_testsEtcDir);
        if (is_array($this->_additionalTestsEtcDirs)) {
            $sourceEtcDirs = array_merge($sourceEtcDirs, $this->_additionalTestsEtcDirs);
        }
        /**
         * List of directories where xml files may be found
         *
         * @var array $allEtcDirs
         */
        $allEtcDirs = array_merge((array) "$magentoEtcDir/modules", $sourceEtcDirs);

        /* Copy *.xml configuration files */
        foreach ($allEtcDirs as $sourceEtcDir) {
            $targetEtcDir = str_replace(
                $sourceEtcDirs,
                $this->_installEtcDir,
                $sourceEtcDir
            );
            $this->_ensureDirExists($targetEtcDir);
            foreach (glob("$sourceEtcDir/*.xml") as $sourceXmlFile) {
                /* Skip "local.xml" files */
                if (preg_match('#[\\/].*?local\..*?$#', $sourceXmlFile)) {
                    continue;
                }
                $targetXmlFile = str_replace($sourceEtcDir, $targetEtcDir, $sourceXmlFile);
                copy($sourceXmlFile, $targetXmlFile);
            }
        }

        /* Make sure that local.xml contains an invalid installation date */
        $installDate = (string)$this->_localXml->global->install->date;
        if ($installDate && strtotime($installDate)) {
            throw new Exception("Configuration file '$this->_localXmlFile' must contain an invalid installation date.");
        }

        /* Replace local.xml */
        $targetLocalXml = $this->_installEtcDir . '/local.xml';
        copy($this->_localXmlFile, $targetLocalXml);

        /* Initialize an application in non-installed mode */
        $this->initialize();

        /* Run all install and data-install scripts */
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        Mage_Core_Model_Resource_Setup::applyAllDataUpdates();

        /* Enable configuration cache by default in order to improve tests performance */
        Mage::app()->getCacheInstance()->saveOptions(array('config' => 1));

        /* Fill installation date in local.xml to indicate that application is installed */
        $localXml = file_get_contents($targetLocalXml);
        $localXml = str_replace($installDate, date('r'), $localXml, $replacementCount);
        if ($replacementCount != 1) {
            throw new Exception("Unable to replace installation date properly in '$targetLocalXml' file.");
        }
        file_put_contents($targetLocalXml, $localXml, LOCK_EX);

        /* Make a database backup to be able to restore it to initial state any time */
        $this->_db->createBackup(self::DB_BACKUP_NAME);

        /* Switch an application to installed mode */
        $this->initialize();

        /* Perform Reindex */
        $this->_refreshIndexes();
    }

    /**
     * Remove temporary files and directories from the filesystem
     */
    protected function _cleanupFilesystem()
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->_installDir),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        /* On Windows iterator excludes iterated directory itself */
        if (is_dir($this->_installDir)) {
            rmdir($this->_installDir);
        }
    }

    /**
     * Refresh all indexes
     */
    protected function _refreshIndexes()
    {
        if (defined('TESTS_REINDEX_ONSTART') && TESTS_REINDEX_ONSTART) {
            /** @var $processCollection Mage_Index_Model_Mysql4_Process_Collection */
            $processCollection = Mage::getResourceModel('index/process_collection');
            /** @var $process Mage_Index_Model_Process */
            foreach ($processCollection as $process) {
                $process->reindexAll();
            }
        }
    }
}
