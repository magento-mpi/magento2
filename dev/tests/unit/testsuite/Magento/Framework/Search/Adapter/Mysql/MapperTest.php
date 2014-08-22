<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource\Config;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\TestFramework\Helper\ObjectManager;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;
    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scoreManager;
    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Mapper
     */
    private $mapper;
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->resource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionAdapter = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['select'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $connectionAdapter->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->select));
        $this->resource->expects($this->once())
            ->method('getConnection')
            ->with(Config::DEFAULT_SETUP_CONNECTION)
            ->will($this->returnValue($connectionAdapter));
        $this->scoreManager = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\ScoreManager')
            ->setMethods(['clear'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder('Magento\Framework\Search\RequestInterface')
            ->setMethods(['getQuery'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->mapper = $this->objectManager->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Mapper',
            [
                'resource' => $this->resource,
                'scoreManager' => $this->scoreManager,
            ]
        );
    }

    public function testBuildMatchQuery()
    {
        $query = $this->createMatchQuery();

        $this->request->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $response = $this->mapper->buildQuery($this->request);

        $this->assertEquals($this->select, $response);
    }

    public function testBuildFilterQuery()
    {
        $query = $this->createFilterQuery();
        $this->request->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $response = $this->mapper->buildQuery($this->request);

        $this->assertEquals($this->select, $response);
    }

    public function testBuildBoolQuery()
    {
        $query = $this->createBoolQuery();
        $this->request->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('getMust')
            ->will(
                $this->returnValue(
                    [
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                    ]
                )
            );

        $query->expects($this->once())
            ->method('getShould')
            ->will(
                $this->returnValue(
                    [
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                    ]
                )
            );

        $query->expects($this->once())
            ->method('getMustNot')
            ->will(
                $this->returnValue(
                    [
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                        $this->createMatchQuery(),
                        $this->createFilterQuery(),
                    ]
                )
            );

        $response = $this->mapper->buildQuery($this->request);

        $this->assertEquals($this->select, $response);
    }

    public function testBuildQueryConditionInheritance()
    {
        // @TODO: Finish this test after creating query builder
        $this->markTestSkipped(
            'We need to implement at least one query builder to check which condition was passed to it'
        );
        $query = $this->createBoolQuery();
        $this->request->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $matchQuery = $this->createMatchQuery();
        $nestedBoolQuery = $this->createBoolQuery();
        $nestedBoolQuery->expects($this->once())->method('getMust')->will($this->returnValue([$matchQuery]));
        $nestedBoolQuery->expects($this->once())->method('getShould')->will($this->returnValue([]));
        $nestedBoolQuery->expects($this->once())->method('getMustNot')->will($this->returnValue([]));

        $query->expects($this->once())
            ->method('getMust')
            ->will($this->returnValue([]));

        $query->expects($this->once())
            ->method('getShould')
            ->will($this->returnValue([]));

        $query->expects($this->once())
            ->method('getMustNot')
            ->will($this->returnValue([$nestedBoolQuery]));

        $response = $this->mapper->buildQuery($this->request);

        $this->assertEquals($this->select, $response);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown query type 'unknownQuery'
     */
    public function testGetUnknownQueryType()
    {
        $query = $this->getMockBuilder('Magento\Framework\Search\Request\QueryInterface')
            ->setMethods(['getType'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $query->expects($this->exactly(2))
            ->method('getType')
            ->will($this->returnValue('unknownQuery'));

        $this->request->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $this->mapper->buildQuery($this->request);
    }

    private function createMatchQuery()
    {
        $query = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Match')
            ->setMethods(['getType'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $query->expects($this->exactly(1))
            ->method('getType')
            ->will($this->returnValue(QueryInterface::TYPE_MATCH));
        return $query;
    }

    private function createFilterQuery()
    {
        $query = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Filter')
            ->setMethods(['getType'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $query->expects($this->exactly(1))
            ->method('getType')
            ->will($this->returnValue(QueryInterface::TYPE_FILTER));
        return $query;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createBoolQuery()
    {
        $query = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Bool')
            ->setMethods(['getMust', 'getShould', 'getMustNot', 'getType'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $query->expects($this->exactly(1))
            ->method('getType')
            ->will($this->returnValue(QueryInterface::TYPE_BOOL));
        return $query;
    }

}
