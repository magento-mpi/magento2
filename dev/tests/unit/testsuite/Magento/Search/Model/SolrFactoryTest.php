<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Class for \Magento\Solr\Model\Factory\Factory
 */
namespace Magento\Solr\Model;

class SolrFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Solr\Model\SolrFactory|\PHPUnit_Framework_MockObject_MockObject*/
    protected $_factoryObject;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var \Magento\Solr\Model\SolrFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_solrFactoryMock;

    /** @var SolrClient */
    protected $_solrClientMock;

    /** @var \Magento\Solr\Model\Adapter\PhpExtension */
    protected $_solrAdapteryMock;

    /**
     * Set adapter mocks
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')->getMock();
        $this->_solrFactoryMock = $this->getMock(
            'Magento\Solr\Model\SolrFactory',
            array(),
            array(),
            '',
            false,
            false
        );
        $this->_solrAdapteryMock = $this->getMock(
            'Magento\Solr\Model\Adapter\PhpExtension',
            array(),
            array(),
            '',
            false,
            false
        );

        $this->_factoryObject = new \Magento\Solr\Model\SolrFactory($this->_objectManager);
    }

    /**
     * Test if we get Solr factory
     */
    public function testGetClient()
    {
        $options = array('attr1' => 'value1', 'attr2' => 'value2');
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'SolrClient'
        )->will(
            $this->returnValue($this->_solrClientMock)
        );

        $this->_factoryObject->createClient($options);
    }

    /**
     * Test if we get adapter
     */
    public function testCreateAdapter()
    {
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Solr\Model\Adapter\PhpExtension'
        )->will(
            $this->returnValue($this->_solrAdapteryMock)
        );
        $this->_factoryObject->createAdapter();
    }
}
