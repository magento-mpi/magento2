<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * AdminNotification Inbox model
 *
 * @method Mage_AdminNotification_Model_Resource_Inbox _getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox getResource()
 * @method int getSeverity()
 * @method Mage_AdminNotification_Model_Inbox setSeverity(int $value)
 * @method string getDateAdded()
 * @method Mage_AdminNotification_Model_Inbox setDateAdded(string $value)
 * @method string getTitle()
 * @method Mage_AdminNotification_Model_Inbox setTitle(string $value)
 * @method string getDescription()
 * @method Mage_AdminNotification_Model_Inbox setDescription(string $value)
 * @method string getUrl()
 * @method Mage_AdminNotification_Model_Inbox setUrl(string $value)
 * @method int getIsRead()
 * @method Mage_AdminNotification_Model_Inbox setIsRead(int $value)
 * @method int getIsRemove()
 * @method Mage_AdminNotification_Model_Inbox setIsRemove(int $value)
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Inbox extends Mage_Core_Model_Abstract
{
    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;

    protected function _construct()
    {
        $this->_init('Mage_AdminNotification_Model_Resource_Inbox');
    }

    /**
     * Retrieve Severity collection array
     *
     * @return array|string
     */
    public function getSeverities($severity = null)
    {
        $severities = array(
            self::SEVERITY_CRITICAL => Mage::helper('Mage_AdminNotification_Helper_Data')->__('critical'),
            self::SEVERITY_MAJOR    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('major'),
            self::SEVERITY_MINOR    => Mage::helper('Mage_AdminNotification_Helper_Data')->__('minor'),
            self::SEVERITY_NOTICE   => Mage::helper('Mage_AdminNotification_Helper_Data')->__('notice'),
        );

        if (!is_null($severity)) {
            if (isset($severities[$severity])) {
                return $severities[$severity];
            }
            return null;
        }

        return $severities;
    }

    /**
     * Retrieve Latest Notice
     *
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function loadLatestNotice()
    {
        $this->setData(array());
        $this->getResource()->loadLatestNotice($this);
        return $this;
    }

    /**
     * Retrieve notice statuses
     *
     * @return array
     */
    public function getNoticeStatus()
    {
        return $this->getResource()->getNoticeStatus($this);
    }

    /**
     * Parse and save new data
     *
     * @param array $data
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function parse(array $data)
    {
        return $this->getResource()->parse($this, $data);
    }
}
