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
 * Test Class for Magento_Search_Model_Factory_Factory
 */
class Magento_Search_Model_SolrFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Search_Model_SolrFactory|PHPUnit_Framework_MockObject_MockObject*/
    protected $_factoryObject;

    /** @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var Magento_Search_Model_SolrFactory|PHPUnit_Framework_MockObject_MockObject */
    protected $_solrFactoryMock;

    /** @var SolrClient */
    protected $_solrClientMock;

    /** @var Magento_Search_Model_Adapter_PhpExtension */
    protected $_solrAdapteryMock;

    /**
     * Set adapter mocks
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')->getMock();
        $this->_solrFactoryMock = $this->getMock('Magento_Search_Model_SolrFactory',
            array(), array(), '', false, false);
        $this->_solrAdapteryMock = $this->getMock('Magento_Search_Model_Adapter_PhpExtension',
            array(), array(), '', false, false);

        $this->_factoryObject = new Magento_Search_Model_SolrFactory($this->_objectManager);
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
            ->with('Magento_Search_Model_Adapter_PhpExtension')
            ->will($this->returnValue($this->_solrAdapteryMock));
        $this->_factoryObject->createAdapter();
    }
}

