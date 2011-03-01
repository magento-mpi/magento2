<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Fixtures
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class WebService_Fixtures_Fixtures {

    protected static $_config;
    protected static $_dbConnection;
    protected static $_installer;
    protected static $_fixturesDir;
    protected static $_error;
    protected static $_appliedFixtures;

    /**
     * Starts fixtures processing.
     */
    public static function run() {
        Mage::app('admin');
        self::$_error = null;
        self::$_appliedFixtures = null;

        self::getDBConnection();
        self::init();
        self::applyFixtures();

        return is_null(self::$_error);
    }
    /**
     * Get Exception Message
     *
     * @return string
     */
    public static function getErrorMessage()
    {
        return self::$_error;
    }

    /**
     * Initiates connection to DB
     */
    private static function getDBConnection()
    {
        /** @var self::$_installer Mage_Core_Model_Resource_Setup */
        self::$_installer = new Mage_Core_Model_Resource_Setup('');

        /** @var self::$_dbConnection Varien_Db_Adapter_Pdo_Mysql */
        self::$_dbConnection = self::$_installer->getConnection();
    }

    /**
     * Initiates an fixtures variables
     */
    private static function init() {

        $xml                = simplexml_load_file(realpath(dirname(__FILE__).'/../etc/config.xml'));
        self::$_config      = WebService_Helper_Xml::simpleXMLToArray($xml);
        self::$_fixturesDir    = realpath(dirname(__FILE__).'/../Fixtures/fixtures/') . '/';

        self::applyFixtures();
    }

    private static function applyFixtures(){
        $appliedFixtures = self::getAppliedFixtures();
        self::$_installer->startSetup();
        try {
            foreach(self::$_config['fixtures']['befor_test'] AS $module_name => $module_conf){
                if(isset($module_conf['is_active'])
                        && isset($module_conf['path'])
                        && ($module_conf['is_active'] == "true"
                            || $module_conf['is_active'] == "1"
                            || strtolower($module_conf['is_active']) == "on")
                        && !in_array($module_name, $appliedFixtures))
                {
                    $filePath = self::$_fixturesDir . $module_conf['path'];

                    if(is_file($filePath)){
                        $query = preg_replace('/\`(.+)?\`/i', '`'.Mage::app()->getConfig()->getTablePrefix().'$1`', file_get_contents($filePath) );
                        if(strlen($query)){
                            self::$_installer->run($query);
                            $appliedFixtures[] = $module_name;
                        } else {
                            echo "File is empty or can't be read: " . $filePath;
                        }
                    } else {
                        echo "File, does not exist: " . $filePath;
                    }
                }
            }
        } catch (Exception $e) {
             self::$_error = $e->getMessage().":\n".$e->getTraceAsString();
        }
        self::$_installer->endSetup();
        self::setAppliedFixtures($appliedFixtures);
    }

    public static function getAppliedFixtures() {
        if ( is_null(self::$_appliedFixtures) ){
            $io   = new Varien_Io_File();
            $io->open(array('path' => Mage::getBaseDir('log')));
            self::$_appliedFixtures = array();
            if ( $io->fileExists('fixtures.csv') ){
                $io->streamOpen('fixtures.csv', 'r');
                $appliedFixture = $io->streamReadCsv();
                while ( is_array($appliedFixture) ){
                    self::$_appliedFixtures[] = array_shift($appliedFixture);
                    $appliedFixture = $io->streamReadCsv();
                }
            }
            $io->close();
            $io->open(array('path' => Mage::getBaseDir('etc') . DS . 'modules'));
            if( $io->fileExists("XEnterprise.xml") ) {
                self::$_appliedFixtures[] = 'Base';
            } else {
                self::$_appliedFixtures[] = 'Enterprice';
            }
            $io->close();
        }
        return self::$_appliedFixtures;
    }

    private static function setAppliedFixtures($arr = null) {
        if ( is_array($arr) ){
            self::$_appliedFixtures = array_keys(array_flip(self::$_appliedFixtures) + array_flip($arr));
        }
        $io   = new Varien_Io_File();
        $io->open(array('path' => Mage::getBaseDir('log')));

        $io->streamOpen('fixtures.csv');
        $io->streamLock();
        foreach (self::$_appliedFixtures as $modulName) {
            $io->streamWriteCsv(array($modulName));
        }
        $io->streamUnlock();
        $io->close();
    }

    public static function getDbName(){
        $res = self::$_dbConnection->query('SELECT DATABASE();');
        return array_shift($res->fetch(PDO::FETCH_ASSOC));
    }
}
