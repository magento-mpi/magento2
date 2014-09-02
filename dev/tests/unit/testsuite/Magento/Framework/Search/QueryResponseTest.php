<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\TestFramework\Helper\ObjectManager;

class QueryResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Document[]
     */
    private $documents = [];

    /**
     * @var \Magento\Framework\Search\Document
     */
    private $aggregations = [];

    /**
     * @var \Magento\Framework\Search\QueryResponse | \PHPUnit_Framework_MockObject_MockObject
     */
    private $queryResponse;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        for ($count = 0; $count < 5; $count++) {
            $document = $this->getMockBuilder('Magento\Framework\Search\Document')
                ->disableOriginalConstructor()
                ->getMock();

            $document->expects($this->any())->method('getId')->will($this->returnValue($count));
            $this->documents[] = $document;
        }

        $this->aggregations = $document = $this->getMockBuilder('Magento\Framework\Search\Document')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryResponse = $helper->getObject(
            'Magento\Framework\Search\QueryResponse',
            [
                'documents' => $this->documents,
                'aggregations' => $this->aggregations,
            ]
        );
    }

    public function testGetIterator()
    {
        $count = 0;
        foreach ($this->queryResponse as $document) {
             $this->assertEquals($document->getId(), $count);
             $count++;
        }
    }

    public function testCount()
    {
        $this->assertEquals(count($this->queryResponse), 5);
    }

    public function testGetAggregations()
    {
        $aggregations = $this->queryResponse->getAggregations();
        $this->assertInstanceOf('Magento\Framework\Search\Document', $aggregations);
    }
}
