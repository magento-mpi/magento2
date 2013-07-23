<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Resource_Template_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_PrintedTemplate_Model_Resource_Template_Collection
     */
    protected $_collection;

    /**
     * @var Saas_PrintedTemplate_Model_Template
     */
    protected $_model;

    protected function setUp()
    {
        return; // MAGETWO-7075
        $this->_collection = Mage::getResourceModel('Saas_PrintedTemplate_Model_Resource_Template_Collection');
        $this->_model = Mage::getModel('Saas_PrintedTemplate_Model_Template');
        $this->_model->setData(array('name' => 'Test invoice','entity_type' => 'invoice'))->save();
    }

    protected function tearDown()
    {
        return; // MAGETWO-7075
        Mage::getConfig()->setCurrentAreaCode(Mage::helper('Mage_Backend_Helper_Data')->getAreaCode());
        $this->_model->delete();
        $this->_collection = null;
    }

    /**
     * Test for Saas_PrintedTemplate_Model_Resource_Template_Collection::toOptionArray
     */
    public function testToOptionArray()
    {
        $this->assertNotEmpty($this->_collection->toOptionArray());

        foreach ($this->_collection->toOptionArray() as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertEquals('Test invoice', $item['label']);
        }
    }
}
