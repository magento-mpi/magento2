<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_MenuHierarchyConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Config_Reader
     */
    protected $_model;

    protected function setUp()
    {
        // List of all available menu_hierarchy.xml
        $xmlFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles(
            '{*/menu_hierarchy.xml,menu_hierarchy.xml}',
            array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
            false
        );
        $fileResolverMock = $this->getMock('Magento_Config_FileResolverInterface');
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($xmlFiles));

        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $validationStateMock->expects($this->any())->method('isValidated')->will($this->returnValue(true));
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento_VersionsCms_Model_Hierarchy_Config_Reader', array(
            'fileResolver' => $fileResolverMock,
            'validationState' => $validationStateMock,
        ));
    }

    public function testMenuHierarchyConfigFiles()
    {
        $this->_model->read('global');
    }
}
