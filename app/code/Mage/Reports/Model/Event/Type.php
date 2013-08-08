<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event type model
 *
 * @method Mage_Reports_Model_Resource_Event_Type _getResource()
 * @method Mage_Reports_Model_Resource_Event_Type getResource()
 * @method string getEventName()
 * @method Mage_Reports_Model_Event_Type setEventName(string $value)
 * @method int getCustomerLogin()
 * @method Mage_Reports_Model_Event_Type setCustomerLogin(int $value)
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Event_Type extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Reports_Model_Resource_Event_Type');
    }
}
