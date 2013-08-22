<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log items collection
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Model_Resource_Event_Changes_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Logging_Model_Event_Changes', 'Enterprise_Logging_Model_Resource_Event_Changes');
    }
}
