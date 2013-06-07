<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_TaskTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $helper->getObject('Enterprise_Queue_Model_Task');
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testIsEnqueued()
    {
        $this->assertFalse($this->_model->isEnqueued());
        $this->_model->setStatus(array('isEnqueued' => true, 'percentage' => 10));
        $this->assertTrue($this->_model->isEnqueued());
    }

    public function testGetPercentage()
    {
        $this->assertEquals(0, $this->_model->getPercentage());
        $this->_model->setStatus(array('isEnqueued' => true, 'percentage' => 10));
        $this->assertEquals(10, $this->_model->getPercentage());
    }
}
