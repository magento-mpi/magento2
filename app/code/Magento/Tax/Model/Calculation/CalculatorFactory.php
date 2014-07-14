<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

use \Magento\Customer\Service\V1\Data\Address;

class CalculatorFactory
{
    /**
     * Identifier constant for unit based calculation
     */
    const CALC_UNIT_BASE = 'UNIT_BASE_CALCULATION';

    /**
     * Identifier constant for row based calculation
     */
    const CALC_ROW_BASE = 'ROW_BASE_CALCULATION';

    /**
     * Identifier constant for total based calculation
     */
    const CALC_TOTAL_BASE = 'TOTAL_BASE_CALCULATION';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create new calculator
     *
     * @param string $type Type of calculator
     * @param int $storeId
     * @param Address $billingAddress
     * @param Address $shippingAddress
     * @param null|int $customerTaxClassId
     * @return \Magento\Tax\Model\Calculation\AbstractCalculator
     */
    public function create(
        $type,
        $storeId,
        Address $billingAddress = null,
        Address $shippingAddress = null,
        $customerTaxClassId = null
    ) {
        switch ($type) {
            case self::CALC_UNIT_BASE:
                $className = 'Magento\Tax\Model\Calculation\UnitBasedCalculator';
                break;
            case self::CALC_ROW_BASE:
                $className = 'Magento\Tax\Model\Calculation\RowBasedCalculator';
                break;
            case self::CALC_TOTAL_BASE:
                $className = 'Magento\Tax\Model\Calculation\TotalBasedCalculator';
                break;
            default:
                return null;
        }
        /** @var \Magento\Tax\Model\Calculation\AbstractCalculator $calculator */
        $calculator = $this->objectManager->create($className, ['storeId' => $storeId]);
        if (null != $shippingAddress) {
            $calculator->setShippingAddress($shippingAddress);
        }
        if (null != $billingAddress) {
            $calculator->setBillingAddress($billingAddress);
        }
        if (null != $customerTaxClassId) {
            $calculator->setCustomerTaxClassId($customerTaxClassId);
        }
        return $calculator;
    }
}
