<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class IndexerConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Plugin\IndexerConfigData
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    /**
     * @var \Magento\Code\Plugin\InvocationChain|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chainMock;

    protected function setUp()
    {
        $this->stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State', array('isFlatEnabled'), array(), '', false
        );

        $this->chainMock = $this->getMock(
            'Magento\Code\Plugin\InvocationChain', array('proceed'), array(), '', false
        );

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\Plugin\IndexerConfigData(
            $this->stateMock
        );
    }

    /**
     * @param bool $isFlat
     * @param array $arguments
     * @param array $inputData
     * @param array $outputData
     * @dataProvider aroundGetDataProvider
     */
    public function testAroundGet($isFlat, array $arguments, $inputData, $outputData)
    {
        $this->stateMock->expects($this->once())
            ->method('isFlatEnabled')
            ->will($this->returnValue($isFlat));

        $this->chainMock->expects($this->once())
            ->method('proceed')
            ->with($arguments)
            ->will($this->returnValue($inputData));

        $this->assertEquals($outputData, $this->model->aroundGet($arguments, $this->chainMock));
    }

    public function aroundGetDataProvider()
    {
        $flatIndexerData = array(
            'indexer_id' => 'catalog_category_flat',
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description',
        );
        $otherIndexerData = array(
            'indexer_id' => 'other_indexer',
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description',
        );
        return array(
            // flat is enabled, nothing is being changed
            array(
                true,
                array(),
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
            ),
            // flat is disabled, path is absent, flat indexer is being removed
            array(
                false,
                array(),
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
                array('other_indexer' => $otherIndexerData),
            ),
            // flat is disabled, path is null, flat indexer is being removed
            array(
                false,
                array(null),
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
                array('other_indexer' => $otherIndexerData),
            ),
            // flat is disabled, path is flat indexer, flat indexer is being removed
            array(
                false,
                array('catalog_category_flat'),
                $flatIndexerData,
                null,
            ),
            // flat is disabled, path is flat indexer, default is array(), flat indexer is being array()
            array(
                false,
                array('catalog_category_flat', array()),
                $flatIndexerData,
                array(),
            ),
            // flat is disabled, path is other indexer, nothing is being changed
            array(
                false,
                array('other_indexer'),
                $otherIndexerData,
                $otherIndexerData,
            ),
        );
    }
}
