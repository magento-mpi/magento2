<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Query\Builder;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface;
use Magento\Framework\Search\Adapter\Mysql\ScoreBuilder;
use Magento\Framework\Search\Request\Query\Bool;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

class Match implements QueryInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

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
        $queryValue = $query->getValue();
        if ($conditionType === Bool::QUERY_CONDITION_MUST) {
            $queryValue = '+' . $queryValue;
        } elseif ($conditionType === Bool::QUERY_CONDITION_NOT) {
            $queryValue = '-' . $queryValue;
        }
        
        $fieldList = [];
        foreach ($query->getMatches() as $match) {
            $fieldList[] = $match['field'];
        }
        $resolvedFieldList = $this->resolver->resolve($fieldList);

        $queryBoost = $query->getBoost();
        $scoreBuilder->addCondition(
            $select->getMatchQuery($resolvedFieldList, $queryValue, Select::FULLTEXT_MODE_BOOLEAN),
            !is_null($queryBoost) ? $queryBoost : 1
        );
        $select->match($resolvedFieldList, $queryValue, true, Select::FULLTEXT_MODE_BOOLEAN);

        return $select;
    }
}
