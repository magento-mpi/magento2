<?php
/**
 * Test class for Mage_Core_Model_DataService_Path_Navigator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Path_NavigatorTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject  Mage_Core_Model_DataService_Path_NodeInterface */
    private $_rootNode;

    /**
     * test static search method
     * @return null
     */
    public function testSearch()
    {
        $this->_rootNode = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $this->_rootNode->expects($this->any())
            ->method('getChildNode')
            ->with('branch')
            ->will($this->returnValue($branch));
        $branch->expects($this->any())
            ->method('getChildNode')
            ->with('leaf')
            ->will($this->returnValue($leaf));

        $nodeFound = Mage_Core_Model_DataService_Path_Navigator::search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }

    /**
     * Test searching of path
     */
    public function testSearchOfArray()
    {
        $this->_rootNode = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = array();
        $leaf = 'a leaf node can be anything';
        $branch['leaf'] = $leaf;
        $this->_rootNode->expects($this->any())
            ->method('getChildNode')
            ->with('branch')
            ->will($this->returnValue($branch));

        $nodeFound = Mage_Core_Model_DataService_Path_Navigator::search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }

    /**
     * Try to find a node in an empty array
     */
    public function testSearchOfEmptyArray()
    {
        $this->_rootNode = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $branch = array();
        $this->_rootNode->expects($this->any())
            ->method('getChildNode')
            ->with('branch')
            ->will($this->returnValue($branch));

        $nodeFound = Mage_Core_Model_DataService_Path_Navigator::search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals(null, $nodeFound);
    }

    /**
     * Verify we get a proper exception when a node in the path isn't found
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid.leaf
     */
    public function testSearchWithInvalidPath()
    {
        $this->_rootNode = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Mage_Core_Model_DataService_Path_NodeInterface')
            ->disableOriginalConstructor()->getMock();

        $nodeFound = Mage_Core_Model_DataService_Path_Navigator::search($this->_rootNode, explode('.', 'invalid.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }
}