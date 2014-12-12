<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
