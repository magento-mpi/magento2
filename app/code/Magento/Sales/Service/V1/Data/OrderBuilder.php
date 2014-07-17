<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

/**
 * Class OrderBuilder
 */
class OrderBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    private $data;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     */
    public function __construct(
        \Magento\Framework\App\ObjectManager $objectManager,
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder
    ) {
        $this->objectManager = $objectManager;
        parent::__construct($objectFactory, $valueBuilder);
    }

    /**
     * Sets adjustment_negative
     *
     * @param string|null $value
     * @return $this
     */
    public function setAdjustmentNegative($value)
    {
        return $this->_set(Order::ADJUSTMENT_NEGATIVE, $value);
    }

    /**
     * Set adjustment_positive
     *
     * @param string|null $value
     * @return $this
     */
    public function setAdjustmentPositive($value)
    {
        return $this->_set(Order::ADJUSTMENT_POSITIVE, $value);
    }

    /**
     * Set applied_rule_ids
     *
     * @param string|null $value
     * @return $this
     */
    public function setAppliedRuleIds($value)
    {
        return $this->_set(Order::APPLIED_RULE_IDS, $value);
    }

    /**
     * Set base_adjustment_negative
     *
     * @param string|null $value
     * @return $this
     */
    public function setBaseAdjustmentNegative($value)
    {
        return $this->_set(Order::BASE_ADJUSTMENT_NEGATIVE, $value);
    }

    /**
     * Set entity_id
     *
     * @param string|null $value
     * @return $this
     */
    public function setEntityId($value)
    {
        return $this->_set(Order::ENTITY_ID, $value, $value);
    }

    public function create()
    {
        return $this->objectManager->create('Magento\Sales\Service\V1\Data\Order', ['data' => $this->data]);
    }
}