<?php
class Mage_Tag_Model_TagTest extends Mage_ModelCase
{
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('tag/tag');
    }

    public function testIsRatioSet()
    {
        $this->assertTrue($this->_model->getRatio());
    }
}
