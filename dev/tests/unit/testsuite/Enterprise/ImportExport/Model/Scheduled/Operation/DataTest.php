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
            'import_model'         => new Mage_ImportExport_Model_Import(),
        ));
    }

    public function tearDown()
    {
        unset($this->_dataModel);
    }

    /**
     * Test for getEntitySubtypesOptionArray
     *
     * @covers Enterprise_ImportExport_Model_Scheduled_Operation_Data::getEntitySubtypesOptionArray
     */
    public function testGetEntitySubtypesOptionArray()
    {
        $testArray = $this->_dataModel->getEntitySubtypesOptionArray();
        $this->assertEquals($this->_importEntities, $testArray, 'Incorrect entity subtypes array.');
    }

    /**
     * Callback to imitate Mage_ImportExport_Model_Config::getModelsArrayOptions
     *
     * @param string $configKey
     * @return array|null
     */
    public function getModelsArrayOptionsCallback($configKey)
    {
        $this->assertEquals(Mage_ImportExport_Model_Import::CONFIG_KEY_CUSTOMER_ENTITIES, $configKey);
        return $this->_importEntities;
    }
}
