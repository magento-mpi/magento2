<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Builder\Query;

use Magento\Framework\Search\Adapter\Mysql\QueryInterface;
use Magento\Framework\Search\Adapter\Mysql\ScoreManager;

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
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\Search\Request\QueryInterface $query,
        $queryType
    ) {
        $this->scoreManager->setQueryBoost($query->getName(), $query->getBoost());

        /** @var $query \Magento\Framework\Search\Request\Query\Match */
        foreach ($query->getMatches() as $match) {
            $this->scoreManager->addCondition(
                $query->getName(),
                $select->getMatchQuery($match['field'], $match['value']),
                isset($match['boost']) ? $match['boost'] : 1
            );
            $select->match($match['field'], $match['value'], true);
        }

        return $select;
    }
}
