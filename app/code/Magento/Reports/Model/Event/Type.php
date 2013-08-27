<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event type model
 *
 * @method Magento_Reports_Model_Resource_Event_Type _getResource()
 * @method Magento_Reports_Model_Resource_Event_Type getResource()
 * @method string getEventName()
 * @method Magento_Reports_Model_Event_Type setEventName(string $value)
 * @method int getCustomerLogin()
 * @method Magento_Reports_Model_Event_Type setCustomerLogin(int $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Reports_Model_Event_Type extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Reports_Model_Resource_Event_Type');
    }
}
