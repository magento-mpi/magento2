<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Statuses option array
 *
 * @category   Enterprise
 * @package    Enterprice_Reminder
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reminder_Model_Reminder_Options_Website implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * System Store Model
     *
     * @var Mage_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Mage_Core_Model_System_Store
     */
    public function __construct(Mage_Core_Model_System_Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return website array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getWebsiteOptionHash();
    }
}
