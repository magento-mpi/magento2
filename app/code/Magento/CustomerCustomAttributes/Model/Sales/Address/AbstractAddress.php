<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Model\Sales\Address;

/**
 * Customer Address abstract model
 *
 */
abstract class AbstractAddress extends \Magento\CustomerCustomAttributes\Model\Sales\AbstractSales
{
    /**
     * Attach data to models
     *
     * @param \Magento\Framework\Object[] $entities
     * @return $this
     */
    public function attachDataToEntities(array $entities)
    {
        $this->_getResource()->attachDataToEntities($entities);
        return $this;
    }
}
