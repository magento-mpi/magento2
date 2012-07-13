<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_ImportExport_Model_Scheduled_Operation_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Data model for scheduled operation
     *
     * @var Enterprise_ImportExport_Model_Scheduled_Operation_Data
     */
    protected $_dataModel;

    /**
     * Test export entities
     *
     * @var array
     */
    protected $_exportEntities = array(
        'export_key_1' => 'export_value_1',
        'export_key_2' => 'export_value_2',
    );

    /**
     * Test import entities
     *
     * @var array
     */
    protected $_importEntities = array(
        'import_key_1' => 'import_value_1',
        'import_key_2' => 'import_value_2',
    );

    public function setUp()
    {
        parent::setUp();

        // import/export config mock
        $configMock = $this->getMock(
            'Mage_ImportExport_Model_Config',
            array('getModelsArrayOptions')
        );
        $configMock->staticExpects($this->any())
            ->method('getModelsArrayOptions')
            ->will($this->returnCallback(array($this, 'getModelsArrayOptionsCallback')));

        // data model test object
        $this->_dataModel = new Enterprise_ImportExport_Model_Scheduled_Operation_Data(array(
                'import_export_config' => $configMock,
                'export_model'         => new Mage_ImportExport_Model_Export(),
                'import_model'         => new Mage_ImportExport_Model_Import(),
        ));
    }

    public function tearDown()
    {
        unset($this->_dataModel);
        parent::tearDown();
    }

    /**
     * Test for getEntitySubtypesOptionArray
     *
     * @covers Enterprise_ImportExport_Model_Scheduled_Operation_Data::getEntitySubtypesOptionArray
     */
    public function testGetEntitySubtypesOptionArray()
    {
        $testArray = $this->_dataModel->getEntitySubtypesOptionArray();
        $correctArray = array_merge($this->_exportEntities, $this->_importEntities);
        $this->assertEquals($correctArray, $testArray, 'Incorrect entity subtypes array.');
    }

    /**
     * Callback to imitate Mage_ImportExport_Model_Config::getModelsArrayOptions
     *
     * @param $configKey
     * @return array|null
     */
    public function getModelsArrayOptionsCallback($configKey)
    {
        switch ($configKey) {
            case Mage_ImportExport_Model_Export::CONFIG_KEY_CUSTOMER_ENTITIES:
                return $this->_exportEntities;
            case Mage_ImportExport_Model_Import::CONFIG_KEY_CUSTOMER_ENTITIES:
                return $this->_importEntities;
            default:
                return null;
        }
    }
}
