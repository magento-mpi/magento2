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
     * Attach data to collection
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Enterprise_Customer_Model_Sales_Address_Abstract
     */
    public function attachDataToCollection(Varien_Data_Collection_Db $collection)
    {
        $this->_getResource()->attachDataToCollection($collection);
        return $this;
    }
}
