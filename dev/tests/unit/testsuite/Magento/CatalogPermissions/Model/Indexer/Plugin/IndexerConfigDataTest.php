<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class IndexerConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\IndexerConfigData
     */
    protected $model;

    /**
     * @var \Magento\CatalogPermissions\App\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock(
            'Magento\CatalogPermissions\App\Config',
            array('isEnabled'),
            array(),
            '',
            false
        );
        $this->subjectMock = $this->getMock('Magento\Indexer\Model\Config\Data', array(), array(), '', false);

        $this->model = new \Magento\CatalogPermissions\Model\Indexer\Plugin\IndexerConfigData($this->configMock);
    }

    /**
     * @param bool $isEnabled
     * @param string $path
     * @param mixed $default
     * @param array $inputData
     * @param array $outputData
     * @dataProvider aroundGetDataProvider
     */
    public function testAroundGet($isEnabled, $path, $default, $inputData, $outputData)
    {
        $closureMock = function () use ($inputData) {
            return $inputData;
        };
        $this->configMock->expects($this->any())->method('isEnabled')->will($this->returnValue($isEnabled));

        $this->assertEquals($outputData, $this->model->aroundGet($this->subjectMock, $closureMock, $path, $default));
    }

    public function aroundGetDataProvider()
    {
        $categoryIndexerData = array(
            'indexer_id' => \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description'
        );
        $productIndexerData = array(
            'indexer_id' => \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID,
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description'
        );

        return array(
            array(
                true,
                null,
                null,
                array(
                    \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID => $categoryIndexerData,
                    \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID => $productIndexerData
                ),
                array(
                    \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID => $categoryIndexerData,
                    \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID => $productIndexerData
                )
            ),
            array(
                false,
                null,
                null,
                array(
                    \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID => $categoryIndexerData,
                    \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID => $productIndexerData
                ),
                array()
            ),
            array(
                false,
                \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
                null,
                $categoryIndexerData,
                null
            ),
            array(
                false,
                \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID,
                null,
                $productIndexerData,
                null
            )
        );
    }
}
