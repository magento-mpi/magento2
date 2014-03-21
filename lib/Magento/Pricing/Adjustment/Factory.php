<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adjustment factory
 */
namespace Magento\Pricing\Adjustment;


class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create shared price adjustment
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\Pricing\Adjustment\AdjustmentInterface
     * @throws \InvalidArgumentException
     */
    public function create($className, array $arguments = [])
    {
        $price = $this->objectManager->create($className, $arguments);
        if (!$price instanceof \Magento\Pricing\Adjustment\AdjustmentInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Pricing\Adjustment\AdjustmentInterface'
            );
        }
        return $price;
    }
}
