<?php
/**
 * Magento_Core_Model_DataService_Path_Navigator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Path_NavigatorTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject  Magento_Core_Model_DataService_Path_NodeInterface */
    private $_rootNode;

    /**
     * @var Magento_Core_Model_DataService_Path_Navigator
     */
    private $_navigator;

    public function setUp()
    {
        $this->_navigator = new Magento_Core_Model_DataService_Path_Navigator();
    }

    public function testSearch()
    {
        $this->_rootNode = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
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
        $this->_rootNode = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
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
        $this->_rootNode = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid.leaf
     */
    public function testSearchWithInvalidPath()
    {
        $this->_rootNode = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Magento_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();

        $nodeFound = $this->_navigator->search($this->_rootNode, explode('.', 'invalid.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }
}