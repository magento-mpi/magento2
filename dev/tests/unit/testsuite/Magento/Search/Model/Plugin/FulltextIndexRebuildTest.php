<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_Plugin_FulltextIndexRebuildTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Search_Model_Plugin_FulltextIndexRebuild
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_searchHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_engineProviderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filterPriceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_searchEngineMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fulltextSearchMock;

    /**
     * @var array
     */
    protected $_arguments;

    protected function setUp()
    {
        $this->_engineProviderMock = $this->getMock('Magento_CatalogSearch_Model_Resource_EngineProvider', array(),
            array(), '', false);
        $this->_searchHelperMock = $this->getMock('Magento_Search_Helper_Data', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false);
        $this->_searchEngineMock = $this->getMock('Magento_Search_Model_Resource_Engine', array(), array(), '', false);
        $this->_fulltextSearchMock = $this->getMock(
            'Magento_CatalogSearch_Model_Fulltext', array(), array(), '', false
        );
        $this->_filterPriceMock = $this->getMock(
            'Magento_Search_Model_Catalog_Layer_Filter_Price', array(), array(), '', false
        );

        $this->_arguments = array(1, array(1,2));

        $this->_model = new Magento_Search_Model_Plugin_FulltextIndexRebuild(
            $this->_engineProviderMock,
            $this->_searchHelperMock,
            $this->_filterPriceMock,
            $this->_cacheMock
        );
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::beforeRebuildIndex
     */
    public function testBeforeRebuildIndexNoThirdPartyEngine()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(false));

        $this->_engineProviderMock->expects($this->never())
            ->method('get');

        $this->assertEquals($this->_arguments, $this->_model->beforeRebuildIndex($this->_arguments));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::beforeRebuildIndex
     */
    public function testBeforeRebuildIndexThirdPartyEngine()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('holdCommit')
            ->will($this->returnValue(false));

        $this->_searchEngineMock->expects($this->never())
            ->method('setIndexNeedsOptimization');

        $this->_engineProviderMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_searchEngineMock));

        $this->assertEquals($this->_arguments, $this->_model->beforeRebuildIndex($this->_arguments));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::beforeRebuildIndex
     */
    public function testBeforeRebuildIndexThirdPartyEngineNoProductIds()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('holdCommit')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('setIndexNeedsOptimization');

        $this->_engineProviderMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_searchEngineMock));

        $arguments = $this->_arguments;
        unset($arguments[1]);
        $this->assertEquals($arguments, $this->_model->beforeRebuildIndex($arguments));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::beforeRebuildIndex
     */
    public function testBeforeRebuildIndexThirdPartyEngineProductIdsSet()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('holdCommit')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->never())
            ->method('setIndexNeedsOptimization');

        $this->_engineProviderMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_searchEngineMock));

        $this->assertEquals($this->_arguments, $this->_model->beforeRebuildIndex($this->_arguments));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::afterRebuildIndex
     */
    public function testAfterRebuildIndexNoThirdPartyEngine()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(false));

        $this->_engineProviderMock->expects($this->never())
            ->method('get');

        $this->assertEquals($this->_fulltextSearchMock, $this->_model->afterRebuildIndex($this->_fulltextSearchMock));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::afterRebuildIndex
     */
    public function testAfterRebuildIndexThirdPartyEngine()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('allowCommit')
            ->will($this->returnValue(false));

        $this->_searchEngineMock->expects($this->never())
            ->method('getIndexNeedsOptimization');

        $this->_engineProviderMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_searchEngineMock));

        $this->assertEquals($this->_fulltextSearchMock, $this->_model->afterRebuildIndex($this->_fulltextSearchMock));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::afterRebuildIndex
     */
    public function testAfterRebuildIndexThirdPartyEngineAllowCommitOptimizationNeeded()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('allowCommit')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('getIndexNeedsOptimization')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('optimizeIndex');

        $this->_searchEngineMock->expects($this->never())
            ->method('commitChanges');

        $this->_engineProviderMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_searchEngineMock));

        $cacheTag = 'cacheTag';
        $this->_filterPriceMock->expects($this->once())
            ->method('getCacheTag')
            ->will($this->returnValue($cacheTag));

        $this->_cacheMock->expects($this->once())
            ->method('clean')
            ->will($this->returnValue(array($cacheTag)));

        $this->assertEquals($this->_fulltextSearchMock, $this->_model->afterRebuildIndex($this->_fulltextSearchMock));
    }

    /**
     * @covers Magento_Search_Model_Plugin_FulltextIndexRebuild::afterRebuildIndex
     */
    public function testAfterRebuildIndexThirdPartyEngineAllowCommitOptimizationNotNeeded()
    {
        $this->_searchHelperMock->expects($this->once())
            ->method('isThirdPartyEngineAvailable')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('allowCommit')
            ->will($this->returnValue(true));

        $this->_searchEngineMock->expects($this->once())
            ->method('getIndexNeedsOptimization')
            ->will($this->returnValue(false));

        $this->_searchEngineMock->expects($this->never())
            ->method('optimizeIndex');

        $this->_searchEngineMock->expects($this->once())
            ->method('commitChanges');

        $this->_engineProviderMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_searchEngineMock));

        $cacheTag = 'cacheTag';
        $this->_filterPriceMock->expects($this->once())
            ->method('getCacheTag')
            ->will($this->returnValue($cacheTag));

        $this->_cacheMock->expects($this->once())
            ->method('clean')
            ->will($this->returnValue(array($cacheTag)));

        $this->assertEquals($this->_fulltextSearchMock, $this->_model->afterRebuildIndex($this->_fulltextSearchMock));
    }
}
