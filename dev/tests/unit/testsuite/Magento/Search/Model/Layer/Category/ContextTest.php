<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Solr\Model\Layer\Category;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateKeyMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFilterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;


    /**
     * @var \Magento\Solr\Model\Layer\Category\Context
     */
    protected $model;

    protected function setUp()
    {
        $this->searchProviderMock = $this->getMock(
            '\Magento\Solr\Model\Layer\Category\ItemCollectionProvider',
            array(),
            array(),
            '',
            false
        );
        $this->catalogProviderMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Category\ItemCollectionProvider',
            array(),
            array(),
            '',
            false
        );
        $this->helperMock = $this->getMock('\Magento\Solr\Helper\Data', array(), array(), '', false);
        $this->stateKeyMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Category\StateKey',
            array(),
            array(),
            '',
            false
        );
        $this->collectionFilterMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Category\CollectionFilter',
            array(),
            array(),
            '',
            false
        );

        $this->model = new Context(
            $this->catalogProviderMock,
            $this->stateKeyMock,
            $this->collectionFilterMock,
            $this->searchProviderMock,
            $this->helperMock
        );
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Category\Context::getCollectionProvider
     * @covers \Magento\Solr\Model\Layer\Category\Context::__construct
     */
    public function testGetCollectionProviderEngineAvailable()
    {
        $this->helperMock->expects($this->once())
            ->method('getIsEngineAvailableForNavigation')
            ->will($this->returnValue(true));

        $this->assertSame($this->searchProviderMock, $this->model->getCollectionProvider());
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Category\Context::getCollectionProvider
     */
    public function testGetCollectionProviderEngineUnavailable()
    {
        $this->helperMock->expects($this->once())
            ->method('getIsEngineAvailableForNavigation')
            ->will($this->returnValue(false));

        $this->assertSame($this->catalogProviderMock, $this->model->getCollectionProvider());
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Category\Context::getStateKey
     */
    public function testGetStateKey()
    {
        $this->assertSame($this->stateKeyMock, $this->model->getStateKey());
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Category\Context::getCollectionFilter
     */
    public function testGetCollectionFilter()
    {
        $this->assertSame($this->collectionFilterMock, $this->model->getCollectionFilter());
    }
}
