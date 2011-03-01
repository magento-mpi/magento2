<?php
class WebService_Utils_TestSuite_Abstract extends Mage_TestSuite
{
    protected $_configFilePath = null;
    protected $_suite = null;
    protected $_dirClassPath = null;

    protected function setUp()
    {
//        if ( !WebService_Fixtures_Fixtures::run() ) {
//            $this->markTestSuiteSkipped(WebService_Fixtures_Fixtures::getErrorMessage());
//        }
        WebService_Helper_Data::set('Suite', $this->_suite);

        WebService_Helper_Data::set(
            'pathToImplementation',
            'WebService/'.WebService_Helper_Xml::getValueByPath($this->_configFilePath, '//root/path/implementation')
        );
        WebService_Helper_Data::set(
            'pathToConfig',
            $this->_dirClassPath.DS.WebService_Helper_Xml::getValueByPath($this->_configFilePath, '//root/path/config')
        );
    }

    protected function tearDown()
    {
    }

    final protected function _initSuite()
    {
        $moduleList = WebService_Helper_Xml::getModuleList($this->_configFilePath);
        
        $testPath = 'WebService/'.WebService_Helper_Xml::getValueByPath($this->_configFilePath, '//root/path/test');
        foreach ($moduleList as $moduleName) {
            $modelPath = WebService_Helper_Xml::getValueByPath($this->_configFilePath, '//root/modules/'.$moduleName.'/model');
            $extendedTestCaseFilePath = $this->_dirClassPath.DS."..".DS.WebService_Helper_Data::transformPath($testPath.'/'.$this->_suite.'/'.$modelPath.'TestCase').'.php';
            if (is_file($extendedTestCaseFilePath)) {
                $modulePath = $testPath.'/'.$this->_suite.'/'.$modelPath.'TestCase';
            } else {
                $modulePath = $testPath.'/' .$modelPath.'TestCase';
            }

            $this->addTestSuite(WebService_Helper_Data::transformToClass($modulePath));
        }
    }
}


