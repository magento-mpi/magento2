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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State',
            array('isFlatEnabled'),
            array(),
            '',
            false
        );

        $this->subjectMock = $this->getMock('Magento\Indexer\Model\Config\Data', array(), array(), '', false);

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\Plugin\IndexerConfigData($this->stateMock);
    }

    /**
     * @param bool $isFlat
     * @param string $path
     * @param mixed $default
     * @param array $inputData
     * @param array $outputData
     * @dataProvider aroundGetDataProvider
     */
    public function testAroundGet($isFlat, $path, $default, $inputData, $outputData)
    {
        $this->stateMock->expects($this->once())->method('isFlatEnabled')->will($this->returnValue($isFlat));
        $closureMock = function () use ($inputData) {
            return $inputData;
        };
        $this->assertEquals($outputData, $this->model->aroundGet($this->subjectMock, $closureMock, $path, $default));
    }

    public function aroundGetDataProvider()
    {
        $flatIndexerData = array(
            'indexer_id' => 'catalog_category_flat',
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description'
        );
        $otherIndexerData = array(
            'indexer_id' => 'other_indexer',
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description'
        );
        return array(
            array(
                true,
                null,
                null,
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData)
            ),
            array(
                false,
                null,
                null,
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
                array('other_indexer' => $otherIndexerData)
            ),
            array(
                false,
                null,
                null,
                array('catalog_category_flat' => $flatIndexerData, 'other_indexer' => $otherIndexerData),
                array('other_indexer' => $otherIndexerData)
            ),
            array(false, 'catalog_category_flat', null, $flatIndexerData, null),
            array(false, 'catalog_category_flat', null, $flatIndexerData, null),
            array(false, 'other_indexer', null, $otherIndexerData, $otherIndexerData)
        );
    }
}
