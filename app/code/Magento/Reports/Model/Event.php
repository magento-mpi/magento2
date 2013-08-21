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
 * Events model
 *
 * @method Magento_Reports_Model_Resource_Event _getResource()
 * @method Magento_Reports_Model_Resource_Event getResource()
 * @method string getLoggedAt()
 * @method Magento_Reports_Model_Event setLoggedAt(string $value)
 * @method int getEventTypeId()
 * @method Magento_Reports_Model_Event setEventTypeId(int $value)
 * @method int getObjectId()
 * @method Magento_Reports_Model_Event setObjectId(int $value)
 * @method int getSubjectId()
 * @method Magento_Reports_Model_Event setSubjectId(int $value)
 * @method int getSubtype()
 * @method Magento_Reports_Model_Event setSubtype(int $value)
 * @method int getStoreId()
 * @method Magento_Reports_Model_Event setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Event extends Magento_Core_Model_Abstract
{
    const EVENT_PRODUCT_VIEW    = 1;
    const EVENT_PRODUCT_SEND    = 2;
    const EVENT_PRODUCT_COMPARE = 3;
    const EVENT_PRODUCT_TO_CART = 4;
    const EVENT_PRODUCT_TO_WISHLIST = 5;
    const EVENT_WISHLIST_SHARE  = 6;

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Reports_Model_Resource_Event');
    }

    /**
     * Before Event save process
     *
     * @return Magento_Reports_Model_Event
     */
    protected function _beforeSave()
    {
        $this->setLoggedAt(Mage::getModel('Magento_Core_Model_Date')->gmtDate());
        return parent::_beforeSave();
    }

    /**
     * Update customer type after customer login
     *
     * @param int $visitorId
     * @param int $customerId
     * @param array $types
     * @return Magento_Reports_Model_Event
     */
    public function updateCustomerType($visitorId, $customerId, $types = null)
    {
        if (is_null($types)) {
            $types = array();
            foreach (Mage::getModel('Magento_Reports_Model_Event_Type')->getCollection() as $eventType) {
                if ($eventType->getCustomerLogin()) {
                    $types[$eventType->getId()] = $eventType->getId();
                }
            }
        }
        $this->getResource()->updateCustomerType($this, $visitorId, $customerId, $types);
        return $this;
    }

    /**
     * Clean events (visitors)
     *
     * @return Magento_Reports_Model_Event
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
