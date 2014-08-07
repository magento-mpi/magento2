<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

use Magento\Framework\Search\Request\Query\Filter;
use Magento\TestFramework\Helper\ObjectManager;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    const ROOT_QUERY = 'someQuery';

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $helper;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Search\Request\Query\Match|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryMatch;

    /**
     * @var \Magento\Framework\Search\Request\Query\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBool;

    /**
     * @var \Magento\Framework\Search\Request\Query\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryFilter;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Term|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterTerm;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Range|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRange;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBool;

    protected function setUp()
    {
        $this->helper = new ObjectManager($this);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create', 'get', 'configure'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryMatch = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Match')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBool = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Bool')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryFilter = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Filter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterTerm = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Term')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterRange = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Range')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterBool = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Bool')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param $queries
     * @dataProvider getQueryMatchProvider
     */
    public function testGetQueryMatch($queries)
    {
        $query = $queries[self::ROOT_QUERY];
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $this->objectManager->expects($this->once())->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Match'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'matches' => $query['match']
                    ]
                )
            )
            ->will($this->returnValue($this->queryMatch));

        $this->assertEquals($this->queryMatch, $mapper->getRootQuery());
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     */
    public function testGetQueryNotUsedStateException()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_MATCH,
                'name' => 'someName',
                'boost' => 3,
                'match' => 'someMatches'
            ],
            'notUsedQuery' => [
                'type' => QueryInterface::TYPE_MATCH,
                'name' => 'someName',
                'boost' => 3,
                'match' => 'someMatches'
            ]
        ];
        $query = $queries['someQuery'];
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $this->objectManager->expects($this->once())->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Match'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'matches' => $query['match']
                    ]
                )
            )
            ->will($this->returnValue($this->queryMatch));

        $this->assertEquals($this->queryMatch, $mapper->getRootQuery());
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     */
    public function testGetQueryUsedStateException()
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_BOOL,
                        'name' => 'someName',
                        'queryReference' => [
                            [
                                'clause' => 'someClause',
                                'ref' => 'someQuery'
                            ]
                        ]
                    ]
                ],
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $this->assertEquals($this->queryMatch, $mapper->getRootQuery());
    }

    /**
     * @param $queries
     * @dataProvider getQueryFilterQueryReferenceProvider
     */
    public function testGetQueryFilterQueryReference($queries)
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $query = $queries['someQueryMatch'];
        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Match'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => 1,
                        'matches' => 'someMatches'
                    ]
                )
            )
            ->will($this->returnValue($this->queryMatch));
        $query = $queries[self::ROOT_QUERY];
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Filter'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'reference' => $this->queryMatch,
                        'referenceType' => Filter::REFERENCE_QUERY
                    ]
                )
            )
            ->will($this->returnValue($this->queryFilter));

        $this->assertEquals($this->queryFilter, $mapper->getRootQuery());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Reference is not provided
     */
    public function testGetQueryFilterReferenceException()
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [
                    'someQuery' => [
                        'type' => QueryInterface::TYPE_FILTER
                    ]
                ],
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $mapper->getRootQuery();
    }

    /**
     * @param $queries
     * @dataProvider getQueryBoolProvider
     */
    public function testGetQueryBool($queries)
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $query = $queries['someQueryMatch'];
        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Match'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => 1,
                        'matches' => 'someMatches'
                    ]
                )
            )
            ->will($this->returnValue($this->queryMatch));
        $query = $queries[self::ROOT_QUERY];
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Bool'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => isset($query['boost']) ? $query['boost'] : 1,
                        'someClause' => ['someQueryMatch' => $this->queryMatch]
                    ]
                )
            )
            ->will($this->returnValue($this->queryBool));

        $this->assertEquals($this->queryBool, $mapper->getRootQuery());
    }

    /**
     * #@expectedException \InvalidArgumentException
     */
    public function testGetQueryInvalidArgumentException()
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [
                    self::ROOT_QUERY => [
                        'type' => 'invalid_type'
                    ]
                ],
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $mapper->getRootQuery();
    }

    /**
     * @expectedException \Exception
     */
    public function testGetQueryException()
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [],
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $mapper->getRootQuery();
    }

    public function testGetFilterTerm()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_FILTER,
                'name' => 'someName',
                'filterReference' => [
                    [
                        'ref' => 'someFilter'
                    ]
                ]
            ]
        ];
        $filters = [
            'someFilter' => [
                'type' => FilterInterface::TYPE_TERM,
                'name' => 'someName',
                'field' => 'someField',
                'value' => 'someValue'
            ]
        ];

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => $filters
            ]
        );

        $filter = $filters['someFilter'];
        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Filter\Term'),
                $this->equalTo(
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'value' => $filter['value']
                    ]
                )
            )
            ->will($this->returnValue($this->filterTerm));
        $query = $queries[self::ROOT_QUERY];
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Filter'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => 1,
                        'reference' => $this->filterTerm,
                        'referenceType' => Filter::REFERENCE_FILTER
                    ]
                )
            )
            ->will($this->returnValue($this->queryFilter));

        $this->assertEquals($this->queryFilter, $mapper->getRootQuery());
    }

    public function testGetFilterRange()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_FILTER,
                'name' => 'someName',
                'filterReference' => [
                    [
                        'ref' => 'someFilter'
                    ]
                ]
            ]
        ];
        $filters = [
            'someFilter' => [
                'type' => FilterInterface::TYPE_RANGE,
                'name' => 'someName',
                'field' => 'someField',
                'from' => 'from',
                'to' => 'to'
            ]
        ];

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => $filters
            ]
        );

        $filter = $filters['someFilter'];
        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Filter\Range'),
                $this->equalTo(
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'from' => $filter['from'],
                        'to' => $filter['to']
                    ]
                )
            )
            ->will($this->returnValue($this->filterRange));
        $query = $queries[self::ROOT_QUERY];
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Filter'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => 1,
                        'reference' => $this->filterRange,
                        'referenceType' => Filter::REFERENCE_FILTER
                    ]
                )
            )
            ->will($this->returnValue($this->queryFilter));

        $this->assertEquals($this->queryFilter, $mapper->getRootQuery());
    }

    public function testGetFilterBool()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_FILTER,
                'name' => 'someName',
                'filterReference' => [
                    [
                        'ref' => 'someFilter'
                    ]
                ]
            ]
        ];
        $filters = [
            'someFilter' => [
                'type' => FilterInterface::TYPE_BOOL,
                'name' => 'someName',
                'filterReference' => [
                    [
                        'ref' => 'someFilterTerm',
                        'clause' => 'someClause'
                    ]
                ]
            ],
            'someFilterTerm' => [
                'type' => FilterInterface::TYPE_TERM,
                'name' => 'someName',
                'field' => 'someField',
                'value' => 'someValue'
            ]
        ];

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => $filters
            ]
        );

        $filter = $filters['someFilterTerm'];
        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Filter\Term'),
                $this->equalTo(
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'value' => $filter['value']
                    ]
                )
            )
            ->will($this->returnValue($this->filterTerm));
        $filter = $filters['someFilter'];
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Filter\Bool'),
                $this->equalTo(
                    [
                        'name' => $filter['name'],
                        'someClause' => ['someFilterTerm' => $this->filterTerm]
                    ]
                )
            )
            ->will($this->returnValue($this->filterBool));
        $query = $queries[self::ROOT_QUERY];
        $this->objectManager->expects($this->at(2))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Filter'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => 1,
                        'reference' => $this->filterBool,
                        'referenceType' => Filter::REFERENCE_FILTER
                    ]
                )
            )
            ->will($this->returnValue($this->queryFilter));

        $this->assertEquals($this->queryFilter, $mapper->getRootQuery());
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     */
    public function testGetFilterNotUsedStateException()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_FILTER,
                'name' => 'someName',
                'filterReference' => [
                    [
                        'ref' => 'someFilter'
                    ]
                ]
            ]
        ];
        $filters = [
            'someFilter' => [
                'type' => FilterInterface::TYPE_TERM,
                'name' => 'someName',
                'field' => 'someField',
                'value' => 'someValue'
            ],
            'notUsedFilter' => [
                'type' => FilterInterface::TYPE_TERM,
                'name' => 'someName',
                'field' => 'someField',
                'value' => 'someValue'
            ]
        ];

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => $filters
            ]
        );

        $filter = $filters['someFilter'];
        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Filter\Term'),
                $this->equalTo(
                    [
                        'name' => $filter['name'],
                        'field' => $filter['field'],
                        'value' => $filter['value']
                    ]
                )
            )
            ->will($this->returnValue($this->filterTerm));
        $query = $queries[self::ROOT_QUERY];
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Query\Filter'),
                $this->equalTo(
                    [
                        'name' => $query['name'],
                        'boost' => 1,
                        'reference' => $this->filterTerm,
                        'referenceType' => Filter::REFERENCE_FILTER
                    ]
                )
            )
            ->will($this->returnValue($this->queryFilter));

        $this->assertEquals($this->queryFilter, $mapper->getRootQuery());
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     */
    public function testGetFilterUsedStateException()
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_FILTER,
                        'name' => 'someName',
                        'filterReference' => [
                            [
                                'ref' => 'someFilter'
                            ]
                        ]
                    ]
                ],
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => [
                    'someFilter' => [
                        'type' => FilterInterface::TYPE_BOOL,
                        'name' => 'someName',
                        'filterReference' => [
                            [
                                'ref' => 'someFilter',
                                'clause' => 'someClause'
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals($this->queryMatch, $mapper->getRootQuery());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid filter type
     */
    public function testGetFilterInvalidArgumentException()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_FILTER,
                'name' => 'someName',
                'filterReference' => [
                    [
                        'ref' => 'someFilter'
                    ]
                ]
            ]
        ];
        $filters = [
            'someFilter' => [
                'type' => 'invalid_type'
            ]
        ];

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => $filters
            ]
        );

        $this->assertEquals($this->queryFilter, $mapper->getRootQuery());
    }

    /**
     * @expectedException \Exception
     */
    public function testGetFilterException()
    {
        $queries = [
            self::ROOT_QUERY => [
                'type' => QueryInterface::TYPE_FILTER,
                'name' => 'someName',
                'boost' => 3,
                'filterReference' => [
                    [
                        'ref' => 'someQueryMatch',
                        'clause' => 'someClause',
                    ]
                ]
            ]
        ];

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $queries,
                'rootQueryName' => self::ROOT_QUERY,
                'filters' => []
            ]
        );

        $this->assertEquals($this->queryBool, $mapper->getRootQuery());
    }

    public function getQueryMatchProvider()
    {
        return [
            [
                [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_MATCH,
                        'name' => 'someName',
                        'boost' => 3,
                        'match' => 'someMatches'
                    ]
                ]
            ],
            [
                [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_MATCH,
                        'name' => 'someName',
                        'match' => 'someMatches'
                    ]
                ]
            ]
        ];
    }

    public function getQueryFilterQueryReferenceProvider()
    {
        return [
            [
                [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_FILTER,
                        'name' => 'someName',
                        'boost' => 3,
                        'queryReference' => [
                            [
                                'ref' => 'someQueryMatch',
                                'clause' => 'someClause',
                            ]
                        ]
                    ],
                    'someQueryMatch' => [
                        'type' => QueryInterface::TYPE_MATCH,
                        'name' => 'someName',
                        'match' => 'someMatches'
                    ]
                ]
            ],
            [
                [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_FILTER,
                        'name' => 'someName',
                        'queryReference' => [
                            [
                                'ref' => 'someQueryMatch',
                                'clause' => 'someClause',
                            ]
                        ]
                    ],
                    'someQueryMatch' => [
                        'type' => QueryInterface::TYPE_MATCH,
                        'name' => 'someName',
                        'match' => 'someMatches'
                    ]
                ]
            ]
        ];
    }

    public function getQueryBoolProvider()
    {
        return [
            [
                [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_BOOL,
                        'name' => 'someName',
                        'boost' => 3,
                        'queryReference' => [
                            [
                                'ref' => 'someQueryMatch',
                                'clause' => 'someClause',
                            ]
                        ]
                    ],
                    'someQueryMatch' => [
                        'type' => QueryInterface::TYPE_MATCH,
                        'name' => 'someName',
                        'match' => 'someMatches'
                    ]
                ]
            ],
            [
                [
                    self::ROOT_QUERY => [
                        'type' => QueryInterface::TYPE_BOOL,
                        'name' => 'someName',
                        'queryReference' => [
                            [
                                'ref' => 'someQueryMatch',
                                'clause' => 'someClause',
                            ]
                        ]
                    ],
                    'someQueryMatch' => [
                        'type' => QueryInterface::TYPE_MATCH,
                        'name' => 'someName',
                        'match' => 'someMatches'
                    ]
                ]
            ]
        ];
    }
}
