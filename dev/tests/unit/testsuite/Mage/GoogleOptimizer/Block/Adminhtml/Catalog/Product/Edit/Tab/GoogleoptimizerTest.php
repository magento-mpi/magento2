<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_GoogleoptimizerTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    public function setUp()
    {
        $this->_blockMock = $this->getMock(
            'Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_Googleoptimizer',
            array('__'), array(), '', false);
    }

    public function testGetTabLabel()
    {
        $this->_blockMock->expects($this->once())->method('__')->with('Product View Optimization')
            ->will($this->returnValue('Product View Optimization Translated'));

        $this->assertEquals('Product View Optimization Translated', $this->_blockMock->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->_blockMock->expects($this->once())->method('__')->with('Product View Optimization')
            ->will($this->returnValue('Product View Optimization Translated'));

        $this->assertEquals('Product View Optimization Translated', $this->_blockMock->getTabTitle());
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->_blockMock->isHidden());
    }
}
class Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_GoogleoptimizerMock
{

}
