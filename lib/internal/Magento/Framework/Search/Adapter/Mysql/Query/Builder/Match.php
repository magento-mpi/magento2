<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Query\Builder;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\ScoreBuilder;
use Magento\Framework\Search\Request\Query\Bool;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

class Match implements QueryInterface
{
    /**
     * @var ScoreBuilder
     */
    private $scoreBuilder;

    /**
     * @param ScoreBuilder $scoreBuilder
     */
    public function __construct(ScoreBuilder $scoreBuilder)
    {
        $this->scoreBuilder = $scoreBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(
        Select $select,
        RequestQueryInterface $query,
        $conditionType
    ) {
        $this->scoreBuilder->setQueryBoost($query->getName(), $query->getBoost());

        /** @var $query \Magento\Framework\Search\Request\Query\Match */
        foreach ($query->getMatches() as $match) {
            $mode = Select::FULLTEXT_MODE_NATURAL_QUERY;
            if ($conditionType === Bool::QUERY_CONDITION_NOT) {
                $match['value'] = '-' . $match['value'];
                $mode = Select::FULLTEXT_MODE_BOOLEAN;
            }

            $this->scoreBuilder->addCondition(
                $query->getName(),
                $select->getMatchQuery($match['field'], $match['value']),
                isset($match['boost']) ? $match['boost'] : 1,
                $mode
            );
            $select->match($match['field'], $match['value'], true, $mode);
        }

        return $select;
    }
}
