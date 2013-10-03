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
 * Exceptional situation of a performance testing scenario failure
 */
namespace Magento\TestFramework\Performance\Scenario;

class FailureException extends \Magento\Exception
{
    /**
     * @var \Magento\TestFramework\Performance\Scenario
     */
    protected $_scenario;

    /**
     * Constructor
     *
     * @param \Magento\TestFramework\Performance\Scenario $scenario
     * @param string $message
     */
    public function __construct(\Magento\TestFramework\Performance\Scenario $scenario, $message = '')
    {
        parent::__construct($message);
        $this->_scenario = $scenario;
    }

    /**
     * Retrieve scenario
     *
     * @return \Magento\TestFramework\Performance\Scenario
     */
    public function getScenario()
    {
        return $this->_scenario;
    }
}
