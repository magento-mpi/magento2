<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Product_Limitation
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
        $this->_resource = $this->getMock('Mage_Catalog_Model_Resource_Product', array(), array(), '', false);
        $this->_config = $this->getMock('Saas_Limitation_Model_Limitation_Config', array(), array(), '', false);
        $this->_model = new Saas_Limitation_Model_Catalog_Product_Limitation($this->_config, $this->_resource);
    }

    public function testGetThreshold()
    {
        $this->_config->expects($this->once())
            ->method('getThreshold')
            ->with('catalog_product')
            ->will($this->returnValue(5))
        ;
        $this->assertEquals(5, $this->_model->getThreshold());
    }

    public function testGetTotalCount()
    {
        $this->_resource->expects($this->any())->method('countAll')->will($this->returnValue(81));
        $this->assertEquals(81, $this->_model->getTotalCount());
    }
}
