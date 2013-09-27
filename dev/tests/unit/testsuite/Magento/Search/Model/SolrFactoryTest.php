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
namespace Magento\Search\Model;

class SolrFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Search\Model\SolrFactory|\PHPUnit_Framework_MockObject_MockObject*/
    protected $_factoryObject;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var \Magento\Search\Model\SolrFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_solrFactoryMock;

    /** @var SolrClient */
    protected $_solrClientMock;

    /** @var \Magento\Search\Model\Adapter\PhpExtension */
    protected $_solrAdapteryMock;

    /**
     * Set adapter mocks
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $this->_solrFactoryMock = $this->getMock('Magento\Search\Model\SolrFactory',
            array(), array(), '', false, false);
        $this->_solrAdapteryMock = $this->getMock('Magento\Search\Model\Adapter\PhpExtension',
            array(), array(), '', false, false);

        $this->_factoryObject = new \Magento\Search\Model\SolrFactory($this->_objectManager);
    }

    /**
     * Test if we get Solr factory
     */
    public function testGetClient()
    {
        $options = array('attr1' => 'value1', 'attr2' => 'value2');
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('SolrClient')
            ->will($this->returnValue($this->_solrClientMock));

        $this->_factoryObject->createClient($options);
    }

    /**
     * Test if we get adapter
     */
    public function testCreateAdapter()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Search\Model\Adapter\PhpExtension')
            ->will($this->returnValue($this->_solrAdapteryMock));
        $this->_factoryObject->createAdapter();
    }
}

