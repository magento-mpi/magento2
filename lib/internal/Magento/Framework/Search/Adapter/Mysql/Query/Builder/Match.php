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
     * {@inheritdoc}
     */
    public function build(
        ScoreBuilder $scoreBuilder,
        Select $select,
        RequestQueryInterface $query,
        $conditionType
    ) {
        /** @var $query \Magento\Framework\Search\Request\Query\Match */
        foreach ($query->getMatches() as $match) {
            $mode = Select::FULLTEXT_MODE_BOOLEAN;
            if ($conditionType === Bool::QUERY_CONDITION_MUST) {
                $match['value'] = '+' . $match['value'];
            } elseif ($conditionType === Bool::QUERY_CONDITION_NOT) {
                $match['value'] = '-' . $match['value'];
            }

            $scoreBuilder->addCondition(
                $select->getMatchQuery($match['field'], $match['value'], $mode),
                isset($match['boost']) ? $match['boost'] : 1
            );

            $select->match($match['field'], $match['value'], true, $mode);
        }
        return $select;
    }
}
