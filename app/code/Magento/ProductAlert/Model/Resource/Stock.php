<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model\Resource;

/**
 * Product alert for back in stock resource model
 */
class Stock extends \Magento\ProductAlert\Model\Resource\AbstractResource
{
    /**
     * @var \Magento\Core\Model\DateFactory
     */
    protected $_dateFactory;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\DateFactory $dateFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Model\DateFactory $dateFactory
    ) {
        $this->_dateFactory = $dateFactory;
        parent::__construct($resource);
    }

    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('product_alert_stock', 'alert_stock_id');
    }

    /**
     * Before save action
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
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
