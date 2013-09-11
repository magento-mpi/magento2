<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for entity source model Magento_ImportExport_Model_Source_Import_Entity
 */
class Magento_ImportExport_Model_Source_Import_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var Magento_ImportExport_Model_Source_Import_Entity
     */
    protected $_sourceModel;

    /**
     * Test entity
     *
     * @var array
     */
    protected $_testEntity = array(
        'label' => 'test_label',
        'node'  => 'test_node'
    );

    /**
     * Init source model
     *
     * @static
     */
    public function setUp()
    {
        /** @var Magento_Test_ObjectManager $objectMaganger */
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();

        /** @var $config Magento_ImportExport_Model_Config */
        $config = $objectManager->create(
            'Magento_ImportExport_Model_Config',
            array('coreConfig' => $this->_mockConfig())
        );
        $this->_sourceModel = $objectManager->create(
            'Magento_ImportExport_Model_Source_Import_Entity',
            array('config' => $config)
        );
    }

    /**
     * Unregister source model and helper
     */
    public function tearDown()
    {
        $this->_sourceModel = null;
    }

    /**
     * Mock config
     */
    protected function _mockConfig()
    {
        $configObject = new Magento_Core_Model_Config_Base(new Magento_Simplexml_Element('<config></config>'));
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/model_token',
            'Some_Class'
        );
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/label',
            $this->_testEntity['label']
        );
        return $configObject;
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $optionalArray = $this->_sourceModel->toOptionArray();

        $this->assertInternalType('array', $optionalArray, 'Result variable must be an array.');
        $this->assertCount(2, $optionalArray);

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }

        $headerElement = $optionalArray[0];
        $dataElement = $optionalArray[1];

        $this->assertEmpty($headerElement['value'], 'Value must be empty.');
        $this->assertEquals($this->_testEntity['node'], $dataElement['value'], 'Incorrect element value.');
        $this->assertEquals($this->_testEntity['label'], $dataElement['label'], 'Incorrect element label.');
    }
}
