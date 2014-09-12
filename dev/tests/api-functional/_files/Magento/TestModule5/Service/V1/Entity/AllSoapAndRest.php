<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule5\Service\V1\Entity;

use Magento\Framework\Service\Data\AbstractSimpleObject;

/**
 * Some Data Object short description.
 *
 * Data Object long
 * multi line description.
 */
class AllSoapAndRest extends AbstractSimpleObject
{
    const ID = 'id';
    const NAME = 'name';
    const ENABLED = 'enabled';
    const HAS_ORDERS = 'orders';

    /**
     * Retrieve item ID.
     *
     * @return int Item ID
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Retrieve item Name.
     *
     * @return string|null Item name
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Check if entity is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_get(self::ENABLED);
    }

    /**
     * Check if current entity has a property defined
     *
     * @return bool
     */
    public function hasOrders()
    {
        return $this->_get(self::HAS_ORDERS);
    }

    /**
     * Method which will not be used when adding complex type field to WSDL.
     *
     * @param string $value
     * @return string
     */
    public function getFieldExcludedFromWsdl($value)
    {
        return $value;
    }
}
