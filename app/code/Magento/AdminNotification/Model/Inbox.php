<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * AdminNotification Inbox model
 *
 * @method Magento_AdminNotification_Model_Resource_Inbox _getResource()
 * @method Magento_AdminNotification_Model_Resource_Inbox getResource()
 * @method int getSeverity()
 * @method Magento_AdminNotification_Model_Inbox setSeverity(int $value)
 * @method string getDateAdded()
 * @method Magento_AdminNotification_Model_Inbox setDateAdded(string $value)
 * @method string getTitle()
 * @method Magento_AdminNotification_Model_Inbox setTitle(string $value)
 * @method string getDescription()
 * @method Magento_AdminNotification_Model_Inbox setDescription(string $value)
 * @method string getUrl()
 * @method Magento_AdminNotification_Model_Inbox setUrl(string $value)
 * @method int getIsRead()
 * @method Magento_AdminNotification_Model_Inbox setIsRead(int $value)
 * @method int getIsRemove()
 * @method Magento_AdminNotification_Model_Inbox setIsRemove(int $value)
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Inbox extends Magento_Core_Model_Abstract
{
    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;

    protected function _construct()
    {
        $this->_init('Magento_AdminNotification_Model_Resource_Inbox');
    }

    /**
     * Retrieve Severity collection array
     *
     * @return array|string
     */
    public function getSeverities($severity = null)
    {
        $severities = array(
            self::SEVERITY_CRITICAL => __('critical'),
            self::SEVERITY_MAJOR    => __('major'),
            self::SEVERITY_MINOR    => __('minor'),
            self::SEVERITY_NOTICE   => __('notice'),
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
     * @return Magento_AdminNotification_Model_Inbox
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
     * @return Magento_AdminNotification_Model_Inbox
     */
    public function parse(array $data)
    {
        return $this->getResource()->parse($this, $data);
    }

    /**
     * Add new message
     *
     * @param int $severity
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Magento_AdminNotification_Model_Inbox
     */
    public function add($severity, $title, $description, $url = '', $isInternal = true)
    {
        if (!$this->getSeverities($severity)) {
            Mage::throwException(__('Wrong message type'));
        }
        if (is_array($description)) {
            $description = '<ul><li>' . implode('</li><li>', $description) . '</li></ul>';
        }
        $date = date('Y-m-d H:i:s');
        $this->parse(array(array(
            'severity'    => $severity,
            'date_added'  => $date,
            'title'       => $title,
            'description' => $description,
            'url'         => $url,
            'internal'    => $isInternal
        )));
        return $this;
    }

    /**
     * Add critical severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Magento_AdminNotification_Model_Inbox
     */
    public function addCritical($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_CRITICAL, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add major severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Magento_AdminNotification_Model_Inbox
     */
    public function addMajor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MAJOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add minor severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Magento_AdminNotification_Model_Inbox
     */
    public function addMinor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MINOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add notice
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return Magento_AdminNotification_Model_Inbox
     */
    public function addNotice($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_NOTICE, $title, $description, $url, $isInternal);
        return $this;
    }
}
