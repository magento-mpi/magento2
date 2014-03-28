<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Amount;

use Magento\Pricing\Object\SaleableInterface;

/**
 * Class AmountFactory
 */
class AmountFactory
{
    /**
     * Default Amount Class
     */
    const DEFAULT_AMOUNT_CLASS = '\Magento\Pricing\Amount\Base';

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
     * Create Amount object
     *
     * @param float $amount
     * @param array $adjustmentAmounts
     * @throws \InvalidArgumentException
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function create($amount, array $adjustmentAmounts = [])
    {
        $className = self::DEFAULT_AMOUNT_CLASS;

        $arguments['amount'] = $amount;
        $arguments['adjustmentAmounts'] = $adjustmentAmounts;
        $amountModel = $this->objectManager->create(self::DEFAULT_AMOUNT_CLASS, $arguments);
        if (!$amountModel instanceof \Magento\Pricing\Amount\AmountInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Pricing\Amount\AmountInterface'
            );
        }
        return $amountModel;
    }
}
