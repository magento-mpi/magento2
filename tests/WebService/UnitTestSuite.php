<?php
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/../');
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'Mage.php';

class UnitTestSuite extends Mage_TestSuite
{
    protected static $_configFilePath = null;

    protected function setUp()
    {
        WebService_Helper_Data::set('Suite', 'Unit');

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
    }

    public static function suite()
    {
        self::$_configFilePath = dirname(__FILE__).'/etc/config.xml';

        $moduleList = WebService_Helper_Xml::getModuleList( self::$_configFilePath );

        $suite = new UnitTestSuite();
        $testPath = 'WebService/'.WebService_Helper_Xml::getValueByPath( self::$_configFilePath, '//root/path/test');
        foreach($moduleList as $moduleName) {
            $modelPath = WebService_Helper_Xml::getValueByPath( self::$_configFilePath, '//root/modules/'.$moduleName.'/model');
            $modulePath = $testPath.'/'.$modelPath.'TestCase';
//            $groups = array($moduleName);
//            $class = new ReflectionClass(WebService_Helper_Data::transformToClass($modulePath));
//            $methods = $class->getMethods();
//            foreach( $methods as $method ) {
//                if( strpos($method->name, 'test') === 0 ) {
//                    $suite->addTestMethod($class, $method, &$groups);
//                }
//            }

            // $suite->addTestMethod(new ReflectionClass(WebService_Helper_Data::transformToClass($modulePath)), 'testCreate', $suite->getName());
            //$suite->addTestFile( $modulePath.'.php');
            $suite->addTestSuite( WebService_Helper_Data::transformToClass($modulePath) );
        }

        return $suite;
    }
}