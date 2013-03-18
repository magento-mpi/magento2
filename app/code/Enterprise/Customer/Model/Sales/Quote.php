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
 * Customer Quote model
 *
 * @method Enterprise_Customer_Model_Resource_Sales_Quote _getResource()
 * @method Enterprise_Customer_Model_Resource_Sales_Quote getResource()
 * @method Enterprise_Customer_Model_Sales_Quote setEntityId(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Model_Sales_Quote extends Enterprise_Customer_Model_Sales_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Customer_Model_Resource_Sales_Quote');
    }
}
