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
 * Interface for handling performance testing scenarios
 */
namespace Magento\TestFramework\Performance\Scenario;

interface HandlerInterface
{
    /**
     * Run scenario and optionally write results to report file
     *
     * @param \Magento\TestFramework\Performance\Scenario $scenario
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     */
    public function run(\Magento\TestFramework\Performance\Scenario $scenario, $reportFile = null);
}
