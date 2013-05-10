<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Path_Visitor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Path_VisitorTest extends PHPUnit_Framework_TestCase
{
    const SOMETHING_MORE_INTERESTING_THAN_NULL = 'Something more interesting than null.';

    /** @var Mage_Core_Model_Dataservice_Path_Visitor */
    protected $_visitor;

    public function setup()
    {
        $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')->disableOriginalConstructor()->getMock();
        $objectManagerMock->expects($this->once())->method('create')->with(
            $this->equalTo('Mage_Core_Model_Dataservice_Path_Visitor'),
            $this->equalTo(array('path' => '{root/branch/leaf}', 'separator' => '.'))
        )->will($this->returnValue(new Mage_Core_Model_Dataservice_Path_Visitor('{root.branch.leaf}', '.')));
        $factory = new Mage_Core_Model_Dataservice_Path_Visitor_Factory($objectManagerMock);
        $this->_visitor = $factory->get('{root/branch/leaf}');
    }

    public function testPathElement()
    {
        $this->assertNull($this->_visitor->getCurrentPathElement());
        $this->assertEquals('root', $this->_visitor->chopCurrentPathElement());
        $this->assertEquals('root', $this->_visitor->getCurrentPathElement());
        $this->assertEquals('branch', $this->_visitor->chopCurrentPathElement());
        $this->assertEquals('branch', $this->_visitor->getCurrentPathElement());
        $this->assertEquals('leaf', $this->_visitor->chopCurrentPathElement());
        $this->assertEquals('leaf', $this->_visitor->getCurrentPathElement());
        $this->assertEquals('', $this->_visitor->chopCurrentPathElement());
        $this->assertEquals('', $this->_visitor->getCurrentPathElement());
        $this->assertEquals('', $this->_visitor->chopCurrentPathElement());
        $this->assertEquals('', $this->_visitor->getCurrentPathElement());
    }

    public function testVisit()
    {
        // leaf
        $visitableMockLeaf = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Visitable')
            ->disableOriginalConstructor()->getMock();
        $visitableMockLeaf->expects($this->once())->method('visit')->with($this->_visitor)->will(
            $this->returnValue(self::SOMETHING_MORE_INTERESTING_THAN_NULL)
        );
        // branch
        $visitableMockBranch = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Visitable')
            ->disableOriginalConstructor()->getMock();
        $visitableMockBranch->expects($this->once())->method('visit')->with($this->_visitor)->will(
            $this->returnValue($visitableMockLeaf)
        );
        // root
        $target = array('root' => $visitableMockBranch);
        $this->assertEquals(self::SOMETHING_MORE_INTERESTING_THAN_NULL, $this->_visitor->visit($target));
    }
}