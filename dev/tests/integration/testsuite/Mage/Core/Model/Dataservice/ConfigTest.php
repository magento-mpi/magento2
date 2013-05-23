<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Dataservice_Config
     */
    protected $_config;

    public function setup() {
        $xml
            = <<<XML
<?xml version="1.0"?>
<config>
    <modules>
        <Testmodule>
            <version>1.6.0.0.23</version>
            <active>true</active>
            <depends/>
        </Testmodule>
    </modules>
    <global>
        <di/>
        <service_calls>
            <testmodule>
                <file>Mage_TestModule/service_calls.xml</file>
            </testmodule>
        </service_calls>
    </global>
</config>
XML;
        $dirs = Mage::getObjectManager()->create(
            'Mage_Core_Model_Dir', array(
                                        'baseDir' => array(__DIR__ . '/_files'),
                                        'dirs'    => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'),
                                   )
        );
        $loader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules', array('dirs' => $dirs,)
        );
        $config = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Base', array($xml)
        );
        $loader->load($config);
        $fileReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules_File', array('dirs' => $dirs)
        );
        $this->_config = new Mage_Core_Model_Dataservice_Config($config, $fileReader);
    }

    public function testGetClassByAlias() {
        $classInfo = $this->_config->getClassByAlias('alias');
        $this->assertEquals('some_class_name', $classInfo['class']);
        $this->assertEquals('some_method_name', $classInfo['retrieveMethod']);
        $this->assertEquals(array('some_arg_name' => 'some_arg_value'), $classInfo['methodArguments']);
    }
}