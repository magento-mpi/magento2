<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Class for \Magento\Search\Model\Factory\Factory
 */
namespace Magento\Search\Model\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Search\Model\Solr\State |\PHPUnit_Framework_MockObject_MockObject */
    protected $_solrStateMock;

    /** @var \Magento\Search\Model\Factory\Factory|\PHPUnit_Framework_MockObject_MockObject*/
    protected $_factoryObject;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var \Magento\Search\Model\SolrFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_solrFactoryMock;

    /** @var \Magento\Search\Model\RegularFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_regularFactoryMock;

    /**
     * Set adapter mocks
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $this->_solrStateMock = $this->getMock('Magento\Search\Model\Solr\State',
            array(), array(), '', false, false);
        $this->_solrFactoryMock = $this->getMock('Magento\Search\Model\SolrFactory',
            array(), array(), '', false, false);
        $this->_regularFactoryMock = $this->getMock('Magento\Search\Model\RegularFactory',
            array(), array(), '', false, false);

        $this->_factoryObject = new \Magento\Search\Model\Factory\Factory($this->_objectManager, $this->_solrStateMock);
    }

    /**
     * Test if we get Solr factory on solr ext loaded
     */
    public function testGetFactorySolr()
    {
        $this->_solrStateMock->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(true));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Search\Model\SolrFactory')
            ->will($this->returnValue($this->_solrFactoryMock));
        $this->_factoryObject->getFactory();
    }

    /**
     * Test if we get regular factory on solr ext not loaded
     */
    public function testGetFactoryRegular()
    {
        $this->_solrStateMock->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(false));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Search\Model\RegularFactory')
            ->will($this->returnValue($this->_regularFactoryMock));
        $this->_factoryObject->getFactory();
    }
}

