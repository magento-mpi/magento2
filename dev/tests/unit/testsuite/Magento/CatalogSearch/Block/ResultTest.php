<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Block;

/**
 * Unit test for \Magento\CatalogSearch\Block\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogSearch\Block\Result */
    protected $model;

    /** @var \Magento\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\Catalog\Model\Layer|\PHPUnit_Framework_MockObject_MockObject */
    protected $layerMock;

    /** @var \Magento\CatalogSearch\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $dataMock;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $childBlockMock;

    public function setUp()
    {
        $this->contextMock = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $this->layerMock = $this->getMock('Magento\Catalog\Model\Layer\Search', [], [], '', false);
        $this->dataMock = $this->getMock('Magento\CatalogSearch\Helper\Data', [], [], '', false);
        $this->model = new Result($this->contextMock, $this->layerMock, $this->dataMock);
    }

    public function testGetSearchQueryText()
    {
        $this->dataMock->expects($this->once())->method('getEscapedQueryText')->will($this->returnValue('query_text'));
        $this->assertEquals('Search results for: \'query_text\'', $this->model->getSearchQueryText());
    }

    public function testGetNoteMessages()
    {
        $this->dataMock->expects($this->once())->method('getNoteMessages')->will($this->returnValue('SOME-MESSAGE'));
        $this->assertEquals('SOME-MESSAGE', $this->model->getNoteMessages());
    }

    /**
     * @param bool $isMinQueryLength
     * @param string $expectedResult
     * @dataProvider getNoResultTextDataProvider
     */
    public function testGetNoResultText($isMinQueryLength, $expectedResult)
    {
        $this->dataMock->expects($this->once())
            ->method('isMinQueryLength')
            ->will($this->returnValue($isMinQueryLength));
        if ($isMinQueryLength) {
            $queryMock = $this->getMock('Magento\CatalogSearch\Model\Query', [], [], '', false);
            $queryMock->expects($this->once())->method('getMinQueryLength')->will($this->returnValue('5'));

            $this->dataMock->expects($this->once())
                ->method('getQuery')
                ->will($this->returnValue($queryMock));
        }
        $this->assertEquals($expectedResult, $this->model->getNoResultText());
    }

    /**
     * @return array
     */
    public function getNoResultTextDataProvider()
    {
        return array(
            array(true, 'Minimum Search query length is 5'),
            array(false, null),
        );
    }
}
