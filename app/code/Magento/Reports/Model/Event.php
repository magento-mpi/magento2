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
 * @method \Magento\Reports\Model\Resource\Event _getResource()
 * @method \Magento\Reports\Model\Resource\Event getResource()
 * @method string getLoggedAt()
 * @method \Magento\Reports\Model\Event setLoggedAt(string $value)
 * @method int getEventTypeId()
 * @method \Magento\Reports\Model\Event setEventTypeId(int $value)
 * @method int getObjectId()
 * @method \Magento\Reports\Model\Event setObjectId(int $value)
 * @method int getSubjectId()
 * @method \Magento\Reports\Model\Event setSubjectId(int $value)
 * @method int getSubtype()
 * @method \Magento\Reports\Model\Event setSubtype(int $value)
 * @method int getStoreId()
 * @method \Magento\Reports\Model\Event setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model;

class Event extends \Magento\Core\Model\AbstractModel
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
        $this->_init('\Magento\Reports\Model\Resource\Event');
    }

    /**
     * Before Event save process
     *
     * @return \Magento\Reports\Model\Event
     */
    protected function _beforeSave()
    {
        $this->setLoggedAt(\Mage::getModel('Magento\Core\Model\Date')->gmtDate());
        return parent::_beforeSave();
    }

    /**
     * Update customer type after customer login
     *
     * @param int $visitorId
     * @param int $customerId
     * @param array $types
     * @return \Magento\Reports\Model\Event
     */
    public function updateCustomerType($visitorId, $customerId, $types = null)
    {
        if (is_null($types)) {
            $types = array();
            foreach (\Mage::getModel('Magento\Reports\Model\Event\Type')->getCollection() as $eventType) {
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
     * @return \Magento\Reports\Model\Event
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
