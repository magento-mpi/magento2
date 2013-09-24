<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_ImportConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Import_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        // List of all available import.xml
        $xmlFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles(
            '{*/import.xml,import.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );

        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')
            ->will($this->returnValue(true));
        $fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $this->_model = $objectManager->create('Magento_ImportExport_Model_Import_Config_Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testImportXmlFiles()
    {
        $this->_model->read('global');
    }
}
