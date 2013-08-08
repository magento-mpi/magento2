<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Group_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Store_Group_Limitation
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_config;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    protected function setUp()
    {
        $this->_resource = $this->getMock('Magento_Core_Model_Resource_Store_Group', array(), array(), '', false);
        $this->_config = $this->getMock('Saas_Limitation_Model_Limitation_Config', array(), array(), '', false);
        $this->_model = new Saas_Limitation_Model_Store_Group_Limitation($this->_config, $this->_resource);
    }

    public function testGetThreshold()
    {
        $this->_config->expects($this->once())
            ->method('getThreshold')
            ->with('store_group')
            ->will($this->returnValue(5))
        ;
        $this->assertEquals(5, $this->_model->getThreshold());
    }

    public function testGetTotalCount()
    {
        $this->_resource->expects($this->once())->method('countAll')->will($this->returnValue(81));
        $this->assertEquals(81, $this->_model->getTotalCount());
    }
}
