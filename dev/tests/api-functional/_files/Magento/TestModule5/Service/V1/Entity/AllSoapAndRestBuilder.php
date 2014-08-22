<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule5\Service\V1\Entity;

use Magento\Framework\Service\Data\AbstractSimpleObjectBuilder;

/**
 * Some Data Object short description.
 *
 * Data Object long
 * multi line description.
 */
class AllSoapAndRestBuilder extends AbstractSimpleObjectBuilder
{
    const ID = 'id';
    const NAME = 'name';
    const ENABLED = 'enabled';
    const HAS_ORDERS = 'orders';

    /**
     * @param int $id
     * @return AllSoapAndRestBuilder
     */
    public function setId($id)
    {
        return $this->_set(self::ID, $id);
    }

    /**
     * @param string $name
     * @return AllSoapAndRestBuilder
     */
    public function setName($name)
    {
        return $this->_set(self::NAME, $name);
    }

    /**
     * Set flag if entity is enabled
     *
     * @param bool $isEnabled
     * @return AllSoapAndRestBuilder
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->_set(self::ENABLED, $isEnabled);
    }

    /**
     * Set flag if entity has orders
     *
     * @param bool $hasOrders
     * @return AllSoapAndRestBuilder
     */
    public function setHasOrders($hasOrders)
    {
        return $this->_set(self::HAS_ORDERS, $hasOrders);
    }
}
