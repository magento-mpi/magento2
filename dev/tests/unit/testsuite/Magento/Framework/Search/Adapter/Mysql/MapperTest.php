<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource;
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
     * @var \Magento\Framework\Search\Adapter\Mysql\ScoreBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scoreBuilder;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\ScoreBuilderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scoreBuilderFactory;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match|\PHPUnit_Framework_MockObject_MockObject
     */
    private $matchQueryBuilder;

    /**
     * @var Mapper
     */
    private $mapper;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $connectionAdapter = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['select'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $connectionAdapter->expects($this->any())->method('select')->will($this->returnValue($this->select));

        $this->resource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->any())->method('getConnection')
            ->with(Resource::DEFAULT_READ_RESOURCE)
            ->will($this->returnValue($connectionAdapter));

        $this->scoreBuilder = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\ScoreBuilder')
            ->setMethods(['clear'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->scoreBuilderFactory = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\ScoreBuilderFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->scoreBuilderFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->scoreBuilder));

        $this->request = $this->getMockBuilder('Magento\Framework\Search\RequestInterface')
            ->setMethods(['getQuery'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->matchQueryBuilder = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match')
            ->setMethods(['build'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->mapper = $helper->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Mapper',
            [
                'resource' => $this->resource,
                'scoreBuilderFactory' => $this->scoreBuilderFactory,
                'matchQueryBuilder' => $this->matchQueryBuilder
            ]
        );
    }

    public function testBuildMatchQuery()
    {
        $query = $this->createMatchQuery();

        $this->matchQueryBuilder->expects($this->once())->method('build')
            ->with(
                $this->equalTo($this->scoreBuilder),
                $this->equalTo($this->select),
                $this->equalTo($query),
                $this->equalTo(Mapper::BOOL_MUST)
            )
            ->will($this->returnValue($this->select));

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

        $this->matchQueryBuilder->expects($this->exactly(3))->method('build')
            ->will($this->returnValue($this->select));

        $query->expects($this->once())
            ->method('getMust')
            ->will(
                $this->returnValue(
                    [
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
        $query->expects($this->once())->method('getType')
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
