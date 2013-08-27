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
 * Logging event changes model
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Logging_Model_Resource_Event_Changes extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_logging_event_changes', 'id');
    }
}
