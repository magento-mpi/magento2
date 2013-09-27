<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_ResourcesConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Config_Reader
     */
    protected $_model;

    protected function setUp()
    {
        // List of all resources.xml
        $xmlFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles(
            '{*/resources.xml,resources.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        $fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
        $localConfigMock = $this->getMock('Magento_Core_Model_Config_Local', array(), array(), '', false);
        $localConfigMock->expects($this->any())->method('getConfiguration')->will($this->returnValue(array()));
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento_Core_Model_Resource_Config_Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
            'localConfig' => $localConfigMock,
        ));
    }

    public function testResourcesXmlFiles()
    {
        $this->_model->read('global');
    }
}
