<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Price;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\ObjectManager;

class IntervalFactory
{
    /**
     * @var string
     */
    private $interval;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     * @param ScopeConfigInterface $scopeConfig
     * @param string $configPath
     * @param string[] $intervals
     * @param string $scope
     */
    public function __construct(
        ObjectManager $objectManager,
        ScopeConfigInterface $scopeConfig,
        $configPath,
        $intervals,
        $scope = ScopeInterface::SCOPE_DEFAULT
    ) {
        $this->objectManager = $objectManager;
        $configValue = $scopeConfig->getValue($configPath, $scope);
        if (isset($intervals[$configValue])) {
            $this->interval = $intervals[$configValue];
        } else {
            throw new \LogicException("Interval not found by config {$configValue}");
        }
    }

    /**
     * Create interval
     *
     * @return IntervalInterface
     */
    public function create()
    {
        return $this->objectManager->create($this->interval);
    }
}
