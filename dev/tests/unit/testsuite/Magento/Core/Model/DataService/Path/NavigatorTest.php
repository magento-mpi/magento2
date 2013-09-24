<?php
/**
 * \Magento\Core\Model\DataService\Path\Navigator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Path;

class NavigatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject  \Magento\Core\Model\DataService\Path\NodeInterface */
    private $_rootNode;

    /**
     * @var \Magento\Core\Model\DataService\Path\Navigator
     */
    private $_navigator;

    protected function setUp()
    {
        $this->_navigator = new \Magento\Core\Model\DataService\Path\Navigator();
    }

    public function testSearch()
    {
        $this->_rootNode = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_rootNode->expects($this->any())
            ->method('getChildNode')
            ->with('branch')
            ->will($this->returnValue($branch));
        $branch->expects($this->any())
            ->method('getChildNode')
            ->with('leaf')
            ->will($this->returnValue($leaf));

        $nodeFound = $this->_navigator->search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }

    public function testSearchOfArray()
    {
        $this->_rootNode = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = array();
        $leaf = 'a leaf node can be anything';
        $branch['leaf'] = $leaf;
        $this->_rootNode->expects($this->any())
            ->method('getChildNode')
            ->with('branch')
            ->will($this->returnValue($branch));

        $nodeFound = $this->_navigator->search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }

    public function testSearchOfEmptyArray()
    {
        $this->_rootNode = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = array();
        $this->_rootNode->expects($this->any())
            ->method('getChildNode')
            ->with('branch')
            ->will($this->returnValue($branch));

        $nodeFound = $this->_navigator->search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals(null, $nodeFound);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage invalid.leaf
     */
    public function testSearchWithInvalidPath()
    {
        $this->_rootNode = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Magento\Core\Model\DataService\Path\NodeInterface')
            ->disableOriginalConstructor()->getMock();

        $nodeFound = $this->_navigator->search($this->_rootNode, explode('.', 'invalid.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }
}
