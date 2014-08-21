<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Builder\Query;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\QueryInterface;
use Magento\Framework\Search\Adapter\Mysql\ScoreManager;
use Magento\Framework\Search\Request\Query\Bool;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

class Match implements QueryInterface
{
    /**
     * @var ScoreManager
     */
    private $scoreManager;

    /**
     * @param ScoreManager $scoreManager
     */
    public function __construct(ScoreManager $scoreManager)
    {
        $this->scoreManager = $scoreManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildQuery(
        Select $select,
        RequestQueryInterface $query,
        $queryType
    ) {
        $this->scoreManager->setQueryBoost($query->getName(), $query->getBoost());

        /** @var $query \Magento\Framework\Search\Request\Query\Match */
        foreach ($query->getMatches() as $match) {
            if ($queryType === Bool::QUERY_TYPE_NOT) {
                $match['value'] = '-' . $match['value'];
            }

            $this->scoreManager->addCondition(
                $query->getName(),
                $select->getMatchQuery($match['field'], $match['value']),
                isset($match['boost']) ? $match['boost'] : 1,
                Select::FULLTEXT_MODE_BOOLEAN
            );
            $select->match($match['field'], $match['value'], true, Select::FULLTEXT_MODE_BOOLEAN);
        }

        return $select;
    }
}
