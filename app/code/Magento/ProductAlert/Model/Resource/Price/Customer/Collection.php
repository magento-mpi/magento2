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
 * ProductAlert Price Customer collection
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ProductAlert\Model\Resource\Price\Customer;

class Collection
    extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * join productalert price data to customer collection
     *
     * @param int $productId
     * @param int $websiteId
     * @return \Magento\ProductAlert\Model\Resource\Price\Customer\Collection
     */
    public function join($productId, $websiteId)
    {
        $this->getSelect()->join(
            array('alert' => $this->getTable('product_alert_price')),
            'e.entity_id=alert.customer_id',
            array('alert_price_id', 'price', 'add_date', 'last_send_date', 'send_count', 'status')
        );

        $this->getSelect()->where('alert.product_id=?', $productId);
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_price_id');
        $this->addAttributeToSelect('*');

        return $this;
    }
}
