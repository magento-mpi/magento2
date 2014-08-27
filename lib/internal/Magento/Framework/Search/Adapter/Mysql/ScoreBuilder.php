<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

/**
 * Class for generating sql condition for calculating store manager
 */
class ScoreBuilder
{
    /**
     * List of score conditions
     *
     * @var string[]
     */
    private $scoreQueryList = [];

    /**
     * Get column alias for global score query in sql
     *
     * @return string
     */
    public function getScoreAlias()
    {
        return 'global_score';
    }

    /**
     * Set boost for some query by queryName
     *
     * @param string $queryName
     * @param float $boost
     * @return $this
     */
    public function setQueryBoost($queryName, $boost = 1.0)
    {
        $this->scoreQueryList[$queryName]['boost'] = $boost;

        return $this;
    }

    /**
     * Add condition to score list
     *
     * @param string $queryName
     * @param string $match
     * @param float|null $boost
     * @return $this
     */
    public function addCondition($queryName, $match, $boost = 1.0)
    {
        if (!$this->hasQuery($queryName)) {
            $this->setQueryBoost($queryName);
        }

        $this->scoreQueryList[$queryName]['values'][] = ['value' => $match, 'boost' => $boost];

        return $this;
    }

    /**
     * Get generated sql condition for global score
     *
     * @return string
     */
    public function build()
    {
        $scoreCondition = $this->processQueries();
        if (!empty($scoreCondition)) {
            $scoreCondition = "({$scoreCondition}) AS " . $this->getScoreAlias();
        }
        $this->clear();

        return $scoreCondition;
    }

    /**
     * Check whether query exists in ScoreBuilder
     *
     * @param string $queryName
     * @return bool
     */
    private function hasQuery($queryName)
    {
        return isset($this->scoreQueryList[$queryName]);
    }

    /**
     * Clear score manager
     *
     * @return void
     */
    private function clear()
    {
        $this->scoreQueryList = [];
    }

    /**
     * Convert array of queries and conditions to string for sql
     *
     * @return string
     */
    private function processQueries()
    {
        $resultCondition = [];
        foreach ($this->scoreQueryList as $query) {
            $conditions = $this->processConditions($query['values']);
            $resultCondition[] = "({$conditions}) * {$query['boost']}";
        }
        return implode(' + ', $resultCondition);
    }

    /**
     * Convert array of conditions to condition string for sql
     *
     * @param array $conditions
     * @return string
     */
    private function processConditions($conditions)
    {
        $resultConditions = [];
        foreach ($conditions as $condition) {
            $resultConditions[] = "{$condition['value']} * {$condition['boost']}";
        }
        return implode(' + ', $resultConditions);
    }
}
