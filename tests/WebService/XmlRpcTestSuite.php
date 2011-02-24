<?php
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/../');
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'Mage.php';

class XmlRpcTestSuite extends Mage_TestSuite
{
    protected static $_configFilePath = null;

    protected function setUp()
    {
        if ( !WebService_Fixtures_Fixtures::run() ) {
            var_dump('Applying fixtures to DB: ' . WebService_Fixtures_Fixtures::getDbName());
            var_dump(WebService_Fixtures_Fixtures::getErrorMessage());
            $this->markTestSuiteSkipped(WebService_Fixtures_Fixtures::getErrorMessage());
        }
        WebService_Helper_Data::set('Suite', 'Api/XmlRpc');

        WebService_Helper_Data::set(
            'pathToImplementation',
            'WebService/'.WebService_Helper_Xml::getValueByPath( self::$_configFilePath, '//root/path/implementation')
        );
        WebService_Helper_Data::set(
            'pathToConfig',
            dirname(__FILE__).'/'.WebService_Helper_Xml::getValueByPath( self::$_configFilePath, '//root/path/config')
        );
    }

    protected function tearDown()
    {
        WebService_Connector_Provider::disconnect('XmlRpc');
    }
    
    public static function suite()
    {
        self::$_configFilePath = dirname(__FILE__).'/etc/config.xml';
        
        $moduleList = WebService_Helper_Xml::getModuleList( self::$_configFilePath );

        $suite = new XmlRpcTestSuite();

        $testPath = 'WebService/'.WebService_Helper_Xml::getValueByPath( self::$_configFilePath, '//root/path/test');
        foreach($moduleList as $moduleName) {
            $modelPath = WebService_Helper_Xml::getValueByPath( self::$_configFilePath, '//root/modules/'.$moduleName.'/model');
            $modulePath = $testPath.'/'.$modelPath.'TestCase';
            $suite->addTestSuite( WebService_Helper_Data::transformToClass($modulePath) );
        }
        
        return $suite;
    }
}
