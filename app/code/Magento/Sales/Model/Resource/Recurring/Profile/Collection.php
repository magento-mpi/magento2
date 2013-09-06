<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Recurring profile collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Recurring_Profile_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_recurring_profile_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'recurring_profile_collection';

    /**
     * Entity initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Recurring_Profile', 'Magento_Sales_Model_Resource_Recurring_Profile');
    }
}
