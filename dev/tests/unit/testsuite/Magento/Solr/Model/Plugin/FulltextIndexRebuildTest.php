<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Plugin;

class FulltextIndexRebuildTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\Model\Plugin\FulltextIndexRebuild
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_searchHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_engineProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filterPriceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_searchEngineMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->_engineProviderMock = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\EngineProvider',
            [],
            [],
            '',
            false
        );
        $this->_searchHelperMock = $this->getMock('Magento\Solr\Helper\Data', [], [], '', false);
        $this->_cacheMock = $this->getMock('Magento\Framework\App\CacheInterface', [], [], '', false);
        $this->_searchEngineMock = $this->getMock(
            'Magento\Solr\Model\Resource\Solr\Engine',
            [],
            [],
            '',
            false
        );
        $this->_filterPriceMock = $this->getMock(
            'Magento\Solr\Model\Layer\Category\Filter\Price',
            [],
            [],
            '',
            false
        );
        $this->subjectMock = $this->getMock(
            'Magento\CatalogSearch\Model\Indexer\Fulltext',
            [],
            [],
            '',
            false
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManager->getObject(
            '\Magento\Solr\Model\Plugin\FulltextIndexRebuild',
            [
                'engineProvider' => $this->_engineProviderMock,
                'searchHelper' => $this->_searchHelperMock,
                'layerFilterPrice' => $this->_filterPriceMock,
                'cache' => $this->_cacheMock,
            ]
        );
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::beforeExecuteFull
     */
    public function testBeforeRebuildIndexNoThirdPartyEngine()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(false)
        );

        $this->_engineProviderMock->expects($this->never())->method('get');

        $this->_model->beforeExecuteFull($this->subjectMock);
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::beforeExecuteFull
     */
    public function testBeforeRebuildIndexThirdPartyEngineNoHoldCommit()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(true)
        );

        $this->_searchEngineMock->expects($this->once())->method('holdCommit')->will($this->returnValue(false));

        $this->_searchEngineMock->expects($this->never())->method('setIndexNeedsOptimization');

        $this->_engineProviderMock->expects(
            $this->once()
        )->method(
            'get'
        )->will(
            $this->returnValue($this->_searchEngineMock)
        );

        $this->_model->beforeExecuteFull($this->subjectMock);
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::beforeExecuteFull
     */
    public function testBeforeRebuildIndexThirdPartyEngineHoldCommit()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(true)
        );

        $this->_searchEngineMock->expects($this->once())->method('holdCommit')->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())->method('setIndexNeedsOptimization');

        $this->_engineProviderMock->expects(
            $this->once()
        )->method(
            'get'
        )->will(
            $this->returnValue($this->_searchEngineMock)
        );

        $this->_model->beforeExecuteFull($this->subjectMock);
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::afterExecuteFull
     */
    public function testAfterRebuildIndexNoThirdPartyEngine()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(false)
        );

        $this->_engineProviderMock->expects($this->never())->method('get');

        $this->_model->afterExecuteFull($this->subjectMock);
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::afterExecuteFull
     */
    public function testAfterRebuildIndexThirdPartyEngine()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(true)
        );

        $this->_searchEngineMock->expects($this->once())->method('allowCommit')->will($this->returnValue(false));

        $this->_searchEngineMock->expects($this->never())->method('getIndexNeedsOptimization');

        $this->_engineProviderMock->expects(
            $this->once()
        )->method(
            'get'
        )->will(
            $this->returnValue($this->_searchEngineMock)
        );

        $this->_model->afterExecuteFull($this->subjectMock);
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::afterExecuteFull
     */
    public function testAfterRebuildIndexThirdPartyEngineAllowCommitOptimizationNeeded()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(true)
        );

        $this->_searchEngineMock->expects($this->once())->method('allowCommit')->will($this->returnValue(true));

        $this->_searchEngineMock->expects(
            $this->once()
        )->method(
            'getIndexNeedsOptimization'
        )->will(
            $this->returnValue(true)
        );

        $this->_searchEngineMock->expects($this->once())->method('optimizeIndex');

        $this->_searchEngineMock->expects($this->never())->method('commitChanges');

        $this->_engineProviderMock->expects(
            $this->once()
        )->method(
            'get'
        )->will(
            $this->returnValue($this->_searchEngineMock)
        );

        $cacheTag = 'cacheTag';
        $this->_filterPriceMock->expects($this->once())->method('getCacheTag')->will($this->returnValue($cacheTag));

        $this->_cacheMock->expects($this->once())->method('clean')->will($this->returnValue([$cacheTag]));

        $this->_model->afterExecuteFull($this->subjectMock);
    }

    /**
     * @covers \Magento\Solr\Model\Plugin\FulltextIndexRebuild::afterExecuteFull
     */
    public function testAfterRebuildIndexThirdPartyEngineAllowCommitOptimizationNotNeeded()
    {
        $this->_searchHelperMock->expects(
            $this->once()
        )->method(
            'isThirdPartyEngineAvailable'
        )->will(
            $this->returnValue(true)
        );

        $this->_searchEngineMock->expects($this->once())->method('allowCommit')->will($this->returnValue(true));

        $this->_searchEngineMock->expects(
            $this->once()
        )->method(
            'getIndexNeedsOptimization'
        )->will(
            $this->returnValue(false)
        );

        $this->_searchEngineMock->expects($this->never())->method('optimizeIndex');

        $this->_searchEngineMock->expects($this->once())->method('commitChanges');

        $this->_engineProviderMock->expects(
            $this->once()
        )->method(
            'get'
        )->will(
            $this->returnValue($this->_searchEngineMock)
        );

        $cacheTag = 'cacheTag';
        $this->_filterPriceMock->expects($this->once())->method('getCacheTag')->will($this->returnValue($cacheTag));

        $this->_cacheMock->expects($this->once())->method('clean')->will($this->returnValue([$cacheTag]));

        $this->_model->afterExecuteFull($this->subjectMock);
    }
}
