<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item
     */
    protected  $_model;

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Menu_Item('<item/>');
    }

    public function testAddData()
    {
        $this->assertEmpty($this->_model->getAttribute('id'));
        $this->assertEmpty($this->_model->getAttribute('parent'));
        $this->_model->addData(array(
            'id' => 3,
            'parent' => 2
        ));
        $this->assertEquals(3, $this->_model->getAttribute('id'));
        $this->assertEquals(2, $this->_model->getAttribute('parent'));

    }

    public function testUpdateData()
    {
        $this->assertNull($this->_model->getAttribute('id'));
        $this->assertNull($this->_model->getAttribute('parent'));
        $this->_model->updateData(array(
            'id' => 3,
            'parent' => 2
        ));
        $this->assertEquals(3, $this->_model->getAttribute('id'));
        $this->assertEquals(2, $this->_model->getAttribute('parent'));
    }

    public function testAddDataAfterUpdateDoesntChangeExistingData()
    {
        $this->assertNull($this->_model->getAttribute('id'));
        $this->assertNull($this->_model->getAttribute('parent'));
        $this->_model->updateData(array(
            'id' => 3,
            'parent' => 2
        ));
        $this->assertEquals(3, $this->_model->getAttribute('id'));
        $this->assertEquals(2, $this->_model->getAttribute('parent'));
        $this->_model->addData(array(
            'id' => 3,
            'parent' => 5
        ));
        $this->assertEquals(3, $this->_model->getAttribute('id'));
        $this->assertEquals(2, $this->_model->getAttribute('parent'));
    }


}
