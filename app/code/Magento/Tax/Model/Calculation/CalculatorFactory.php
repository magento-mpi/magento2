<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

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
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new calculator
     *
     * @param string $type Type of calculator
     * @param array $arguments
     * @return \Magento\Tax\Model\Calculation\AbstractBasedCalculator
     */
    public function create($type, array $arguments = array())
    {
        switch ($type) {
            case self::CALC_UNIT_BASE:
                return $this->_objectManager->create('Magento\Tax\Model\Calculation\UnitBasedCalculator', $arguments);
            case self::CALC_ROW_BASE:
                return $this->_objectManager->create('Magento\Tax\Model\Calculation\RowBasedCalculator', $arguments);
            case self::CALC_TOTAL_BASE:
                return $this->_objectManager->create('Magento\Tax\Model\Calculation\TotalBasedCalculator', $arguments);
            default:
                return null;
        }
    }
}
