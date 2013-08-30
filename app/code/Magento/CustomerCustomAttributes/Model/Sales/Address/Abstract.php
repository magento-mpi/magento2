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
abstract class Magento_CustomerCustomAttributes_Model_Sales_Address_Abstract extends Magento_CustomerCustomAttributes_Model_Sales_Abstract
{
    /**
     * Attach data to models
     *
     * @param array $entities
     * @return Magento_CustomerCustomAttributes_Model_Sales_Address_Abstract
     */
    public function attachDataToEntities(array $entities)
    {
        $this->_getResource()->attachDataToEntities($entities);
        return $this;
    }
}
