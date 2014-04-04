<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Amount;

/**
 * Class AmountFactory
 */
class AmountFactory
{
    /**
     * Default amount class
     */
    const DEFAULT_PRICE_AMOUNT_CLASS = 'Magento\Pricing\Amount\AmountInterface';

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
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
     * @return \Magento\Pricing\Amount\AmountInterface
     * @throws \InvalidArgumentException
     */
    public function create($amount, array $adjustmentAmounts = [])
    {
        $amountModel = $this->objectManager->create(
            self::DEFAULT_PRICE_AMOUNT_CLASS,
            [
                'amount' => $amount,
                'adjustmentAmounts' => $adjustmentAmounts
            ]
        );

        if (!$amountModel instanceof \Magento\Pricing\Amount\AmountInterface) {
            throw new \InvalidArgumentException(
                get_class($amountModel) . ' doesn\'t implement \Magento\Pricing\Amount\AmountInterface'
            );
        }

        return $amountModel;
    }
}
