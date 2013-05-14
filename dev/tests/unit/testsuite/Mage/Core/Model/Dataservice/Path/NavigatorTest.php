<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Path_Navigator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Path_NavigatorTest extends PHPUnit_Framework_TestCase
{
    const SOMETHING_MORE_INTERESTING_THAN_NULL = 'Something more interesting than null.';

    /** @var Mage_Core_Model_Dataservice_Path_Navigator */
    protected $_navigator;

    /** @var PHPUnit_Framework_MockObject_MockObject  Mage_Core_Model_Dataservice_Path_Node */
    private $_rootNode;

    public function setup()
    {
        $this->_navigator = new Mage_Core_Model_Dataservice_Path_Navigator();
    }

    public function testSearch()
    {
        $this->_rootNode = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Node')
            ->disableOriginalConstructor()->getMock();
        $branch = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Node')
            ->disableOriginalConstructor()->getMock();
        $leaf = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Node')
            ->disableOriginalConstructor()->getMock();
        $this->_rootNode->expects($this->any())
            ->method('getChild')
            ->with('branch')
            ->will($this->returnValue($branch));
        $branch->expects($this->any())
            ->method('getChild')
            ->with('leaf')
            ->will($this->returnValue($leaf));

        $nodeFound = $this->_navigator->search($this->_rootNode, explode('.', 'branch.leaf'));

        $this->assertEquals($leaf, $nodeFound);
    }
}