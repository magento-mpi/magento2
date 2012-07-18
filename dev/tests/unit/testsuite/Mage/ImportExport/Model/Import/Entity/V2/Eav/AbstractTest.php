<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Entity type id
     */
    const ENTITY_TYPE_ID   = 1;
    /**#@-*/

    /**
     * Abstract import entity eav model
     *
     * @var Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract',
            array($this->_getModelDependencies())
        );
    }

    public function tearDown()
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
        $translator = $this->getMock('stdClass', array('__'));
        $translator->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $data = array(
            'data_source_model'            => 'not_used',
            'connection'                   => 'not_used',
            'translator'                   => $translator,
            'json_helper'                  => 'not_used',
            'string_helper'                => new Mage_Core_Helper_String(),
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
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract::getEntityTypeId
     */
    public function testGetEntityTypeId()
    {
        $this->assertEquals(self::ENTITY_TYPE_ID, $this->_model->getEntityTypeId());
    }

    /**
     * @todo implement in the scope of https://wiki.magento.com/display/MAGE2/Technical+Debt+%28Team-Donetsk-B%29
     *
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract::getAttributeOptions
     */
    public function testGetAttributeOptions()
    {
        $this->markTestIncomplete('Technical debt - not implemented');
    }
}
