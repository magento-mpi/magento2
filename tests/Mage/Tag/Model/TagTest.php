<?php
class Mage_Tag_Model_TagTest extends PHPUnit_Framework_TestCase
{
    protected $_model;

    protected function setUp()
    {
        Mage::app();
        $this->_model = Mage::getModel('tag/tag');
    }

    public function testIsRatioSet()
    {
        $this->assertTrue($this->_model->getRatio());
    }
}
