<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Change_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Collection model for testing
     *
     * @var Mage_DesignEditor_Model_Change_Collection
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = new Mage_DesignEditor_Model_Change_Collection;
    }

    /**
     * @covers Mage_DesignEditor_Model_Change_Collection::getItemClass
     */
    public function testGetItemClass()
    {
        $this->assertEquals('Mage_DesignEditor_Model_ChangeAbstract', $this->_model->getItemClass());
    }

    /**
     * Test toArray method
     *
     * @covers Mage_DesignEditor_Model_Change_Collection::toArray
     */
    public function testToArray()
    {
        $this->assertInternalType('array', $this->_model->toArray());
    }
}
