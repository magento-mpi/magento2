<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bootstrap of the memory monitoring
 */
class Magento_TestFramework_Bootstrap_Memory
{
    /**
     * Policy to perform requested actions on shutdown
     */
    const POLICY_SHUTDOWN = 'register_shutdown_function';

    /**
     * @var Magento_TestFramework_MemoryLimit
     */
    private $_memoryLimit;

    /**
     * @var callable
     */
    private $_activationPolicy;

    /**
     * @param Magento_TestFramework_MemoryLimit $memoryLimit
     * @param callable|string $activationPolicy
     * @throws InvalidArgumentException
     */
    public function __construct(Magento_TestFramework_MemoryLimit $memoryLimit, $activationPolicy = self::POLICY_SHUTDOWN)
    {
        if (!is_callable($activationPolicy)) {
            throw new InvalidArgumentException('Activation policy is expected to be a callable.');
        }
        $this->_memoryLimit = $memoryLimit;
        $this->_activationPolicy = $activationPolicy;
    }

    /**
     * Display memory usage statistics
     */
    public function displayStats()
    {
        echo $this->_memoryLimit->printHeader() . $this->_memoryLimit->printStats() . PHP_EOL;
    }

    /**
     * Activate displaying of the memory usage statistics
     */
    public function activateStatsDisplaying()
    {
        call_user_func($this->_activationPolicy, array($this, 'displayStats'));
    }

    /**
     * Activate validation of the memory usage/leak limitations
     */
    public function activateLimitValidation()
    {
        call_user_func($this->_activationPolicy, array($this->_memoryLimit, 'validateUsage'));
    }
}
