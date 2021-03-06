<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test Class for \Magento\Solr\Model\Factory\Factory
 */
namespace Magento\Solr\Model\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Solr\Model\Solr\State |\PHPUnit_Framework_MockObject_MockObject */
    protected $_solrStateMock;

    /** @var \Magento\Solr\Model\Factory\Factory|\PHPUnit_Framework_MockObject_MockObject*/
    protected $_factoryObject;

    /** @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var \Magento\Solr\Model\SolrFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_solrFactoryMock;

    /** @var \Magento\Solr\Model\RegularFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_regularFactoryMock;

    /**
     * Set adapter mocks
     */
    public function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_solrStateMock = $this->getMock('Magento\Solr\Model\Solr\State', [], [], '', false, false);
        $this->_solrFactoryMock = $this->getMock(
            'Magento\Solr\Model\SolrFactory',
            [],
            [],
            '',
            false,
            false
        );
        $this->_regularFactoryMock = $this->getMock(
            'Magento\Solr\Model\RegularFactory',
            [],
            [],
            '',
            false,
            false
        );

        $this->_factoryObject = new \Magento\Solr\Model\Factory\Factory(
            $this->_objectManager,
            $this->_solrStateMock
        );
    }

    /**
     * Test if we get Solr factory on solr ext loaded
     */
    public function testGetFactorySolr()
    {
        $this->_solrStateMock->expects($this->once())->method('isActive')->will($this->returnValue(true));
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'Magento\Solr\Model\SolrFactory'
        )->will(
            $this->returnValue($this->_solrFactoryMock)
        );
        $this->_factoryObject->getFactory();
    }

    /**
     * Test if we get regular factory on solr ext not loaded
     */
    public function testGetFactoryRegular()
    {
        $this->_solrStateMock->expects($this->once())->method('isActive')->will($this->returnValue(false));

        $this->_objectManager->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'Magento\Solr\Model\RegularFactory'
        )->will(
            $this->returnValue($this->_regularFactoryMock)
        );
        $this->_factoryObject->getFactory();
    }
}
