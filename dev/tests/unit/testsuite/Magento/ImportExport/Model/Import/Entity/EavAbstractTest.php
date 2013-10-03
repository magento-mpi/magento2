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
 * Test class for \Magento\ImportExport\Model\Import\Entity\AbstractEav
 */
namespace Magento\ImportExport\Model\Import\Entity;

class EavAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Entity type id
     */
    const ENTITY_TYPE_ID   = 1;

    /**
     * Abstract import entity eav model
     *
     * @var \Magento\ImportExport\Model\Import\Entity\AbstractEav
     */
    protected $_model;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreDataMock;

    /**
     * @var \Magento\Core\Helper\String|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreStringMock;

    /**
     * @var \Magento\ImportExport\Model\ImportFactory
     */
    protected $_importFactory;

    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_resource;

    /**
     * @var \Magento\ImportExport\Model\Resource\Helper
     */
    protected $_resourceHelper;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\ImportExport\Model\Export\Factory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    protected function setUp()
    {
        $this->_coreDataMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->_coreStringMock = $this->getMock('Magento\Core\Helper\String', array('__construct'), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);

        $this->_importFactory = $this->getMock('Magento\ImportExport\Model\ImportFactory', array(), array(), '', false);
        $this->_resource = $this->getMock('Magento\Core\Model\Resource', array(), array(), '', false);
        $this->_resourceHelper = $this->getMock(
            'Magento\ImportExport\Model\Resource\Helper', array(), array(), '', false
        );
        $this->_app = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento\ImportExport\Model\Export\Factory', array(), array(), '', false);
        $this->_eavConfig = $this->getMock(
            'Magento\Eav\Model\Config', array(), array(), '', false
        );

        $this->_model = $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\Entity\AbstractEav',
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
        $localeMock = $this->getMock('Magento\Core\Model\Locale', array(), array(), '', false);
        $data = array(
            'data_source_model'            => 'not_used',
            'connection'                   => 'not_used',
            'json_helper'                  => 'not_used',
            'string_helper'                => new \Magento\Core\Helper\String(
                $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false), $localeMock
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
     * @covers \Magento\ImportExport\Model\Import\Entity\AbstractEav::getEntityTypeId
     */
    public function testGetEntityTypeId()
    {
        $this->assertEquals(self::ENTITY_TYPE_ID, $this->_model->getEntityTypeId());
    }
}
