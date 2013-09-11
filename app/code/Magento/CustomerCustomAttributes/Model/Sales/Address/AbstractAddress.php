<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Address abstract model
 *
 */
namespace Magento\CustomerCustomAttributes\Model\Sales\Address;

abstract class AbstractAddress extends \Magento\CustomerCustomAttributes\Model\Sales\AbstractSales
{
    /**
     * Attach data to models
     *
     * @param array $entities
     * @return \Magento\CustomerCustomAttributes\Model\Sales\Address\AbstractAddress
     */
    public function attachDataToEntities(array $entities)
    {
        $this->_getResource()->attachDataToEntities($entities);
        return $this;
    }
}
