<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_Plugin_FulltextIndexRebuildText extends PHPUnit_Framework_TestCase
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
    protected $_catalogHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filterPriceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

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
        $this->_searchHelperMock = $this->getMock('Magento_Search_Helper_Data', array(), array(), '', false);
        $this->_catalogHelperMock = $this->getMock('Magento_CatalogSearch_Helper_Data', array(), array(), '', false);
        $this->_applicationMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_searchEngineMock = $this->getMock('Magento_Search_Model_Resource_Engine', array(), array(), '', false);
        $this->_fulltextSearchMock = $this->getMock(
            'Magento_CatalogSearch_Model_Fulltext',  array(), array(), '', false
        );
        $this->_filterPriceMock = $this->getMock(
            'Magento_Search_Model_Catalog_Layer_Filter_Price', array(), array(), '', false
        );

        $this->_arguments = array('storeId' => 1, 'productIds' => array(1,2));

        $this->_model = new Magento_Search_Model_Plugin_FulltextIndexRebuild(
            $this->_searchHelperMock,
            $this->_catalogHelperMock,
            $this->_filterPriceMock,
            $this->_applicationMock
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

        $this->_catalogHelperMock->expects($this->never())
            ->method('getEngine');

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

        $this->_catalogHelperMock->expects($this->once())
            ->method('getEngine')
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

        $this->_catalogHelperMock->expects($this->once())
            ->method('getEngine')
            ->will($this->returnValue($this->_searchEngineMock));

        $arguments = $this->_arguments;
        $arguments['productIds'] = null;
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

        $this->_catalogHelperMock->expects($this->once())
            ->method('getEngine')
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

        $this->_catalogHelperMock->expects($this->never())
            ->method('getEngine');

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

        $this->_catalogHelperMock->expects($this->once())
            ->method('getEngine')
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

        $this->_catalogHelperMock->expects($this->once())
            ->method('getEngine')
            ->will($this->returnValue($this->_searchEngineMock));

        $cacheTag = 'cacheTag';
        $this->_filterPriceMock->expects($this->once())
            ->method('getCacheTag')
            ->will($this->returnValue($cacheTag));

        $this->_applicationMock->expects($this->once())
            ->method('cleanCache')
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

        $this->_catalogHelperMock->expects($this->once())
            ->method('getEngine')
            ->will($this->returnValue($this->_searchEngineMock));

        $cacheTag = 'cacheTag';
        $this->_filterPriceMock->expects($this->once())
            ->method('getCacheTag')
            ->will($this->returnValue($cacheTag));

        $this->_applicationMock->expects($this->once())
            ->method('cleanCache')
            ->will($this->returnValue(array($cacheTag)));

        $this->assertEquals($this->_fulltextSearchMock, $this->_model->afterRebuildIndex($this->_fulltextSearchMock));
    }
}
