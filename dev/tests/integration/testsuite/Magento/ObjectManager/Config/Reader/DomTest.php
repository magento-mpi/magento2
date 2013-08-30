<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ObjectManager_Config_Reader_DomTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager_Config_Reader_Dom
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_fileList;

    /**
     * @var Magento_Core_Model_Config_FileResolver_Primary
     */
    protected $_fileResolverMock;

    /**
     * @var DOMDocument
     */
    protected $_mergedConfig;

    /**
     * @var Magento_Core_Model_Config_ValidationState
     */
    protected $_validationState;

    /**
     * @var Magento_ObjectManager_Config_SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var Magento_ObjectManager_Config_Mapper_Dom
     */
    protected $_mapper;

    protected function setUp()
    {
        $fixturePath = realpath(__DIR__ . '/../../_files') . DIRECTORY_SEPARATOR;
        $this->_fileList = array(
            $fixturePath . 'config_one.xml',
            $fixturePath . 'config_two.xml',
        );

        $this->_fileResolverMock = $this->getMock(
            'Magento_Core_Model_Config_FileResolver_Primary', array(), array(), '', false
        );
        $this->_fileResolverMock->expects($this->once())->method('get')->will($this->returnValue($this->_fileList));
        $this->_mapper = new Magento_ObjectManager_Config_Mapper_Dom();
        $this->_validationState = new Mage_Core_Model_Config_ValidationState(new Mage_Core_Model_App_State());
        $this->_schemaLocator = new Magento_ObjectManager_Config_SchemaLocator();

        $this->_mergedConfig = new DOMDocument();
        $this->_mergedConfig->load($fixturePath . 'config_merged.xml');
    }

    public function testRead()
    {
        $model = new Magento_ObjectManager_Config_Reader_Dom(
            $this->_fileResolverMock,
            $this->_mapper,
            $this->_schemaLocator,
            $this->_validationState
        );
        $this->assertEquals($this->_mapper->convert($this->_mergedConfig), $model->read('scope'));
    }

}
