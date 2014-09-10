<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\TestFramework\Helper\ObjectManager;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Search\Request\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\Framework\Search\Request\Mapper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMapper;

    /**
     * @var \Magento\Framework\Search\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->config = $this->getMockBuilder('Magento\Framework\Search\Request\Config')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->requestMapper = $this->getMockBuilder('Magento\Framework\Search\Request\Mapper')
            ->setMethods(['getRootQuery', 'getBuckets'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Magento\Framework\Search\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestBuilder = $helper->getObject(
            'Magento\Framework\Search\RequestBuilder',
            ['config' => $this->config, 'objectManager' => $this->objectManager]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Request name 'rn' doesn't exist.
     */
    public function testCreateInvalidArgumentException()
    {
        $requestName = 'rn';

        $this->requestBuilder->setRequestName($requestName);
        $this->config->expects($this->once())->method('get')->with($this->equalTo($requestName))->willReturn(null);

        $this->requestBuilder->create();
    }

    public function testCreate()
    {
        $data = [
            'dimensions' => [
                'scope' => [
                    'name' => 'scope',
                    'value' => 'default',
                ],
            ],
            'queries' => [
                'one_match_filters' => [
                    'name' => 'one_match_filters',
                    'boost' => '2',
                    'queryReference' => [
                        [
                            'clause' => 'must',
                            'ref' => 'fulltext_search_query',
                        ],
                        [
                            'clause' => 'must',
                            'ref' => 'fulltext_search_query2',
                        ],
                    ],
                    'type' => 'boolQuery',
                ],
                'fulltext_search_query' => [
                    'name' => 'fulltext_search_query',
                    'boost' => '5',
                    'match' => [
                        [
                            'field' => 'data_index',
                            'boost' => '2',
                        ],
                    ],
                    'type' => 'matchQuery',
                ],
                'fulltext_search_query2' => [
                    'name' => 'fulltext_search_query2',
                    'filterReference' => [
                        [
                            'ref' => 'pid',
                        ],
                    ],
                    'type' => 'filteredQuery',
                ],
            ],
            'filters' => [
                'pid' => [
                    'name' => 'pid',
                    'filterReference' => [
                        [
                            'clause' => 'should',
                            'ref' => 'pidm',
                        ],
                        [
                            'clause' => 'should',
                            'ref' => 'pidsh',
                        ],
                    ],
                    'type' => 'boolFilter',
                ],
                'pidm' => [
                    'name' => 'pidm',
                    'field' => 'product_id',
                    'type' => 'rangeFilter',
                ],
                'pidsh' => [
                    'name' => 'pidsh',
                    'field' => 'product_id',
                    'type' => 'termFilter',
                ],
            ],
            'from' => '10',
            'size' => '10',
            'query' => 'one_match_filters',
            'index' => 'catalogsearch_fulltext',
            'aggregations' => [],
        ];

        $requestName = 'rn';

        $this->requestBuilder->bindQuery('fulltext_search_query', 'socks');
        $this->requestBuilder->bindFilter('pidsh', 4);
        $this->requestBuilder->bindFilter('pidm', ['from' => 1, 'to' => 3]);
        $this->requestBuilder->setRequestName($requestName);
        $this->requestBuilder->setSize(10);
        $this->requestBuilder->setFrom(10);
        $this->requestBuilder->bindDimension('scope', 'default');

        $this->requestMapper->expects($this->once())->method('getRootQuery')->willReturn([]);

        $this->objectManager->expects($this->at(0))->method('create')->willReturn($this->requestMapper);
        $this->objectManager->expects($this->at(2))->method('create')->willReturn($this->request);
        $this->config->expects($this->once())->method('get')->with($this->equalTo($requestName))->willReturn($data);

        $result = $this->requestBuilder->create();

        $this->assertInstanceOf('\Magento\Framework\Search\Request', $result);
    }
}
