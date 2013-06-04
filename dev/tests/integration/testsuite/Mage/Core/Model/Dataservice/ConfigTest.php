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

    public function setup()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<config>
    <modules>
        <Mage_Test>
            <version>1.6.0.0.23</version>
            <active>true</active>
            <depends/>
        </Mage_Test>
    </modules>
</config>
XML;
        $dirs = Mage::getObjectManager()->create(
            'Mage_Core_Model_Dir', array('baseDir' => array(__DIR__ . '/_files'),
                                         'dirs'    => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'),)
        );
        $fileReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules_File', array('dirs' => $dirs)
        );
        $loader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Loader_Modules', array('dirs' => $dirs, 'fileReader' => $fileReader)
        );
        $config = Mage::getObjectManager()->create('Mage_Core_Model_Config_Base', array('sourceData' => $xml));
        $loader->load($config);
        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Mage_Core_Model_Config_Modules_Reader', array('fileReader' => $fileReader, 'modulesConfig' => $config)
        );


        $dsConfigReader = Mage::getObjectManager()->
            create('Mage_Core_Model_Dataservice_Config_Reader',
                array('moduleReader' => $moduleReader,'dir' => $dirs));

        $this->_config = new Mage_Core_Model_Dataservice_Config($dsConfigReader);
    }

    public function testGetClassByAlias()
    {
        $classInfo = $this->_config->getClassByAlias('alias');
        $this->assertEquals('some_class_name', $classInfo['class']);
        $this->assertEquals('some_method_name', $classInfo['retrieveMethod']);
        $this->assertEquals(array('some_arg_name' => 'some_arg_value'), $classInfo['methodArguments']);
    }
}