<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product alert for back in stock resource model
 */
class Magento_ProductAlert_Model_Resource_Stock extends Magento_ProductAlert_Model_Resource_Abstract
{
    /**
     * @var Magento_Core_Model_DateFactory
     */
    protected $_dateFactory;

    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_DateFactory $dateFactory
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_DateFactory $dateFactory
    ) {
        $this->_dateFactory = $dateFactory;
        parent::__construct($resource);
    }

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('product_alert_stock', 'alert_stock_id');
    }

    /**
     * Before save action
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (is_null($object->getId()) && $object->getCustomerId()
                && $object->getProductId() && $object->getWebsiteId()) {
            if ($row = $this->_getAlertRow($object)) {
                $object->addData($row);
                $object->setStatus(0);
            }
        }
        if (is_null($object->getAddDate())) {
            $object->setAddDate($this->_dateFactory->create()->gmtDate());
            $object->setStatus(0);
        }
        return parent::_beforeSave($object);
    }
}
