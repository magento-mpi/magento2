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
 * ProductAlert Stock Customer collection
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ProductAlert\Model\Resource\Stock\Customer;

class Collection
    extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * join productalert stock data to customer collection
     *
     * @param int $productId
     * @param int $websiteId
     * @return \Magento\ProductAlert\Model\Resource\Stock\Customer\Collection
     */
    public function join($productId, $websiteId)
    {
        $this->getSelect()->join(
            array('alert' => $this->getTable('product_alert_stock')),
            'alert.customer_id=e.entity_id',
            array('alert_stock_id', 'add_date', 'send_date', 'send_count', 'status')
        );

        $this->getSelect()->where('alert.product_id=?', $productId);
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_stock_id');
        $this->addAttributeToSelect('*');

        return $this;
    }
}
