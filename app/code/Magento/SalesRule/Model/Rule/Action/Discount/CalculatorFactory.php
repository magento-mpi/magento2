<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Model\Rule\Action\Discount;

use Magento\ObjectManager\Factory;

class CalculatorFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    protected $classByType = array(
        \Magento\SalesRule\Model\Rule::TO_PERCENT_ACTION  => 'Magento\SalesRule\Model\Rule\Action\Discount',
        \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION  => 'Magento\SalesRule\Model\Rule\Action\Discount',
        \Magento\SalesRule\Model\Rule::TO_FIXED_ACTION    => 'Magento\SalesRule\Model\Rule\Action\Discount\ToFixed',
        \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION    => 'Magento\SalesRule\Model\Rule\Action\Discount',
        \Magento\SalesRule\Model\Rule::CART_FIXED_ACTION  => 'Magento\SalesRule\Model\Rule\Action\Discount',
        \Magento\SalesRule\Model\Rule::BUY_X_GET_Y_ACTION => 'Magento\SalesRule\Model\Rule\Action\Discount',
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        if (!isset($classByType[$type])) {
            throw new \InvalidArgumentException($type . ' is unknown type');
        }

        return $this->_objectManager->create($classByType[$type]);
    }
}
