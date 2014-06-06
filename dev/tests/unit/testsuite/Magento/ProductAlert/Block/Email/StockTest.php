<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Block\Email;

/**
 * Test class for \Magento\ProductAlert\Block\Product\View\Stock
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\ProductAlert\Block\Product\View\Stock
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filter\Input\MaliciousCode
     */
    protected $_filter;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_filter = $this->getMock(
            '\Magento\Framework\Filter\Input\MaliciousCode',
            array('filter'),
            array(),
            '',
            false
        );
        $this->_block = $objectManager->getObject(
            'Magento\ProductAlert\Block\Email\Stock',
            array('maliciousCode' => $this->_filter)
        );
    }

    /**
     * @dataProvider testGetFilteredContentDataProvider
     * @param $contentToFilter
     * @param $contentFiltered
     */
    public function testGetFilteredContent($contentToFilter, $contentFiltered)
    {
        $this->_filter->expects($this->once())->method('filter')->with($contentToFilter)
            ->will($this->returnValue($contentFiltered));
        $this->assertEquals($contentFiltered, $this->_block->getFilteredContent($contentToFilter));
    }

    public function testGetFilteredContentDataProvider()
    {
        return array(
            'normal desc' => array('<b>Howdy!</b>', '<b>Howdy!</b>'),
            'malicious desc 1' => array('<javascript>Howdy!</javascript>', 'Howdy!'),
        );
    }
}
