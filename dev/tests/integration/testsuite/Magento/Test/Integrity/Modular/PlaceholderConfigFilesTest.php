<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_PlaceholderConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Placeholder_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        // List of all available placeholders.xml
        $xmlFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles(
            '{*/placeholders.xml,placeholders.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        $fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($xmlFiles));
        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento_FullPageCache_Model_Placeholder_Config_Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testPlaceholderXmlFiles()
    {
        $this->_model->read('global');
    }
}
