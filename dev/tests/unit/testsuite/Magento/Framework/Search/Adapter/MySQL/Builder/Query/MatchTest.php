<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\MySQL\Builder\Query;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Query\Bool;
use Magento\TestFramework\Helper\ObjectManager;

class MatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scoreManager;

    /**
     * @var \Magento\Framework\Search\Adapter\MySQL\Builder\Query\Match
     */
    private $match;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->scoreManager = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\ScoreManager')
            ->setMethods(['setQueryBoost', 'addCondition'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->match = $helper->getObject(
            'Magento\Framework\Search\Adapter\MySQL\Builder\Query\Match',
            ['scoreManager' => $this->scoreManager]
        );
    }

    public function testBuildQuery()
    {
        $queryName = 'query_name';
        $queryBoost = 3.14;

        /** @var Select|\PHPUnit_Framework_MockObject_MockObject $select */
        $select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods(['getMatchQuery', 'match'])
            ->disableOriginalConstructor()
            ->getMock();
        $select->expects($this->once())->method('getMatchQuery')
            ->with($this->equalTo('some_field'), $this->equalTo('-some_value'))
            ->will($this->returnValue('matchedQuery'));
        $select->expects($this->once())->method('match')
            ->with(
                $this->equalTo('some_field'),
                $this->equalTo('-some_value'),
                $this->equalTo(true),
                $this->equalTo(Select::FULLTEXT_MODE_BOOLEAN)
            );

        /** @var \Magento\Framework\Search\Request\Query\Match|\PHPUnit_Framework_MockObject_MockObject $query */
        $query = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Match')
            ->setMethods(['getName', 'getBoost', 'getMatches'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->exactly(2))->method('getName')->will($this->returnValue($queryName));
        $query->expects($this->once())->method('getBoost')->will($this->returnValue($queryBoost));
        $query->expects($this->once())->method('getMatches')->will(
            $this->returnValue([['field' => 'some_field', 'value' => 'some_value', 'boost' => 6.28]])
        );

        $this->scoreManager->expects($this->once())->method('setQueryBoost')
            ->with($this->equalTo($queryName), $this->equalTo($queryBoost));
        $this->scoreManager->expects($this->once())->method('addCondition')
            ->with(
                $this->equalTo($queryName),
                $this->equalTo('matchedQuery'),
                $this->equalTo(6.28),
                $this->equalTo(Select::FULLTEXT_MODE_BOOLEAN)
            );

        $conditionType = Bool::QUERY_CONDITION_NOT;

        $result = $this->match->buildQuery($select, $query, $conditionType);

        $this->assertEquals($select, $result);
    }
}
