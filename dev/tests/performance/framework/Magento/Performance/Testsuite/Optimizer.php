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
     * Compose array of scenario files, sorted according to scenario fixtures in such an order, that
     * number of Magento reinstalls among scenario executions is reduced.
     *
     * @param array $scenarios Map of scenario files to arrays of their fixtures
     * @return array
     */
    public function run(array $scenarios)
    {
        $result = array();
        $currentScenario = null;
        while ($scenarios) {
            $scenarioFile = $this->_selectNextScenario($currentScenario, $scenarios);
            $result[] = $scenarioFile;

            $currentScenario = $scenarios[$scenarioFile];
            unset($scenarios[$scenarioFile]);
        }

        return $result;
    }

    /**
     * Choose scenario, most suitable to be added next to queue.
     *
     * If $exemplarScenario is not null, then a try is made to choose compatible scenario (considering the set
     * of fixtures $exemplarScenario has). If a scenario is not chosen, or $exemplarScenario is not provided - then
     * just any scenario with minimal number of fixtures is chosen.
     *
     * @param array|null $exemplarScenario
     * @param array $scenarios
     * @return string
     */
    protected function _selectNextScenario($exemplarScenario, array $scenarios)
    {
        $result = null;
        if ($exemplarScenario) {
            $result = $this->_selectCompatibleScenario($exemplarScenario, $scenarios);
        }
        if (!$result) {
            $result = $this->_selectScenarioWithMinFixtures($scenarios);
        }
        return $result;
    }

    /**
     * Choose scenario, which contains same fixtures as $exemplarScenario + some additional fixtures.
     * Prefer the one with minimal number of additional fixtures.
     *
     * @param array $exemplarScenario
     * @param array $scenarios
     * @return string|null
     */
    protected function _selectCompatibleScenario($exemplarScenario, array $scenarios)
    {
        $chosenKey = null;
        $chosenNumFixtures = null;
        foreach ($scenarios as $key => $scenarioFixtures) {
            if (array_diff($exemplarScenario, $scenarioFixtures)) {
                continue; // Fixture lists are incompatible
            }

            $numFixtures = count($scenarioFixtures);
            if (($chosenKey === null) || ($chosenNumFixtures > $numFixtures)) {
                $chosenKey = $key;
                $chosenNumFixtures = $numFixtures;
            }
        }

        return $chosenKey;
    }

    /**
     * Choose a scenario with the minimal number of fixtures. Remove it from list of all scenarios.
     *
     * @param array $scenarios
     * @return string
     */
    protected function _selectScenarioWithMinFixtures(array $scenarios)
    {
        $chosenKey = key($scenarios);
        foreach ($scenarios as $key => $scenarioFixtures) {
            if (count($scenarios[$chosenKey]) > count($scenarioFixtures)) {
                $chosenKey = $key;
            }
        }

        return $chosenKey;
    }
}
