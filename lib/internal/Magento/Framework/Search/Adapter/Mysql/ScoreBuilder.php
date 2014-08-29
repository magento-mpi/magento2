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
     * @var string
     */
    private $scoreCondition = '';

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
     * Get generated sql condition for global score
     *
     * @return string
     */
    public function build()
    {
        $scoreCondition = $this->scoreCondition;
        $this->clear();
        $scoreAlias = $this->getScoreAlias();

        return "({$scoreCondition}) AS {$scoreAlias}";
    }

    /**
     * @return void
     */
    public function startQuery()
    {
        $this->addPlus();
        $this->scoreCondition .= '(';
    }

    public function endQuery($boost)
    {
        $this->scoreCondition .= ") * {$boost}";
    }

    /**
     * @param $score
     * @param $boost
     * @return void
     */
    public function addCondition($score, $boost)
    {
        $this->addPlus();
        $this->scoreCondition .= "{$score} * {$boost}";
    }

    private function addPlus()
    {
        if (!empty($this->scoreCondition) && substr($this->scoreCondition, -1) != '(') {
            $this->scoreCondition .= ' + ';
        }
    }

    /**
     * Clear score manager
     *
     * @return void
     */
    private function clear()
    {
        $this->scoreCondition = '';
    }
}
