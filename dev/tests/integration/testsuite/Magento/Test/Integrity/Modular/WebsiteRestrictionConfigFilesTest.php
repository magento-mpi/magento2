<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_WebsiteRestrictionConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_WebsiteRestriction_Model_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        // List of all available webrestrictions.xml
        $xmlFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles(
            '{*/webrestrictions.xml,webrestrictions.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        $fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($xmlFiles));
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
        $this->_model = $objectManager->create('Magento_WebsiteRestriction_Model_Config_Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testWebsiteRestrictionXmlFiles()
    {
        $this->_model->read('global');
    }
}
