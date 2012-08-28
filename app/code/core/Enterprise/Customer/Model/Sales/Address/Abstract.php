<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Address abstract model
 *
 */
abstract class Enterprise_Customer_Model_Sales_Address_Abstract extends Enterprise_Customer_Model_Sales_Abstract
{
    /**
     * Attach data to models
     *
     * @param array $entities
     * @return Enterprise_Customer_Model_Sales_Address_Abstract
     */
    public function attachDataToEntities(array $entities)
    {
        $this->_getResource()->attachDataToEntities($entities);
        return $this;
    }
}
