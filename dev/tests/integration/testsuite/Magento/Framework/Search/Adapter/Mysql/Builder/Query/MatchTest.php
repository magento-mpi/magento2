<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Builder\Query;

use Magento\Framework\App\Resource\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Search\Request\Query\Bool;

class MatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function testBuildQuery()
    {
        $expectedGeneratedCondition = "((MATCH ('with_boost') AGAINST ('-wb' IN NATURAL LANGUAGE MODE) * 2.15 + " .
            "MATCH ('without_boost') AGAINST ('-wob' IN NATURAL LANGUAGE MODE) * 1) * 3.14) AS global_score";
        $expectedSql = "SELECT `table`.* FROM `table` WHERE (MATCH ('with_boost') AGAINST ('-wb' IN BOOLEAN MODE)) " .
            "AND (MATCH ('without_boost') AGAINST ('-wob' IN BOOLEAN MODE))";

        /** @var \Magento\Framework\Search\Adapter\Mysql\ScoreManager $scoreManager */
        $scoreManager = $this->objectManager->create('Magento\Framework\Search\Adapter\Mysql\ScoreManager');
        /** @var \Magento\Framework\Search\Adapter\Mysql\Builder\Query\Match $match */
        $match = $this->objectManager->create(
            'Magento\Framework\Search\Adapter\Mysql\Builder\Query\Match',
            ['scoreManager' => $scoreManager]
        );
        /** @var \Magento\Framework\Search\Request\Query\Match $query */
        $query = $this->objectManager->create(
            'Magento\Framework\Search\Request\Query\Match',
            [
                'name' => 'Match query',
                'boost' => 3.14,
                'matches' => [
                    ['field' => 'with_boost', 'value' => 'wb', 'boost' => 2.15],
                    ['field' => 'without_boost', 'value' => 'wob']
                ]
            ]
        );
        /** @var \Magento\Framework\App\Resource $resource */
        $resource = $this->objectManager->create('Magento\Framework\App\Resource');
        /** @var \Magento\Framework\DB\Select $select */
        $select = $resource->getConnection(Config::DEFAULT_SETUP_CONNECTION)->select();
        $select->from('table');

        $resultSelect = $match->buildQuery($select, $query, Bool::QUERY_CONDITION_NOT);
        $this->assertEquals($expectedGeneratedCondition, $scoreManager->getGeneratedCondition());
        $this->assertEquals($expectedSql, $resultSelect->assemble());
    }
}
