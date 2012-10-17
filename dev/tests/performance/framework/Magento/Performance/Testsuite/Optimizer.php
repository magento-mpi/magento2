<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Optimizer for scenario queue.
 * Reorders scenario list in order to minimize number of Magento reinstalls between scenario executions.
 */
class Magento_Performance_Testsuite_Optimizer
{
    /**
     * Sort scenarios, according to their fixtures in such an order, that number of Magento reinstalls
     * among future scenario executions is reduced. Return ids of scenarios in an optimized order.
     *
     * @param array $scenarioFixtures Map of scenario ids to their fixture arrays
     * @return array
     */
    public function optimizeScenarios(array $scenarioFixtures)
    {
        $result = array();
        $currentFixtures = null;
        while ($scenarioFixtures) {
            $scenarioId = $this->_selectNextScenario($currentFixtures, $scenarioFixtures);
            $result[] = $scenarioId;

            $currentFixtures = $scenarioFixtures[$scenarioId];
            unset($scenarioFixtures[$scenarioId]);
        }

        return $result;
    }

    /**
     * Choose scenario, most suitable to be added next to queue.
     *
     * If $fixtures is not null, then a try is made to choose scenario, compatible with it. If a scenario is not found,
     * or $fixtures is not provided - then just any entry with minimal number of fixtures is chosen.
     *
     * @param array|null $fixtures
     * @param array $scenarioFixtures
     * @return string
     */
    protected function _selectNextScenario($fixtures, array $scenarioFixtures)
    {
        $result = null;
        if ($fixtures) {
            $result = $this->_selectCompatibleScenario($fixtures, $scenarioFixtures);
        }
        if (!$result) {
            $result = $this->_selectScenarioWithMinFixtures($scenarioFixtures);
        }
        return $result;
    }

    /**
     * Find id of a scenario that contains same fixtures as $fixtures + some additional fixtures.
     * Prefer the scenario with minimal number of additional fixtures.
     *
     * @param array $fixtures
     * @param array $scenarioFixtures
     * @return string|null
     */
    protected function _selectCompatibleScenario(array $fixtures, array $scenarioFixtures)
    {
        $result = null;
        $chosenNumFixtures = null;
        foreach ($scenarioFixtures as $currentId => $currentFixtures) {
            if (array_diff($fixtures, $currentFixtures)) {
                continue; // Fixture lists are incompatible
            }

            $numFixtures = count($currentFixtures);
            if (($result === null) || ($chosenNumFixtures > $numFixtures)) {
                $result = $currentId;
                $chosenNumFixtures = $numFixtures;
            }
        }

        return $result;
    }

    /**
     * Find id of a scenario that has minimal number of fixtures.
     *
     * @param array $scenarioFixtures
     * @return string
     */
    protected function _selectScenarioWithMinFixtures(array $scenarioFixtures)
    {
        $result = key($scenarioFixtures);
        foreach ($scenarioFixtures as $currentId => $currentFixtures) {
            if (count($scenarioFixtures[$result]) > count($currentFixtures)) {
                $result = $currentId;
            }
        }

        return $result;
    }
}
