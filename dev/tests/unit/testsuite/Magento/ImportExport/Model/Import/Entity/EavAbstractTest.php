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
 * Test class for Magento_ImportExport_Model_Import_Entity_EavAbstract
 */
class Magento_ImportExport_Model_Import_Entity_EavAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Entity type id
     */
    const ENTITY_TYPE_ID   = 1;

    /**
     * Abstract import entity eav model
     *
     * @var Magento_ImportExport_Model_Import_Entity_EavAbstract
     */
    protected $_model;

    /**
     * @var Magento_Core_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreDataMock;

    /**
     * @var Magento_Core_Helper_String|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreStringMock;

    /**
     * @var Magento_ImportExport_Model_ImportFactory
     */
    protected $_importFactory;

    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_resource;

    /**
     * @var Magento_ImportExport_Model_Resource_Helper
     */
    protected $_resourceHelper;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @var Magento_ImportExport_Model_Export_Factory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    protected function setUp()
    {
        $this->_coreDataMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $this->_coreStringMock = $this->getMock('Magento_Core_Helper_String', array('__construct'), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);

        $this->_importFactory = $this->getMock('Magento_ImportExport_Model_ImportFactory', array(), array(), '', false);
        $this->_resource = $this->getMock('Magento_Core_Model_Resource', array(), array(), '', false);
        $this->_resourceHelper = $this->getMock(
            'Magento_ImportExport_Model_Resource_Helper', array(), array(), '', false
        );
        $this->_app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento_ImportExport_Model_Export_Factory', array(), array(), '', false);
        $this->_eavConfig = $this->getMock(
            'Magento_Eav_Model_Config', array(), array(), '', false
        );

        $this->_model = $this->getMockForAbstractClass('Magento_ImportExport_Model_Import_Entity_EavAbstract',
            array(
                $this->_coreDataMock,
                $this->_coreStringMock,
                $coreStoreConfig,
                $this->_importFactory,
                $this->_resourceHelper,
                $this->_resource,
                $this->_app,
                $this->_collectionFactory,
                $this->_eavConfig,
                $this->_getModelDependencies()
            )
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $localeMock = $this->getMock('Magento_Core_Model_Locale', array(), array(), '', false);
        $data = array(
            'data_source_model'            => 'not_used',
            'connection'                   => 'not_used',
            'json_helper'                  => 'not_used',
            'string_helper'                => new Magento_Core_Helper_String(
                $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false), $localeMock
            ),
            'page_size'                    => 1,
            'max_data_size'                => 1,
            'bunch_size'                   => 1,
            'collection_by_pages_iterator' => 'not_used',
            'website_manager'              => 'not_used',
            'store_manager'                => 'not_used',
            'attribute_collection'         => 'not_used',
            'entity_type_id'               => self::ENTITY_TYPE_ID,
        );

        return $data;
    }

    /**
     * Test entity type id getter
     *
     * @covers Magento_ImportExport_Model_Import_Entity_EavAbstract::getEntityTypeId
     */
    public function testGetEntityTypeId()
    {
        $this->assertEquals(self::ENTITY_TYPE_ID, $this->_model->getEntityTypeId());
    }
}
