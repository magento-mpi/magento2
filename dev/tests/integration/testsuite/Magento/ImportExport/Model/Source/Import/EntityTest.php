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
 * Test class for entity source model \Magento\ImportExport\Model\Source\Import\Entity
 */
class Magento_ImportExport_Model_Source_Import_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var \Magento\ImportExport\Model\Source\Import\Entity
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
     */
    public function setUp()
    {
        /** @var Magento_TestFramework_ObjectManager $objectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var \Magento\Core\Model\Config $coreConfig */
        $coreConfig = $objectManager->create('Magento\Core\Model\Config', array('storage' => $this->_mockConfig()));
        $coreConfig->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/model_token',
            'Some_Class'
        );
        $coreConfig->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/label',
            $this->_testEntity['label']
        );

        /** @var $config \Magento\ImportExport\Model\Config */
        $config = $objectManager->create('Magento\ImportExport\Model\Config', array('coreConfig' => $coreConfig));
        $this->_sourceModel = $objectManager->create(
            'Magento\ImportExport\Model\Source\Import\Entity',
            array('config' => $config)
        );
    }

    /**
     * Mock config
     */
    protected function _mockConfig()
    {
        $storage = $this->getMock('Magento\Core\Model\Config\Storage', array('getConfiguration'), array(), '', false);
        $configObject = new \Magento\Core\Model\Config\Base(new \Magento\Simplexml\Element('<config></config>'));
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/model_token',
            'Some_Class'
        );
        $configObject->setNode(
            'global/importexport/import_entities/' . $this->_testEntity['node'] . '/label',
            $this->_testEntity['label']
        );
        $storage->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->will($this->returnValue($configObject));
        return $storage;
    }

    /**
     * Unregister source model and helper
     */
    public function tearDown()
    {
        $this->_sourceModel = null;
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
